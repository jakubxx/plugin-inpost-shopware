<?php

namespace WebLivesInPost\Models\Payload;

use WebLivesInPost\Models\Cost\Cod;
use WebLivesInPost\Models\Cost\Insurance;
use WebLivesInPost\Models\Receiver\Receiver;
use WebLivesInPost\Util\Constants;

class CourierPayload extends AbstractPayload
{
    /**
     * LockerPayload constructor.
     * @param Receiver $receiver
     * @param array $parcels
     * @param array $custom_attributes
     * @param Insurance $insurance
     * @param string $reference
     * @param Cod $cod
     */
    public function __construct(
        Receiver $receiver,
        array $parcels,
        string $reference,
        Insurance $insurance = null,
        Cod $cod = null,
        array $custom_attributes = null
    ) {
        $this->receiver = $receiver;
        $this->parcels = $parcels;
        $this->reference = $reference;
        $this->insurance = $insurance;
        $this->cod = $cod;
        $this->custom_attributes = $custom_attributes;

        $this->setService(Constants::SERVICE_COURIER_STANDARD);
    }
}
