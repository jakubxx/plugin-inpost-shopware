<?php

namespace WebLivesInPost\Installer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use WebLivesInPost\Util\ShippingHelper;

class ActivateDeactivate
{

    /**
     * @var Context
     */
    private $context;

    /**
     * @var EntityRepositoryInterface
     */
    private $shippingRepository;

    /**
     * @var ShippingHelper
     */
    private $shippingHelper;

    public function __construct(
        Context $context,
        EntityRepositoryInterface $shippingRepository
    )
    {
        $this->context = $context;
        $this->shippingRepository = $shippingRepository;
        $this->shippingHelper = new ShippingHelper($shippingRepository);
    }

    public function activate()
    {
//        $this->setShippingIsActive(true, $this->context);
    }

    public function deactivate()
    {
//        $this->setShippingIsActive(false, $this->context);
    }

    private function setShippingIsActive(bool $active, $context)
    {
        $lockerShippingId = $this->shippingHelper->getLockerShippingId($context);
        $courierShippingId = $this->shippingHelper->getCourierShippingId($context);

        if ($lockerShippingId !== null) {
            $updateData = [[
                'id' => $lockerShippingId,
                'active' => $active,
            ]];

            $this->shippingRepository->update($updateData, $context);
        }

        if ($courierShippingId !== null) {
            $updateData = [[
                'id' => $courierShippingId,
                'active' => $active,
            ]];

            $this->shippingRepository->update($updateData, $context);
        }
    }
}
