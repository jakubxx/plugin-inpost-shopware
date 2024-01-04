<?php

namespace WebLivesInPost\Models\Packer;

use DVDoug\BoxPacker\Box;
use WebLivesInPost\Models\Parcel\Dimensions;
use WebLivesInPost\Models\Parcel\Weight;

class BasicBox implements Box
{
    /**
     * @var int
     */
    private $outerWidth;

    /**
     * @var int
     */
    private $outerLength;

    /**
     * @var int
     */
    private $outerDepth;

    /**
     * @var int
     */
    private $innerWidth;

    /**
     * @var int
     */
    private $innerLength;

    /**
     * @var int
     */
    private $innerDepth;

    /**
     * @var int
     */
    private $maxWeight;

    /**
     * BasicBox constructor.
     * @param int $width
     * @param int $length
     * @param int $depth
     * @param int $maxWeight
     */
    public function __construct(
        int $width,
        int $length,
        int $depth,
        int $maxWeight
    ) {
        $this->outerWidth = $this->innerWidth = $width;
        $this->outerLength = $this->innerLength = $length;
        $this->outerDepth = $this->innerDepth = $depth;
        $this->maxWeight = $maxWeight * 1000; // in grams
    }

    public function getReference(): string
    {
        return '';
    }

    public function getEmptyWeight(): int
    {
        return 0;
    }

    public function getOuterWidth(): int
    {
        return $this->outerWidth;
    }

    public function getOuterLength(): int
    {
        return $this->outerLength;
    }

    public function getOuterDepth(): int
    {
        return $this->outerDepth;
    }

    public function getInnerWidth(): int
    {
        return $this->innerWidth;
    }

    public function getInnerLength(): int
    {
        return $this->innerLength;
    }

    public function getInnerDepth(): int
    {
        return $this->innerDepth;
    }

    public function getMaxWeight(): int
    {
        return $this->maxWeight;
    }
}
