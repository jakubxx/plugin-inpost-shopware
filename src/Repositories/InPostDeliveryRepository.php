<?php

namespace WebLivesInPost\Repositories;

use Monolog\Logger;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryStates;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use WebLivesInPost\Services\ConfigService;
use WebLivesInPost\Util\Constants;

class InPostDeliveryRepository
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EntityRepository
     */
    private $deliveryRepository;

    /**
     * @var ConfigService
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * OrderDeliveryRepository constructor.
     * @param Logger $logger
     * @param EntityRepository $deliveryRepository
     * @param ConfigService $configService
     */
    public function __construct(
        Logger $logger,
        EntityRepository $deliveryRepository,
        ConfigService $configService
    ) {
        $this->logger = $logger;
        $this->deliveryRepository = $deliveryRepository;
        $this->config = $configService;
        $this->context = Context::createDefaultContext();
    }

    /**
     * @param string $shippingMethodId
     * @return array
     */
    public function getDeliveriesForImport(string $shippingMethodId)
    {
        $criteria = new Criteria();

        $criteria->addAssociation('order'); // attach order to every delivery

        // get only deliveries with lockers shipping method
        $criteria->addFilter(new EqualsFilter('shippingMethodId', $shippingMethodId));
        // get deliveries only for paid or cod orders
        $criteria->addFilter(new MultiFilter(
            MultiFilter::CONNECTION_OR,
            [
                new EqualsFilter('order.transactions.paymentMethodId', $this->config->getCodPaymentId()),
                new EqualsFilter('order.transactions.stateMachineState.technicalName',
                    OrderTransactionStates::STATE_PAID)
            ]
        ));
        // get only opened orders
        $criteria->addFilter(new EqualsFilter('order_delivery.order.stateMachineState.technicalName',
            OrderStates::STATE_OPEN));
        // and those that have the shipment created in ShipX
        $criteria->addFilter(new NotFilter(
            MultiFilter::CONNECTION_AND,
            [
                new EqualsFilter('customFields.' . Constants::CUSTOM_FIELD_SHIPMENT_ID, null)
            ]
        ));

        /** @var OrderDeliveryEntity[] $deliveries */
        $deliveries = $this->deliveryRepository->search($criteria, $this->context);

        return $deliveries;
    }

    /**
     * @param string $shippingMethodId
     * @return array
     */
    public function getDeliveriesForExport(string $shippingMethodId)
    {
        $criteria = new Criteria();

        $criteria->addAssociation('order'); // attach order to every delivery
        $criteria->addAssociation('order.currency'); // attach currency to every order
        $criteria->addAssociation('order.lineItems'); // attach ordered items to every order
        $criteria->addAssociation('shippingOrderAddress.country'); // attach country for shipping address
        $criteria->addAssociation('order.transactions'); // attach transactions to order
        $criteria->addAssociation('order.lineItems.product'); // attach product to every orderLineItem

        // get deliveries only for locker shipping method
        $criteria->addFilter(new EqualsFilter('shippingMethodId', $shippingMethodId));
        // get deliveries only for paid or cod orders
        $criteria->addFilter(new MultiFilter(
            MultiFilter::CONNECTION_OR,
            [
                new EqualsFilter('order.transactions.paymentMethodId', $this->config->getCodPaymentId()),
                new EqualsFilter('order.transactions.stateMachineState.technicalName',
                    OrderTransactionStates::STATE_PAID)
            ]
        ));
        // get only deliveries without shipment created on the InPost side
//        $criteria->addFilter(new EqualsFilter('customFields.' . Constants::CUSTOM_FIELD_SHIPMENT_ID, null));
        // get only open orders
        $criteria->addFilter(new EqualsFilter('order.stateMachineState.technicalName', OrderStates::STATE_OPEN));
        // get not shipped orders
        $criteria->addFilter(new NotFilter(
            MultiFilter::CONNECTION_AND,
            [new EqualsFilter('stateMachineState.technicalName', OrderDeliveryStates::STATE_SHIPPED)]
        ));

        /** @var OrderDeliveryEntity[] $deliveries */
        $deliveries = $this->deliveryRepository->search($criteria, $this->context);

        return $deliveries;
    }
}
