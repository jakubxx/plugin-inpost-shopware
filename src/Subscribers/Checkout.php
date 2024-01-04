<?php declare(strict_types=1);

namespace WebLivesInPost\Subscribers;

use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextSwitchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WebLivesInPost\Util\Constants;

class Checkout implements EventSubscriberInterface
{
    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var EntityRepositoryInterface
     */
    private $addressRepo;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var EntityRepository
     */
    private $shippingRepo;

    /**
     * @var EntityRepository
     */
    private $customerRepo;

    public function __construct(
        AccountService $accountService,
        EntityRepositoryInterface $customerAddressRepo,
        CartService $cartService,
        EntityRepository $shippingRepository,
        EntityRepository $customerRepo
    ) {
        $this->accountService = $accountService;
        $this->addressRepo = $customerAddressRepo;
        $this->cartService = $cartService;
        $this->shippingRepo = $shippingRepository;
        $this->customerRepo = $customerRepo;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SalesChannelContextSwitchEvent::class => 'onShippingMethodChange',
            CustomerLogoutEvent::class => 'onLogout',
            CustomerLoginEvent::class => 'onLogin'
        ];
    }

    /**
     * Changes shipping address to the default one when customer changed the
     * @param SalesChannelContextSwitchEvent $event
     */
    public function onShippingMethodChange(SalesChannelContextSwitchEvent $event)
    {
        $context = $event->getSalesChannelContext();
        $data = $event->getRequestDataBag();

        $customer = $context->getCustomer();
        $cBillingAddress = $customer->getActiveBillingAddress();
        $cShippingAddress = $customer->getActiveShippingAddress();
        $csaIdentifier = $cShippingAddress->getCustomFields()[Constants::CUSTOM_FIELD_IDENTIFIER];

        $newShippingId = $data->get(SalesChannelContextService::SHIPPING_METHOD_ID);

        if (empty($newShippingId)) {
            return; // shipping method not changed
        }

        $newShipping = $this->getShipping($newShippingId, $context->getContext());
        $nsIdentifier = $newShipping->getCustomFields()[Constants::CUSTOM_FIELD_IDENTIFIER];

        /**
         * if new shipping method IS NOT locker
         * and customer address IS locker
         */
        if ($nsIdentifier !== Constants::LOCKER_IDENTIFIER
            && $csaIdentifier === Constants::LOCKER_IDENTIFIER) {

            $this->accountService->setDefaultShippingAddress($cBillingAddress->getId(), $context);

            return;
        }

        $customerLocker = $this->getCustomerLockerAddress($customer->getId(), $context->getContext());

        /**
         * if new shipping method IS locker
         * and customer HAS locker address
         */
        if ($nsIdentifier === Constants::LOCKER_IDENTIFIER
            && !empty($customerLocker)) {

            $this->accountService->setDefaultShippingAddress($customerLocker->getId(), $context);

            return;
        }
    }

    /**
     * Reset default shipping address on logout if locker is set
     * @param CustomerLogoutEvent $event
     */
    public function onLogout(CustomerLogoutEvent $event)
    {
        $customer = $event->getCustomer();
        $shippingAddress = $customer->getActiveShippingAddress();

        if (!empty($shippingAddress)) {
            $saIdentifier = $shippingAddress->getCustomFields()[Constants::CUSTOM_FIELD_IDENTIFIER];

            if ($saIdentifier === Constants::LOCKER_IDENTIFIER) {
                $billingAddressId = $customer->getDefaultBillingAddressId();

                $this->accountService->setDefaultShippingAddress($billingAddressId, $event->getSalesChannelContext());
            }
        }
    }

    /**
     * Reset default shipping address on login if locker is set
     * @param CustomerLoginEvent $event
     */
    public function onLogin(CustomerLoginEvent $event)
    {
        $customer = $event->getCustomer();
        $shippingAddress = $this->getCustomerAddress($customer->getDefaultShippingAddressId(),
            $event->getSalesChannelContext()->getContext());

        if (empty($shippingAddress)) {
            return; // address not found
        }

        $saIdentifier = $shippingAddress->getCustomFields()[Constants::CUSTOM_FIELD_IDENTIFIER];

        if ($saIdentifier === Constants::LOCKER_IDENTIFIER) {
            $billingAddressId = $customer->getDefaultBillingAddressId();

            $this->customerRepo->update([[
                'id' => $customer->getId(),
                'defaultShippingAddressId' => $billingAddressId
            ]], $event->getContext());

//            $this->accountService->setDefaultShippingAddress($billingAddressId, $event->getSalesChannelContext());
        }
    }

    /**
     * Search for shipping method by id
     * @param string $id
     * @param Context $context
     * @return ShippingMethodEntity|null
     */
    private function getShipping(string $id, Context $context): ?ShippingMethodEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('id', $id)
        );

        return $this->shippingRepo->search($criteria, $context)->first() ?: null;
    }

    /**
     * Search for customer address by id
     * @param string $id
     * @param Context $context
     * @return ShippingMethodEntity|null
     */
    private function getCustomerAddress(string $id, Context $context): ?CustomerAddressEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('id', $id)
        );

        return $this->addressRepo->search($criteria, $context)->first() ?: null;
    }

    /**
     * Search for locker address for given customer
     * @param string $customerId
     * @param Context $context
     * @return CustomerAddressEntity|null
     */
    private function getCustomerLockerAddress(string $customerId, Context $context): ?CustomerAddressEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customerId));
        $criteria->addFilter(
            new EqualsFilter(
                'customFields.' . Constants::CUSTOM_FIELD_IDENTIFIER,
                Constants::LOCKER_IDENTIFIER
            ));

        // assume customer can have only one locker address
        return $this->addressRepo->search($criteria, $context)->first();
    }
}
