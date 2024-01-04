<?php declare(strict_types=1);

namespace WebLivesInPost\Services;

use WebLivesInPost\Models\Payload\ShipXResponse;
use WebLivesInPost\Util\Constants;
use WebLivesInPost\Util\CountHelper;

class ImportDeliveryService extends AbstractDeliveryService
{
    protected function name(): string
    {
        return 'ShipX import';
    }

    /**
     * @return bool
     */
    protected function isLockerEnabled(): bool
    {
        return $this->config->isLockerImportEnabled();
    }

    /**
     * @return bool
     */
    protected function isCourierEnabled(): bool
    {
        return $this->config->isCourierImportEnabled();
    }

    /**
     * @param string $shippingType
     * @return void
     */
    protected function handle(string $shippingType = '')
    {
        // init counter
        $this->counters[$shippingType] = new CountHelper();

        if ($shippingType === Constants::SERVICE_LOCKER_STANDARD) {
            $shippingMethodId = $this->shippingHelper->getLockerShippingId($this->context);
        } else {
            $shippingMethodId = $this->shippingHelper->getCourierShippingId($this->context);
        }

        $deliveries = $this->inPostDeliveryRepository->getDeliveriesForImport($shippingMethodId);

        if (!empty($deliveries)) {
            foreach ($deliveries as $delivery) {
                $response = $this->shipX->getShipment($delivery->getCustomFields()[Constants::CUSTOM_FIELD_SHIPMENT_ID]);

                if ($response->getStatusCode() === ShipXResponse::STATUS_OK) {
                    $parcels = $response->getBody()['parcels'];
                    $trackingCodes = [];

                    foreach ($parcels as $parcel) {
                        if (!empty($parcel['tracking_number'])) {
                            $trackingCodes[] = $parcel['tracking_number'];
                        }
                    }

                    if (!empty($trackingCodes)) {
                        $delivery->setTrackingCodes($trackingCodes);

                        $res = $this->deliveryRepository->update([
                            [
                                'id' => $delivery->getId(),
                                'trackingCodes' => $delivery->getTrackingCodes()
                            ]
                        ], $this->context);

                        if (empty($res->getErrors())) {
                            $this->counters[$shippingType]->addSuccess();
                        } else {
                            $this->counters[$shippingType]->addFailed();
                        }
                    }
                }
            }
        }
    }
}
