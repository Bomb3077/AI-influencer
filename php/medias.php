<?php

declare(strict_types=1);
//debuging
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Instagram\Api;
use Instagram\Exception\InstagramException;
use Psr\Cache\CacheException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require realpath(dirname(__FILE__)) . '/../vendor/autoload.php';

$cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');
$login = $_GET['login'] ?? null;
$password = $_GET['password'] ?? null;
$profile_username = $_GET['profile_username'] ?? null;

try {
    $api = new Api($cachePool);
    $api->login($login, $password);

    $profile = $api->getProfile($profile_username);

    printMedias($profile->getMedias());

    do {
        $profile = $api->getMoreMedias($profile);
        printMedias($profile->getMedias());

        // Or with profile id
        //$profile = $api->getMoreMediasWithProfileId(3504244670);
        //printMedias($profile->getMedias());

        // avoid 429 Rate limit from Instagram
        sleep(1);
    } while ($profile->hasMoreMedias());

} catch (InstagramException $e) {
    print_r($e->getMessage());
} catch (CacheException $e) {
    print_r($e->getMessage());
}

function printMedias(array $medias)
{
    foreach ($medias as $media) {
        echo 'ID        : ' . $media->getId() . "\n";
        echo 'Caption   : ' . $media->getCaption() . "\n";
        echo 'Link      : ' . $media->getLink() . "\n";
        echo 'Likes     : ' . $media->getLikes() . "\n";
        echo 'Date      : ' . $media->getDate()->format('Y-m-d h:i:s') . "\n\n";
    }
}
