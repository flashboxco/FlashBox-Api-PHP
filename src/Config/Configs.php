<?php

namespace FlashBox\Config;
use FlashBox\FlashBoxApiHandler;

class Configs
{
    const TOKEN = "PUT-YOUR-ACCESS-TOKEN-HERE";

    private $appConfig;

    public function __construct()
    {
        $this->setConfig();
    }

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | PACKAGE CONSTANTS
    |-------------------------------------------------------------------------------------------------------------------
    |
    | Don't edit following values
    |
    */
    const ENDPOINTS = [
        // 'sandbox'    => [
        //     'url'          => 'https://sandbox-api.flashbox.co/',
        //     'api_url'      => 'https://sandbox-api.flashbox.co/api/v2/',
        //     'tracking_url' => 'https://sandbox-tracking.flashbox.co/',
        // ],
        'production' => [
            'url'          => 'https://api.flashbox.co/',
            'api_url'      => 'https://api.flashbox.co/api/v2/',
            'tracking_url' => 'https://tracking.flashbox.co/',
        ],
        'custom'     => [
            'url'          => 'https://api-***.flashbox.co/',
            'api_url'      => 'https://api-***.flashbox.co/api/v2/',
            'tracking_url' => 'https://tracking-***.flashbox.co/',
        ],
    ];
    const ADDRESS_TYPES = [
        'origin',
        'destination',
    ];
    const TRANSPORT_TYPES = [
        'motorbike' => [
            'label' => 'Motorcycle',
            'delivery' => true
        ],
        'cargo' => [
            'label' => 'Van',
            'delivery' => true
        ],
        'cargo_s' => [
            'label' => 'Mini Van',
            'delivery' => true
        ],
        'car' => [
            'label' => 'Car',
            'delivery' => true
        ],
    ];

    /**
     * Set appConfig attribute
     */
    private function setConfig()
    {
        $this->appConfig = FlashBoxApiHandler::getAppConfig();
    }

    /**
     * Get appConfig attribute
     */
    public function getConfig()
    {
        return $this->appConfig;
    }
}

