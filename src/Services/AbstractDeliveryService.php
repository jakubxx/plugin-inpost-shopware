<?php

namespace WebLivesInPost\Services;

use Monolog\Logger;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use WebLivesInPost\Repositories\InPostDeliveryRepository;
use WebLivesInPost\Util\Constants;
use WebLivesInPost\Util\CountHelper;
use WebLivesInPost\Util\ShippingHelper;

abstract class AbstractDeliveryService
{
    // Service extensions handle following services
    // short name => Constants::KEY
    public const SERVICES_MAP = [
        'lockers' => Constants::SERVICE_LOCKER_STANDARD,
        'couriers' => Constants::SERVICE_COURIER_STANDARD
    ];

    /**
     * @var EntityRepository
     */
    protected $deliveryRepository;

    /**
     * @var EntityRepository
     */
    protected $shippingRepository;

    /**
     * @var ConfigService
     */
    protected $config;

    /**
     * @var ShippingHelper
     */
    protected $shippingHelper;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ShipXApiConnector
     */
    protected $shipX;

    /**
     * @var InPostDeliveryRepository
     */
    protected $inPostDeliveryRepository;

    /**
     * @var CountHelper[]
     */
    protected $counters;

    /**
     * Summary of executed code
     * @var array
     */
    protected $result = [];

    public function __construct(
        Logger $logger,
        EntityRepository $deliveryRepository,
        EntityRepository $shippingRepository,
        ConfigService $configService,
        ShipXApiConnector $shipXApiConnector,
        InPostDeliveryRepository $inPostDeliveryRepository
    ) {
        $this->logger = $logger;
        $this->deliveryRepository = $deliveryRepository;
        $this->shippingRepository = $shippingRepository;
        $this->config = $configService;
        $this->shipX = $shipXApiConnector;
        $this->shippingHelper = new ShippingHelper($shippingRepository);
        $this->context = Context::createDefaultContext();
        $this->inPostDeliveryRepository = $inPostDeliveryRepository;
    }

    /**
     * Name used when building a result
     * @return string
     */
    abstract protected function name(): string;

    /**
     * Is locker action enabled in config?
     * @return bool
     */
    abstract protected function isLockerEnabled(): bool;

    /**
     * Is courier action enabled in config?
     * @return bool
     */
    abstract protected function isCourierEnabled(): bool;

    /**
     * Operations logic
     * @param string $shippingType
     * @return void
     */
    abstract protected function handle(string $shippingType);

    /**
     * Check configs and prepare base data
     * Builds result at the end
     * @return void
     */
    public function run()
    {
        $this->logStart();

        if ($this->isLockerEnabled()) {
            $this->handle(Constants::SERVICE_LOCKER_STANDARD);
        }

        if ($this->isCourierEnabled()) {
            $this->handle(Constants::SERVICE_COURIER_STANDARD);
        }

        $this->buildResult();
        $this->logFinish();
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return void
     */
    protected function logStart(): void
    {
        $this->logger->info($this->name() . ' started');
    }

    /**
     * @return void
     */
    protected function logFinish(): void
    {
        $this->logger->info(
            $this->getResult()['message'],
            $this->getResult()['details'] ?: ''
        );
    }

    /**
     * Build a summary array for logger/response
     * @return void
     */
    protected function buildResult(): void
    {
        $details = [];

        foreach (self::SERVICES_MAP as $key => $service) {
            if (isset($this->counters[$service])
                && $this->counters[$service] instanceof CountHelper) {
                $details[$key] = $this->counters[$service]->getCounts();
            } else {
                // counter not initialized = export disabled
                $details[$key] = 'Disabled in config';
            }
        }

        $this->result = [
            'message' => $this->name() . ' completed',
            'details' => $details
        ];
    }
}
