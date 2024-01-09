<?php

declare(strict_types=1);
//debuging
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Instagram\Api;
use Instagram\Exception\InstagramException;
use Instagram\Auth\Checkpoint\ImapClient;
use Psr\Cache\CacheException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

require realpath(dirname(__FILE__)) . '/../vendor/autoload.php';

$cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');

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

try {
    $api = new Api($cachePool);
    $imapClient = new ImapClient($credentials->getImapServer(), $credentials->getImapLogin(), $credentials->getImapPassword());
    $api->login($credentials->getLogin(), $credentials->getPassword(), $imapClient);

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

    header('Content-Type: application/json');
    echo json_encode($reponse, JSON_PRETTY_PRINT);

} catch (InstagramException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (CacheException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
