<?php

declare(strict_types=1);
//debuging
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Instagram\Api;
use Instagram\Exception\InstagramException;
use Psr\Cache\CacheException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require realpath(dirname(__FILE__)) . '/vendor/autoload.php';

$cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');
// Fetch login and password from query parameters
$login = $_GET['login'] ?? null;
$password = $_GET['password'] ?? null;
// easier to test for now

try {
    $api = new Api($cachePool);
    $api->login($login, $password);

    $profile = $api->getProfile('robertdowneyjr');

    echo '============================' . "\n";
    echo 'User Information : ' . "\n";
    echo '============================' . "\n";
    echo 'ID               : ' . $profile->getId() . "\n";
    echo 'Full Name        : ' . $profile->getFullName() . "\n";
    echo 'UserName         : ' . $profile->getUserName() . "\n";
    echo 'Following        : ' . $profile->getFollowing() . "\n";
    echo 'Followers        : ' . $profile->getFollowers() . "\n";
    echo 'Biography        : ' . $profile->getBiography() . "\n";
    echo 'External Url     : ' . $profile->getExternalUrl() . "\n";
    echo 'Profile Picture  : ' . $profile->getProfilePicture() . "\n";
    echo 'Verified Account : ' . ($profile->isVerified() ? 'Yes' : 'No') . "\n";
    echo 'Private Account  : ' . ($profile->isPrivate() ? 'Yes' : 'No') . "\n";
    echo 'Medias Count     : ' . $profile->getMediaCount() . "\n";
    echo '============================' . "\n";

    // ... rest of the script to output profile information ...

} catch (InstagramException $e) {
    print_r($e->getMessage());
} catch (CacheException $e) {
    print_r($e->getMessage());
}
