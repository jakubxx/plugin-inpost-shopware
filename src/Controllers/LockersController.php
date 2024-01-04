<?php declare(strict_types=1);

namespace WebLivesInPost\Controllers;

use Shopware\Core\Checkout\Cart\CartPersister;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebLivesInPost\Util\Constants;

class LockersController extends StorefrontController
{
    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var CartPersister
     */
    private $cartPersister;

    /**
     * @var EntityRepositoryInterface
     */
    private $addressRepo;

    /**
     * @var AccountService
     */
    private $accountService;

    public function __construct(
        CartService $cartService,
        CartPersister $cartPersister,
        EntityRepositoryInterface $customerAddressRepo,
        AccountService $accountService
    ) {
        $this->cartService = $cartService;
        $this->cartPersister = $cartPersister;
        $this->addressRepo = $customerAddressRepo;
        $this->accountService = $accountService;
    }

    /**
     * @RouteScope(scopes={"sales-channel-api"})
     * @Route("/sales-channel-api/v{version}/inpost/select-locker", name="sales-channel-api.action.inpost.select-locker", methods={"POST"})
     * @param Request $request
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function selectLocker(Request $request, SalesChannelContext $context): JsonResponse
    {
        $locker = $request->request->get('locker');

        if (empty($locker)) {
            return new JsonResponse(['Bad Request'], 400);
        }

        $cart = $this->cartService->getCart($context->getToken(), $context);

        $deliveries = $cart->getDeliveries();
        $addresses = $deliveries->getAddresses();

        $address = $addresses->first();
        $address->setCustomFields([
            'identifier' => Constants::LOCKER_IDENTIFIER
        ]);
        $address->setStreet($locker['address']['line1']); // street + house number
        $address->setCity($locker['address_details']['city']);
        $address->setZipcode($locker['address_details']['post_code']);
        $address->setTitle('');
        $address->setAdditionalAddressLine1($locker['name']);
        $address->setAdditionalAddressLine2($locker['location_description']);

        $customerLocker = $this->getCustomerLockerAddress($context->getCustomer()->getId(), $context->getContext());

        if (!empty($customerLocker)) {
            // customer has already a locker address - update it

            $id = $customerLocker->getId();

            $this->addressRepo->update([
                [
                    'id' => $id,
                    'customFields' => [
                        'identifier' => Constants::LOCKER_IDENTIFIER
                    ],
                    'customerId' => $address->getCustomerId(),
                    'salutationId' => $address->getSalutationId(),
                    'firstName' => $address->getFirstName(),
                    'lastName' => $address->getLastName(),
                    'street' => $address->getStreet(),
                    'city' => $address->getCity(),
                    'countryId' => $address->getCountryId(),
                    'zipcode' => $address->getZipcode(),
                    'title' => $address->getTitle(),
                    'additionalAddressLine1' => $address->getAdditionalAddressLine1(),
                    'additionalAddressLine2' => $address->getAdditionalAddressLine2()
                ]
            ],
                $context->getContext()
            );
        } else {
            // customer does not have any locker addresses - add new one

            $saved = $this->addressRepo->create([
                [
                    'customFields' => [
                        'identifier' => Constants::LOCKER_IDENTIFIER
                    ],
                    'customerId' => $address->getCustomerId(),
                    'salutationId' => $address->getSalutationId(),
                    'firstName' => $address->getFirstName(),
                    'lastName' => $address->getLastName(),
                    'street' => $address->getStreet(),
                    'city' => $address->getCity(),
                    'countryId' => $address->getCountryId(),
                    'zipcode' => $address->getZipcode(),
                    'phoneNumber' => $address->getPhoneNumber(),
                    'title' => $address->getTitle(),
                    'additionalAddressLine1' => $address->getAdditionalAddressLine1(),
                    'additionalAddressLine2' => $address->getAdditionalAddressLine2()
                ]
            ], $context->getContext());

            $id = $saved->getPrimaryKeys('customer_address')[0];
        }

        $customer = $context->getCustomer();
        $customer->setDefaultShippingAddressId($id);

        $this->accountService->setDefaultShippingAddress($id, $context);

        $cart->setDeliveries($cart->getDeliveries());

        $this->cartPersister->save($cart, $context);

        $view = $this->render(
            '@WebLivesInPost/storefront/component/address/locker-address.html.twig',
            ['address' => $address]
        )->getContent();

        return new JsonResponse([
            'view' => $view
        ]);
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
