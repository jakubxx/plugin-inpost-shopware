<?php declare(strict_types=1);

namespace WebLivesInPost\Rules;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Rule\CartRuleScope;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;
use WebLivesInPost\Services\PackerService;
use WebLivesInPost\Util\Constants;

class IsLockerPackageRule extends Rule
{
    /**
     * @var PackerService
     */
    private $packerService;

    public function __construct()
    {
        $this->packerService = new PackerService();

        parent::__construct();
    }

    public function getName(): string
    {
        return 'weblives_locker_package';
    }

    public function match(RuleScope $scope): bool
    {
        if ($scope instanceof CartRuleScope) {
            return $this->matchCart($scope->getCart());
        }

        return false;
    }

    /**
     * @param Cart $cart
     * @return bool
     */
    private function matchCart(Cart $cart): bool
    {
        $lineItems = $cart->getLineItems();

        $this->packerService->addItems($lineItems);

        return $this->packerService->willFit(Constants::SERVICE_LOCKER_STANDARD);
    }

    public function getConstraints(): array
    {
        return [
            //
        ];
    }
}
