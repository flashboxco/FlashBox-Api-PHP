<?php

namespace FlashBox\Model;

use FlashBox\FlashBoxApiHandler;

class Location
{
    /**
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public static function getAddress($latitude, $longitude)
    {
        return FlashBoxApiHandler::getAddress($latitude, $longitude);
    }

    /**
     * @param $locationName
     * @return mixed
     */
    public static function getSuggestions($locationName, $latlng)
    {
        return FlashBoxApiHandler::getLocationSuggestion($locationName, $latlng);
    }

}
