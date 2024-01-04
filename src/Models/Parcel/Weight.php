<?php

namespace WebLivesInPost\Models\Parcel;

class Weight
{
    const UNIT_KG = 'kg';

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $unit;

    /**
     * Weight constructor.
     * @param float $amount
     * @param string $unit
     */
    public function __construct(float $amount, string $unit = self::UNIT_KG)
    {
        $this->amount = $amount;
        $this->unit = $unit;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }
}
