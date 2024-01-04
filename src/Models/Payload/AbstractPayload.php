<?php

namespace WebLivesInPost\Models\Payload;

use WebLivesInPost\Models\Cost\Cod;
use WebLivesInPost\Models\Cost\Insurance;
use WebLivesInPost\Models\Parcel\Parcel;
use WebLivesInPost\Models\Receiver\Receiver;
use WebLivesInPost\Models\Traits\CanBeArrayTrait;

abstract class AbstractPayload
{
    use CanBeArrayTrait;

    /**
     * @var Receiver
     */
    protected $receiver;

    /**
     * @var Parcel[]
     */
    protected $parcels;

    /**
     * @var Insurance
     */
    protected $insurance;

    /**
     * @var Cod
     */
    protected $cod;

    /**
     * @var array
     */
    protected $custom_attributes;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $external_customer_id;

    /**
     * @return Receiver
     */
    public function getReceiver(): Receiver
    {
        return $this->receiver;
    }

    /**
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return Parcel[]
     */
    public function getParcels(): array
    {
        return $this->parcels;
    }

    /**
     * @param Parcel[] $parcels
     */
    public function setParcels(array $parcels): void
    {
        $this->parcels = $parcels;
    }

    /**
     * @return Insurance
     */
    public function getInsurance(): Insurance
    {
        return $this->insurance;
    }

    /**
     * @param Insurance $insurance
     */
    public function setInsurance(Insurance $insurance): void
    {
        $this->insurance = $insurance;
    }

    /**
     * @return Cod
     */
    public function getCod(): Cod
    {
        return $this->cod;
    }

    /**
     * @param Cod $cod
     */
    public function setCod(Cod $cod): void
    {
        $this->cod = $cod;
    }

    /**
     * @return array
     */
    public function getCustomAttributes(): array
    {
        return $this->custom_attributes;
    }

    /**
     * @param array $custom_attributes
     */
    public function setCustomAttributes(array $custom_attributes): void
    {
        $this->custom_attributes = $custom_attributes;
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService(string $service): void
    {
        $this->service = $service;
    }

    /**
     * Order number
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getExternalCustomerId(): string
    {
        return $this->external_customer_id;
    }

    /**
     * @param string $external_customer_id
     */
    public function setExternalCustomerId(string $external_customer_id): void
    {
        $this->external_customer_id = $external_customer_id;
    }
}
