<?php

namespace WebLivesInPost\Services;

use DVDoug\BoxPacker\ItemList;
use DVDoug\BoxPacker\PackedBox;
use DVDoug\BoxPacker\VolumePacker;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemCollection;
use Shopware\Core\Framework\Struct\Collection;
use WebLivesInPost\Models\Packer\BasicBox;
use WebLivesInPost\Models\Packer\BasicItem;
use WebLivesInPost\Models\Parcel\Dimensions;
use WebLivesInPost\Models\Parcel\Weight;
use WebLivesInPost\Util\Constants;

class PackerService
{
    const COURIER_SIZES = [
        ['w' => 300, 'l' => 200, 'h' => 200],
        ['w' => 600, 'l' => 300, 'h' => 300],
        ['w' => 900, 'l' => 600, 'h' => 600],
        ['w' => 1800, 'l' => 1200, 'h' => 1200],
        ['w' => 2700, 'l' => 1800, 'h' => 1800],
        ['w' => 3500, 'l' => 2400, 'h' => 2400]
    ];

    /**
     * @var Collection
     */
    private $items;

    /**
     * @var PackedBox
     */
    private $packedBox;

    /**
     * @var int
     */
    private $cartProducts = 0;

    public function __construct()
    {
        //
    }

    public function addItems(Collection $items)
    {
        if ($items instanceof OrderLineItemCollection || $items instanceof LineItemCollection) {
            $this->items = $items;
        }
    }

    /**
     * Check if the items will fit the given package
     * @param string $inPostService
     * @return bool
     */
    public function willFit(string $inPostService)
    {
        $width = $length = $height = $weight = 0;

        if (empty($this->items)) {
            return false;
        }

        if ($inPostService === Constants::SERVICE_LOCKER_STANDARD) {
            $width = Constants::LOCKER_MAX_WIDTH_MM;
            $length = Constants::LOCKER_MAX_LENGTH_MM;
            $height = Constants::LOCKER_MAX_HEIGHT_MM;
            $weight = Constants::LOCKER_MAX_WEIGHT_KG; // in grams

            return $this->check($width, $length, $height, $weight);
        } elseif ($inPostService === Constants::SERVICE_COURIER_STANDARD) {
            $weight = Constants::COURIER_MAX_WEIGHT_KG; // in grams
            $return = false;

            foreach (self::COURIER_SIZES as $size) {
                if ($this->check($size['w'], $size['l'], $size['h'], $weight)) {
                    $return = true;
                    break;
                }
            }
        } else {
            $return = false;
        }

        return $return;
    }

    private function check($width, $length, $height, $weight): bool
    {
        $box = new BasicBox(
            $width,
            $length,
            $height,
            $weight);

        $itemList = new ItemList();

        if ($this->items instanceof OrderLineItemCollection) {
            foreach ($this->items as $item) {
                if (empty($item->getProduct())) {
                    continue;
                }

                for ($i = 0; $i < $item->getQuantity(); $i++) {
                    $this->cartProducts++;

                    $itemList->insert(new BasicItem(
                        $item->getProduct()->getProductNumber(),
                        $item->getProduct()->getWidth() ?: 0,
                        $item->getProduct()->getLength() ?: 0,
                        $item->getProduct()->getHeight() ?: 0,
                        $item->getProduct()->getWeight() ?: 0
                    ));
                }
            }
        } elseif ($this->items instanceof LineItemCollection) {
            foreach ($this->items as $item) {
                if (!$item->isGood() || empty($item->getDeliveryInformation())) {
                    continue;
                }

                for ($i = 0; $i < $item->getQuantity(); $i++) {
                    $this->cartProducts++;

                    $itemList->insert(new BasicItem(
                        $item->getReferencedId(),
                        $item->getDeliveryInformation()->getWidth() ?: 0,
                        $item->getDeliveryInformation()->getLength() ?: 0,
                        $item->getDeliveryInformation()->getHeight() ?: 0,
                        $item->getDeliveryInformation()->getWeight() ?: 0
                    ));
                }
            }
        }

        $packer = new VolumePacker($box, $itemList);
        $this->packedBox = $packer->pack();

        if ($this->packedBox->getItems()->count() === $this->cartProducts) {
            $return = true;
        } else {
            $return = false;
        }

        $this->cartProducts = 0;
        return $return;
    }

    /**
     * @return PackedBox
     */
    public function getPackedBox(): PackedBox
    {
        return $this->packedBox;
    }

    /**
     * Return used dimensions
     * @return Dimensions
     */
    public function getPackedBoxDimensions(): Dimensions
    {
        return new Dimensions(
            $this->packedBox->getUsedLength(),
            $this->packedBox->getUsedWidth(),
            $this->packedBox->getUsedDepth()
        );
    }

    /**
     * Return weight in grams
     * @return Weight
     */
    public function getPackedBoxWeight(): Weight
    {
       return new Weight($this->packedBox->getWeight() / 1000);
    }

    /**
     * @return int
     */
    public function getCartProducts(): int
    {
        return $this->cartProducts;
    }
}
