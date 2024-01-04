<?php declare(strict_types=1);

namespace WebLivesInPost;

require_once __DIR__ . '/../vendor/autoload.php';

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use WebLivesInPost\Installer\InstallUninstall;
use WebLivesInPost\Installer\ActivateDeactivate;

class WebLivesInPost extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        /** @var EntityRepositoryInterface $shippingRepository */
        $shippingRepository = $this->container->get('shipping_method.repository');
        /** @var EntityRepositoryInterface $deliveryTimeRepository */
        $deliveryTimeRepository = $this->container->get('delivery_time.repository');
        /** @var EntityRepositoryInterface $ruleRepository */
        $ruleRepository = $this->container->get('rule.repository');

        (new InstallUninstall(
            $installContext->getContext(),
            $shippingRepository,
            $deliveryTimeRepository,
            $ruleRepository
        ))->install();

        parent::install($installContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        /** @var EntityRepositoryInterface $shippingRepository */
        $shippingRepository = $this->container->get('shipping_method.repository');
//        /** @var EntityRepositoryInterface $deliveryTimeRepository */
//        $deliveryTimeRepository = $this->container->get('delivery_time.repository');
//        /** @var EntityRepositoryInterface $ruleRepository */
//        $ruleRepository = $this->container->get('rule.repository');

        /* deactivate instead of removing */
        (new ActivateDeactivate(
            $uninstallContext->getContext(),
            $shippingRepository
        ))->deactivate();

//        (new InstallUninstall(
//            $uninstallContext->getContext(),
//            $shippingRepository,
//            $deliveryTimeRepository,
//            $ruleRepository
//        ))->uninstall();

        parent::uninstall($uninstallContext);
    }

//    public function activate(ActivateContext $activateContext): void
//    {
//        /** @var EntityRepositoryInterface $shippingRepository */
//        $shippingRepository = $this->container->get('shipping_method.repository');
//
//        (new ActivateDeactivate(
//            $activateContext->getContext(),
//            $shippingRepository
//        ))->activate();
//
//        parent::activate($activateContext);
//    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        /** @var EntityRepositoryInterface $shippingRepository */
        $shippingRepository = $this->container->get('shipping_method.repository');

        (new ActivateDeactivate(
            $deactivateContext->getContext(),
            $shippingRepository
        ))->deactivate();

        parent::deactivate($deactivateContext);
    }
}
