<?php declare(strict_types=1);

namespace WebLivesInPost\Installer;

use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\DeliveryTime\DeliveryTimeCollection;
use Swag\PayPal\SwagPayPal;
use WebLivesInPost\Util\Constants;
use WebLivesInPost\Util\ShippingHelper;

class InstallUninstall
{

    /**
     * @var Context
     */
    private $context;

    /**
     * @var EntityRepositoryInterface
     */
    private $shippingRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryTimeRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var ShippingHelper
     */
    private $shippingHelper;

    public function __construct(
        Context $context,
        EntityRepositoryInterface $shippingRepository,
        EntityRepositoryInterface $deliveryTimeRepository,
        EntityRepositoryInterface $ruleRepository
    )
    {
        $this->context = $context;
        $this->shippingRepository = $shippingRepository;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->ruleRepository = $ruleRepository;
        $this->shippingHelper = new ShippingHelper($shippingRepository);
    }

    public function install()
    {
        $this->registerShippingMethods();
    }

    public function uninstall()
    {
        $this->unregisterShippingMethods();
    }

    private function registerShippingMethods()
    {
        /* shipping method not exists */
        if ($this->shippingHelper->getLockerShippingId($this->context) === null) {
            $locker = [
                'type' => 0,
                'name' => Constants::LOCKER_NAME,
                'bindShippingfree' => false,
                'active' => false,
                'trackingUrl' => Constants::INPOST_TRACKING_URL_PL,
                'deliveryTimeId' => $this->getDeliveryTime(Constants::LOCKER_DELIVERY_TIME)->getId(),
                'availabilityRuleId' => $this->getAvailabilityRule(Constants::LOCKER_AVAILABILITY_RULE)->getId(),
                'prices' => [
                    [
                        'name' => 'Std',
                        'price' => '10.00',
                        'currencyId' => Defaults::CURRENCY,
                        'calculation' => 1,
                        'quantityStart' => 1,
                        'currencyPrice' => [
                            [
                                'currencyId' => Defaults::CURRENCY,
                                'net' => Constants::LOCKER_PRICE_NET,
                                'gross' => Constants::LOCKER_PRICE_GROSS,
                                'linked' => false,
                            ],
                        ],
                    ],
                ],
                'customFields' => [
                    'identifier' => Constants::LOCKER_IDENTIFIER,
                ],
            ];

            $this->shippingRepository->upsert([$locker], $this->context);
        }

        if ($this->shippingHelper->getCourierShippingId($this->context) === null) {
            $courier = [
                'type' => 0,
                'name' => Constants::COURIER_NAME,
                'bindShippingfree' => false,
                'active' => false,
                'trackingUrl' => Constants::INPOST_TRACKING_URL_PL,
                'deliveryTimeId' => $this->getDeliveryTime(Constants::COURIER_DELIVERY_TIME)->getId(),
                'availabilityRuleId' => $this->getAvailabilityRule(Constants::COURIER_AVAILABILITY_RULE)->getId(),
                'prices' => [
                    [
                        'name' => 'Std',
                        'price' => '10.00',
                        'currencyId' => Defaults::CURRENCY,
                        'calculation' => 1,
                        'quantityStart' => 1,
                        'currencyPrice' => [
                            [
                                'currencyId' => Defaults::CURRENCY,
                                'net' => Constants::COURIER_PRICE_NET,
                                'gross' => Constants::COURIER_PRICE_GROSS,
                                'linked' => false,
                            ],
                        ],
                    ],
                ],
                'customFields' => [
                    'identifier' => Constants::COURIER_IDENTIFIER,
                ],
            ];

            $this->shippingRepository->upsert([$courier], $this->context);
        }
    }

    private function unregisterShippingMethods()
    {
        $lockerId = $this->shippingHelper->getLockerShippingId($this->context);
        $courierId = $this->shippingHelper->getCourierShippingId($this->context);

        if ($lockerId !== null) {
            $this->shippingRepository->delete([['id' => $lockerId]], $this->context);
        }

        if ($courierId !== null) {
            $this->shippingRepository->delete([['id' => $courierId]], $this->context);
        }
    }

    private function getAvailabilityRule(string $name)
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new ContainsFilter('name', $name)
        );

        /** @var RuleCollection|null $deliveryTimes */
        $rules = $this->ruleRepository->search($criteria, $this->context);

        if (!empty($rules->getElements())) {
            return $rules->first();
        } else {
            return $this->ruleRepository->search(new Criteria(), $this->context)->first();
        }
    }

    private function getDeliveryTime(array $deliveryTime)
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('min', $deliveryTime['min']),
            new EqualsFilter('max', $deliveryTime['max']),
            new EqualsFilter('unit', $deliveryTime['unit'])
        );

        /** @var DeliveryTimeCollection|null $deliveryTimes */
        $deliveryTimes = $this->deliveryTimeRepository->search($criteria, $this->context);

        if (!empty($deliveryTimes->getElements())) {
            return $deliveryTimes->first();
        } else {
            return $this->deliveryTimeRepository->search(new Criteria(), $this->context)->first();
        }
    }

}
