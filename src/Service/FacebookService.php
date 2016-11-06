<?php

namespace AGuardia\Service;

use Carbon\Carbon;
use GuzzleHttp\Client;

class FacebookService
{
    /* @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getLatestPostsByFbGroupId($groupId, $timeInterval = 15)
    {
        $fifteenMinutesAgo = Carbon::now()->subMinutes($timeInterval)->toAtomString();

        $response = $this->client->request('GET', $groupId . '/feed', [
            'query' => [
                'since' => $fifteenMinutesAgo,
                'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
            ]
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if(isset($responseBody['error'])) {
            throw new \Exception('An error occurred while fetching latest posts.');
        }

        return $responseBody['data'];
    }

    public function getPostById($postId)
    {
        $response = $this->client->request('GET', $postId, [
            'query' => [
                'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
            ]
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        if(isset($responseBody['error'])) {
            throw new \Exception('An error occurred while fetching the post.');
        }

        return $responseBody;
    }

    public function deletePostById($postId)
    {
        $response = $this->client->request('DELETE', $postId, [
            'query' => [
                'access_token' => getenv('FACEBOOK_ACCESS_TOKEN')
            ]
        ]);

        $responseBody = json_decode($response->getBody()->getContents(), true);
        if(isset($responseBody['error'])) {
            throw new \Exception('An error occurred while deleting the post.');
        }
    }
}
