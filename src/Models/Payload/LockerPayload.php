<?php

namespace WebLivesInPost\Models\Payload;

use WebLivesInPost\Models\Cost\Cod;
use WebLivesInPost\Models\Cost\Insurance;
use WebLivesInPost\Models\Receiver\Receiver;
use WebLivesInPost\Util\Constants;

class LockerPayload extends AbstractPayload
{
    /**
     * LockerPayload constructor.
     * @param Receiver $receiver
     * @param array $parcels
     * @param string $reference
     * @param array $custom_attributes
     * @param Insurance $insurance
     * @param Cod|null $cod
     */
    public function __construct(
        Receiver $receiver,
        array $parcels,
        string $reference,
        array $custom_attributes,
        Insurance $insurance = null,
        Cod $cod = null
    ) {
        $this->receiver = $receiver;
        $this->parcels = $parcels;
        $this->reference = $reference;
        $this->custom_attributes = $custom_attributes;
        $this->insurance = $insurance;
        $this->cod = $cod;

        $this->setService(Constants::SERVICE_LOCKER_STANDARD);
    }
}
