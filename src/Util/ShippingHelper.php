<?php declare(strict_types=1);

namespace WebLivesInPost\Util;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;

class ShippingHelper
{
    /**
     * @var EntityRepositoryInterface
     */
    private $shippingRepository;

    /**
     * ShippingHelper constructor.
     * @param EntityRepositoryInterface $shippingRepository
     */
    public function __construct(EntityRepositoryInterface $shippingRepository)
    {
        $this->shippingRepository = $shippingRepository;
    }

    /**
     * @param Context $context
     * @return string|null
     */
    public function getLockerShippingId(Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new ContainsFilter('customFields', Constants::LOCKER_IDENTIFIER)
        );

        return $this->shippingRepository->searchIds($criteria, $context)->firstId();
    }

    /**
     * @param Context $context
     * @return string|null
     */
    public function getCourierShippingId(Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new ContainsFilter('customFields', Constants::COURIER_IDENTIFIER)
        );

        return $this->shippingRepository->searchIds($criteria, $context)->firstId();
    }

    /**
     * @param Context $context
     * @return string|null
     */
    public function getCourierShippingEntity(Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new ContainsFilter('customFields', Constants::COURIER_IDENTIFIER)
        );

        return $this->shippingRepository->search($criteria, $context)->first();
    }
}
