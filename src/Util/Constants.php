<?php declare(strict_types=1);

namespace WebLivesInPost\Util;

class Constants
{
    // deliveries
    public const LOCKER_IDENTIFIER = 'weblives_inpost_lockers';
    public const LOCKER_NAME = 'InPost Paczkomaty';
    public const LOCKER_DELIVERY_TIME = [
        'min' => 1,
        'max' => 3,
        'unit' => 'day'
    ];
    public const LOCKER_AVAILABILITY_RULE = 'All customers';
    public const LOCKER_PRICE_NET = '8.13';
    public const LOCKER_PRICE_GROSS = '10.00';

    public const COURIER_IDENTIFIER = 'weblives_inpost_courier';
    public const COURIER_NAME = 'InPost Kurier';
    public const COURIER_DELIVERY_TIME = [
        'min' => 1,
        'max' => 3,
        'unit' => 'day'
    ];
    public const COURIER_AVAILABILITY_RULE = 'Klienci';
    public const COURIER_PRICE_NET = '12.19';
    public const COURIER_PRICE_GROSS = '15.00';

    public const INPOST_TRACKING_URL_PL = 'https://inpost.pl/sledzenie-przesylek?number=%s';

    // InPost services
    public const SERVICE_LOCKER_STANDARD = 'inpost_locker_standard';
    public const SERVICE_COURIER_STANDARD = 'inpost_courier_standard';

    // custom fields
    public const CUSTOM_FIELD_IDENTIFIER = 'identifier';
    public const CUSTOM_FIELD_SHIPMENT_ID = 'weblives_inpost_shipment_id';
    public const CUSTOM_FIELD_SHIPMENT_ID_NONE = 'none';

    // dimensions
    public const LOCKER_MAX_WIDTH_MM = 410;
    public const LOCKER_MAX_LENGTH_MM = 380;
    public const LOCKER_MAX_HEIGHT_MM = 640;
    public const LOCKER_MAX_WEIGHT_KG = 25;

    public const COURIER_MAX_WIDTH_MM = 3500;
    public const COURIER_MAX_LENGTH_MM = 2400;
    public const COURIER_MAX_HEIGHT_MM = 2400;
    public const COURIER_MAX_WEIGHT_KG = 50;
}
