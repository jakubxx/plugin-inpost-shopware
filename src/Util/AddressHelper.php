<?php

namespace WebLivesInPost\Util;

class AddressHelper
{
    /**
     * Example:
     * input: (string) "ul. Edwarda Józefa Abramowskiego 29A / 21"
     * output: (array) [0 => (string) "ul. Edwarda Józefa Abramowskiego", 1 => (string) "29A / 21"]
     * @param string $street
     * @return string[]
     */
    public static function splitStreetAndNumber(string $street): array
    {
        $buildingNumber = '';

        if (preg_match('/^([^\d]*[^\d\s]) *(\d.*)$/', $street, $result)) {
            $street = trim($result[1]);
            $buildingNumber = trim($result[2]);
        }

        return [$street, $buildingNumber];
    }
}
