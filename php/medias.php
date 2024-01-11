<?php

declare(strict_types=1);
//debuging
error_reporting(E_ALL);
ini_set('display_errors', '1');

use Instagram\Api;
use Instagram\Exception\InstagramException;
use Instagram\Auth\Checkpoint\ImapClient;
use Instagram\Utils\MediaDownloadHelper;
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
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : null;

try {
    $api = new Api($cachePool);
    $imapClient = new ImapClient($credentials->getImapServer(), $credentials->getImapLogin(), $credentials->getImapPassword());
    $api->login($credentials->getLogin(), $credentials->getPassword(), $imapClient);

    $profile = $api->getProfile($profile_username);
    $mediaData = collectMedias($profile->getMedias(), $limit, $api);

    if ($limit !== null && $limit < count($mediaData)) {
        $mediaData = array_slice($mediaData, 0, $limit);
    } else {
        do {
            $profile = $api->getMoreMedias($profile);
            $additionalMedias = collectMedias($profile->getMedias(), $limit ? $limit - count($mediaData) : null, $api);
            $mediaData = array_merge($mediaData, $additionalMedias);

            if ($limit !== null && count($mediaData) >= $limit) {
                $mediaData = array_slice($mediaData, 0, $limit);
                break;
            }

            // avoid 429 Rate limit from Instagram
            sleep(1);
        } while ($profile->hasMoreMedias());
    }
    $resposne = array();
    $response['Data'] = $mediaData;
    $response['UserID'] = $profile->getId();

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (InstagramException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (CacheException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function collectMedias(array $medias, ?int $limit = null, Api $api)
{
    $data = [];
    foreach ($medias as $media) {
        $mediaDetailed = $api->getMediaDetailedByShortCode($media);
        if ($limit !== null && count($data) >= $limit) {
            break;
        }
        $data[] = [
            'PostID' => $media->getId(),
            'ShortCode' => $media->getShortCode(),
            'Caption' => $media->getCaption(),
            'Link' => $media->getLink(),
            'Likes' => $media->getLikes(),
            'Date' => $media->getDate()->format('Y-m-d h:i:s'),
            'DisplaySrc' => $media->getDisplaySrc(),
            'TypeName'=>$media->getTypeName(),
            'isVideo'=>$media->isVideo()
        ];
    }
    return $data;
}

function formalize(string $str){
    return '"' . $str . '"';
}
function collectImagesId(array $mediaDetails){
    $data = [];
    foreach($mediaDetails as $mediaDetailed){
        $data[] = $mediaDetailed->getid();
    }
    return $data;
}


