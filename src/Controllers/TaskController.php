<?php declare(strict_types=1);

namespace WebLivesInPost\Controllers;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use WebLivesInPost\Services\ExportDeliveryService;
use WebLivesInPost\Services\ImportDeliveryService;

/**
 * @RouteScope(scopes={"api"})
 */
class TaskController extends AbstractController
{
    /**
     * @var ExportDeliveryService
     */
    private $export;

    /**
     * @var ImportDeliveryService
     */
    private $import;

    public function __construct(
        ExportDeliveryService $eds,
        ImportDeliveryService $ids
    )
    {
        $this->export = $eds;
        $this->import = $ids;
    }

    /**
     * @RouteScope(scopes={"api"})
     * @Route("/api/v{version}/weblives/inpost/export", name="api.action.weblives.inpost.export", methods={"POST"})
     *
     * @param Request $request
     * @param Context $context
     *
     * @return JsonResponse
     */
    public function runExport(Request $request, Context $context): JsonResponse
    {
        $this->export->run();

        $result = $this->export->getResult();

        return new JsonResponse($result);
    }

    /**
     * @RouteScope(scopes={"api"})
     * @Route("/api/v{version}/weblives/inpost/import", name="api.action.weblives.inpost.import", methods={"POST"})
     *
     * @param Request $request
     * @param Context $context
     *
     * @return JsonResponse
     */
    public function runImport(Request $request, Context $context): JsonResponse
    {
        $this->import->run();

        $result = $this->import->getResult();

        return new JsonResponse($result);
    }
}
