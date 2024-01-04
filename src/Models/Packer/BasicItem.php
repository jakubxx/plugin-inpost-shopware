<?php

namespace WebLivesInPost\Models\Packer;

use DVDoug\BoxPacker\Item;
use WebLivesInPost\Models\Parcel\Dimensions;
use WebLivesInPost\Models\Parcel\Weight;

class BasicItem implements Item
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $weight;

    /**
     * @var bool
     */
    private $keepFlat;

    /**
     * BasicItem constructor.
     * @param string $description
     * @param int $width
     * @param int $length
     * @param int $depth
     * @param float $weight
     */
    public function __construct(
        string $description,
        int $width,
        int $length,
        int $depth,
        float $weight
    ) {
        $this->description = $description;
        $this->width = $width;
        $this->length = $length;
        $this->depth = $depth;
        $this->weight = $weight * 1000; // in grams
        $this->keepFlat = true;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getKeepFlat(): bool
    {
        return $this->keepFlat;
    }

    public function getInPostDimensions(): Dimensions
    {
        return new Dimensions(
            $this->length,
            $this->width,
            $this->depth
        );
    }

    public function getInPostWeight(): Weight
    {
        return new Weight($this->weight);
    }
}
