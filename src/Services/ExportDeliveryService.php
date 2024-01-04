<?php declare(strict_types=1);

namespace WebLivesInPost\Services;

use Monolog\Logger;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use WebLivesInPost\Models\Cost\Cod;
use WebLivesInPost\Models\Cost\Insurance;
use WebLivesInPost\Models\Parcel\Parcel;
use WebLivesInPost\Models\Parcel\TemplateParcel;
use WebLivesInPost\Models\Payload\CourierPayload;
use WebLivesInPost\Models\Payload\LockerPayload;
use WebLivesInPost\Models\Payload\ShipXResponse;
use WebLivesInPost\Models\Receiver\Address;
use WebLivesInPost\Models\Receiver\Receiver;
use WebLivesInPost\Repositories\InPostDeliveryRepository;
use WebLivesInPost\Util\AddressHelper;
use WebLivesInPost\Util\Constants;
use WebLivesInPost\Util\CountHelper;

class ExportDeliveryService extends AbstractDeliveryService
{
    /**
     * @var PackerService
     */
    private $packerService;

    public function __construct(
        Logger $logger,
        EntityRepository $deliveryRepository,
        EntityRepository $shippingRepository,
        ConfigService $configService,
        ShipXApiConnector $shipXApiConnector,
        InPostDeliveryRepository $inPostDeliveryRepository,
        PackerService $packerService
    ) {
        $this->packerService = $packerService;

        parent::__construct(
            $logger,
            $deliveryRepository,
            $shippingRepository,
            $configService,
            $shipXApiConnector,
            $inPostDeliveryRepository);
    }

    protected function name(): string
    {
        return 'ShipX export';
    }

    /**
     * @return bool
     */
    protected function isLockerEnabled(): bool
    {
        return $this->config->isLockerExportEnabled();
    }

    /**
     * @return bool
     */
    protected function isCourierEnabled(): bool
    {
        return $this->config->isCourierExportEnabled();
    }

    /**
     * @param string $shippingType
     * @return void
     */
    protected function handle(string $shippingType)
    {
        // init counter
        $this->counters[$shippingType] = new CountHelper();

        if ($shippingType === Constants::SERVICE_LOCKER_STANDARD) {
            $shippingMethodId = $this->shippingHelper->getLockerShippingId($this->context);
        } else {
            $shippingMethodId = $this->shippingHelper->getCourierShippingId($this->context);
        }

        $deliveries = $this->inPostDeliveryRepository->getDeliveriesForExport($shippingMethodId);

        if (!empty($deliveries)) {
            foreach ($deliveries as $delivery) {
                if ($shippingType === Constants::SERVICE_LOCKER_STANDARD) {
                    $payload = $this->buildLockerPayload($delivery);
                } else {
                    $payload = $this->buildCourierPayload($delivery);
                }

                if (empty($payload)) {
                    $this->setShipmentId($delivery, Constants::CUSTOM_FIELD_SHIPMENT_ID_NONE);
                    continue; // skip the iteration
                }

                $response = $this->shipX->createShipment($payload);

                if ($response->getStatusCode() === ShipXResponse::STATUS_CREATED ||
                    $response->getStatusCode() === ShipXResponse::STATUS_OK) {

                    $this->setShipmentId($delivery, (string)$response->getBody()['id']);

                    $this->deliveryRepository->update([
                        [
                            'id' => $delivery->getId(),
                            'customFields' => $delivery->getCustomFields()
                        ]
                    ], $this->context);

                    $this->counters[$shippingType]->addSuccess();
                } else {
                    $this->counters[$shippingType]->addFailed();
                }
            }
        }
    }

    /**
     * Prepare data for create locker shipment ShipX API call
     * @param OrderDeliveryEntity $od
     * @return LockerPayload
     */
    private function buildLockerPayload(OrderDeliveryEntity $od): ?LockerPayload
    {
        $receiver = new Receiver(
            $od->getShippingOrderAddress()->getFirstName(),
            $od->getShippingOrderAddress()->getLastName(),
            $od->getOrder()->getOrderCustomer()->getEmail(),
            $od->getShippingOrderAddress()->getPhoneNumber() ?: ''
        );

        $insurance = new Insurance(
            $od->getOrder()->getPrice()->getTotalPrice(),
            $od->getOrder()->getCurrency()->getIsoCode()
        );

        $cod = null;
        $transactions = $od->getOrder()->getTransactions()->getElements();

        if (!empty($transactions)) {
            $codTotalPrice = 0;
            $addCod = false;

            foreach ($transactions as $transaction) {
                if ($transaction->getPaymentMethodId() === $this->config->getCodPaymentId()) {
                    $addCod = true;
                    $codTotalPrice += $transaction->getAmount()->getTotalPrice();
                }
            }

            if ($addCod) {
                $cod = new Cod(
                    $codTotalPrice,
                    $od->getOrder()->getCurrency()->getIsoCode()
                );
            }
        }

        $items = $od->getOrder()->getLineItems();

        $this->packerService->addItems($items);

        if (!$this->packerService->willFit(Constants::SERVICE_LOCKER_STANDARD)) {
            return null;
        }

        $dimensions = $this->packerService->getPackedBoxDimensions(); // TODO improve dimensions/weight calculation

        $weight = $this->packerService->getPackedBoxWeight();

        $parcels = [
            new Parcel(
                $dimensions,
                $weight
            )
        ];

        $customAttributes = [
            'target_point' => $od->getShippingOrderAddress()->getAdditionalAddressLine1()
        ];

        return new LockerPayload(
            $receiver,
            $parcels,
            $od->getOrder()->getOrderNumber(),
            $customAttributes,
            $insurance,
            $cod
        );
    }

    /**
     * Prepare data for create courier shipment ShipX API call
     * @param OrderDeliveryEntity $od
     * @return CourierPayload
     */
    private function buildCourierPayload(OrderDeliveryEntity $od): ?CourierPayload
    {
        $split = AddressHelper::splitStreetAndNumber($od->getShippingOrderAddress()->getStreet());

        $street = $split[0];
        $buildingNumber = $split[1];

        $address = new Address(
            $street,
            $buildingNumber ?: '1', // handle error when customer does not provide number
            $od->getShippingOrderAddress()->getCity(),
            $od->getShippingOrderAddress()->getZipcode(),
            $od->getShippingOrderAddress()->getCountry()->getIso()
        );

        $receiver = new Receiver(
            $od->getShippingOrderAddress()->getFirstName(),
            $od->getShippingOrderAddress()->getLastName(),
            $od->getOrder()->getOrderCustomer()->getEmail(),
            $od->getShippingOrderAddress()->getPhoneNumber(),
            $address
        );

        $insurance = new Insurance(
            $od->getOrder()->getPrice()->getTotalPrice(),
            $od->getOrder()->getCurrency()->getIsoCode()
        );

        $cod = null;
        $transactions = $od->getOrder()->getTransactions()->getElements();

        if (!empty($transactions)) {
            $codTotalPrice = 0;
            $addCod = false;

            foreach ($transactions as $transaction) {
                if ($transaction->getPaymentMethodId() === $this->config->getCodPaymentId()) {
                    $addCod = true;
                    $codTotalPrice += $transaction->getAmount()->getTotalPrice();
                }
            }

            if ($addCod) {
                $cod = new Cod(
                    $codTotalPrice,
                    $od->getOrder()->getCurrency()->getIsoCode()
                );
            }
        }

        $lineItems = $od->getOrder()->getLineItems();

        $this->packerService->addItems($lineItems);

        if (!$this->packerService->willFit(Constants::SERVICE_COURIER_STANDARD)) {
            return null;
        }

        $dimensions = $this->packerService->getPackedBoxDimensions(); // TODO improve dimensions/weight calculation

        $weight = $this->packerService->getPackedBoxWeight();

        $parcels = [
            new Parcel(
                $dimensions,
                $weight
            )
        ];

        $customAttributes = [
            //
        ];

        return new CourierPayload(
            $receiver,
            $parcels,
            $od->getOrder()->getOrderNumber(),
            $insurance,
            $cod,
            $customAttributes
        );
    }

    /**
     * @param OrderDeliveryEntity $delivery
     * @param string $shipmentId
     */
    private function setShipmentId(OrderDeliveryEntity $delivery, string $shipmentId)
    {
        $customFields = $delivery->getCustomFields();

        $customFields[Constants::CUSTOM_FIELD_SHIPMENT_ID] = $shipmentId;

        $delivery->setCustomFields($customFields);
    }
}
