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
// $credentials = include_once realpath(dirname(__FILE__)) . '/credentials.php';

// Fetch login and password from query parameters
$login = $_GET['login'] ?? null;
$password = $_GET['password'] ?? null;
$profile_username = $_GET['profile_username'] ?? null;
// easier to test for now

try {
    $api = new Api($cachePool);
    $api->login($login, $password);

    $profile = $api->getProfile($profile_username);

    $reponse = array();
    $reponse['ID'] = $profile->getId();
    $reponse['FullName'] = $profile->getFullName();
    $reponse['Username'] = $profile->getUserName();
    $reponse['Following'] = $profile->getFollowing();
    $reponse['Follwers'] = $profile->getFollowers();
    $reponse['Biography'] = $profile->getBiography();
    $reponse['ExternalUrl'] = $profile->getExternalUrl();
    $reponse['ProfilePicture'] = $profile->getProfilePicture();
    $reponse['VerifiedAccount'] = ($profile->isVerified() ? 'Yes' : 'No');
    $reponse['PrivateAccount'] = ($profile->isPrivate() ? 'Yes' : 'No');
    $reponse['MediaCount'] = $profile->getMediaCount();
    echo json_encode($reponse, JSON_PRETTY_PRINT);

} catch (InstagramException $e) {
    print_r($e->getMessage());
} catch (CacheException $e) {
    print_r($e->getMessage());
}
