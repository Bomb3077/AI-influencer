<?php

declare(strict_types=1);

use Instagram\Api;
use Instagram\Exception\InstagramException;
use Instagram\Model\Media;
use Instagram\Utils\MediaDownloadHelper;
use Psr\Cache\CacheException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require realpath(dirname(__FILE__)) . '/../vendor/autoload.php';

$credentials = include_once realpath(dirname(__FILE__)) . '/credentials.php';
$credentialsJson = isset($_GET['CREDENTIALS']) ? json_decode($_GET['CREDENTIALS'], true) : null;
if ($credentialsJson) {
    $credentials->setLogin($credentialsJson['login']);
    $credentials->setPassword($credentialsJson['password']);
    $credentials->setImapServer($credentialsJson['imapServer']);
    $credentials->setImapLogin($credentialsJson['imapLogin']);
    $credentials->setImapPassword($credentialsJson['imapPassword']);
}
$profile_username = $_GET['profile_username'] ?? null;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : null;

$cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');

try {
    $api = new Api($cachePool);
    $imapClient = new ImapClient($credentials->getImapServer(), $credentials->getImapLogin(), $credentials->getImapPassword());
    $api->login($credentials->getLogin(), $credentials->getPassword(), $imapClient);

    $profile = $api->getProfile($profile_username);

    downloadMedias($profile->getMedias());

} catch (InstagramException $e) {
    print_r($e->getMessage());
} catch (CacheException $e) {
    print_r($e->getMessage());
}

function downloadMedias(array $medias)
{
    /** @var Media $media */
    foreach ($medias as $media) {
        if ($media->isVideo()) {
            $fileName = MediaDownloadHelper::downloadMedia($media->getVideoUrl());
        } else {
            $fileName = MediaDownloadHelper::downloadMedia($media->getDisplaySrc());
        }
        echo 'Media downloaded as : ' . $fileName . PHP_EOL;
    }
}
