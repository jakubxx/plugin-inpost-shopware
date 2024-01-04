<?php

namespace WebLivesInPost\Services;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use WebLivesInPost\Util\VendorLoader;

class ConfigService
{
    // endpoints
    public const ENDPOINT_SHIPX_PROD = 'https://api-shipx-pl.easypack24.net/v1/';
    public const ENDPOINT_SHIPX_SANDBOX = 'https://sandbox-api-shipx-pl.easypack24.net/v1/';

    // config prefix
    public const CONFIG_PLUGIN_PREFIX = 'WebLivesInPost.config.';

    // config fields
    // ShipX
    public const CONFIG_SHIPX_PROD_TOKEN = 'prodShipXToken';
    public const CONFIG_SHIPX_PROD_ORG_ID = 'prodShipXOrgId';

    public const CONFIG_SHIPX_SANDBOX_ENABLED = 'sandboxShipXEnabled';
    public const CONFIG_SHIPX_SANDBOX_ORG_ID = 'sandboxShipXOrgId';
    public const CONFIG_SHIPX_SANDBOX_TOKEN = 'sandboxShipXToken';

    // imports/exports
    public const CONFIG_EXPORT_LOCKER_ENABLED = 'exportLockerEnabled';
    public const CONFIG_EXPORT_COURIER_ENABLED = 'exportCourierEnabled';
    public const CONFIG_IMPORT_LOCKER_ENABLED = 'importLockerEnabled';
    public const CONFIG_IMPORT_COURIER_ENABLED = 'importCourierEnabled';
    public const CONFIG_COD_PAYMENT_ID = 'codPaymentId';

    /**
     * @var SystemConfigService
     */
    private $scs;

    public function __construct(
        VendorLoader $vendorLoader,
        SystemConfigService $systemConfigService
    ) {
//        $vendorLoader->load();
        $this->scs = $systemConfigService;
    }

    public function isSandboxShipXEnabled()
    {
        return $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_SHIPX_SANDBOX_ENABLED);
    }

    public function getShipXEndpoint()
    {
        return $this->isSandboxShipXEnabled()
            ? self::ENDPOINT_SHIPX_SANDBOX
            : self::ENDPOINT_SHIPX_PROD;
    }

    public function getShipXOrgId()
    {
        return $this->isSandboxShipXEnabled()
            ? $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_SHIPX_SANDBOX_ORG_ID)
            : $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_SHIPX_PROD_ORG_ID);
    }

    public function getShipXToken()
    {
        return $this->isSandboxShipXEnabled()
            ? $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_SHIPX_SANDBOX_TOKEN)
            : $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_SHIPX_PROD_TOKEN);
    }

    public function isLockerExportEnabled()
    {
        return $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_EXPORT_LOCKER_ENABLED);
    }

    public function isCourierExportEnabled()
    {
        return $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_EXPORT_COURIER_ENABLED);
    }

    public function isLockerImportEnabled()
    {
        return $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_IMPORT_LOCKER_ENABLED);
    }

    public function isCourierImportEnabled()
    {
        return $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_IMPORT_COURIER_ENABLED);
    }

    public function getCodPaymentId()
    {
        return $this->scs->get(self::CONFIG_PLUGIN_PREFIX . self::CONFIG_COD_PAYMENT_ID);
    }
}
