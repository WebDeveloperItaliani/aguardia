<?php

namespace AGuardia\Service;


use GuzzleHttp\Client;

class FacebookServiceFactory
{
    const BASE_URI = 'https://graph.facebook.com/';
    const FB_API_VERSION = 'v2.7';

    public static function make($accessToken)
    {
        return new FacebookService(new Client([
            'base_uri' => self::BASE_URI . self::FB_API_VERSION
        ]), $accessToken);
    }
}
