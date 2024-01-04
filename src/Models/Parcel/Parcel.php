<?php

namespace WebLivesInPost\Models\Parcel;

class Parcel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $template;

    /**
     * @var Dimensions
     */
    private $dimensions;

    /**
     * @var Weight
     */
    private $weight;

    /**
     * @var string
     */
    private $tracking_number;

    /**
     * @var bool
     */
    private $is_non_standard;

    /**
     * Parcel constructor.
     * @param Dimensions $dimensions
     * @param Weight $weight
     * @param string $tracking_number
     * @param bool $is_non_standard
     */
    public function __construct(
        Dimensions $dimensions,
        Weight $weight,
        string $tracking_number = null,
        bool $is_non_standard = false
    ) {
        // required
        $this->dimensions = $dimensions;
        $this->weight = $weight;

        // optional
        $this->tracking_number = $tracking_number;
        $this->is_non_standard = $is_non_standard;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return Dimensions
     */
    public function getDimensions(): Dimensions
    {
        return $this->dimensions;
    }

    /**
     * @param Dimensions $dimensions
     */
    public function setDimensions(Dimensions $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    /**
     * @return Weight
     */
    public function getWeight(): Weight
    {
        return $this->weight;
    }

    /**
     * @param Weight $weight
     */
    public function setWeight(Weight $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->tracking_number;
    }

    /**
     * @param string $tracking_number
     */
    public function setTrackingNumber(string $tracking_number): void
    {
        $this->tracking_number = $tracking_number;
    }

    /**
     * @return bool
     */
    public function isIsNonStandard(): bool
    {
        return $this->is_non_standard;
    }

    /**
     * @param bool $is_non_standard
     */
    public function setIsNonStandard(bool $is_non_standard): void
    {
        $this->is_non_standard = $is_non_standard;
    }
}
