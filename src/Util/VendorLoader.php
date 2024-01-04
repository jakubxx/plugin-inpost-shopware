<?php

namespace WebLivesInPost\Util;


class VendorLoader
{
    const AUTOLOAD_PATCH = __DIR__ . '/../../vendor/autoload.php';

    public function load()
    {
        if (is_file(self::AUTOLOAD_PATCH)) {
            require_once self::AUTOLOAD_PATCH;
        }
    }
}
