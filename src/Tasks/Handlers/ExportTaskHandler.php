<?php declare(strict_types=1);

namespace WebLivesInPost\Tasks\Handlers;


use Monolog\Logger;
use WebLivesInPost\Services\ExportDeliveryService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use WebLivesInPost\Tasks\ExportTask;

class ExportTaskHandler extends ScheduledTaskHandler
{

    /**
     * @var ExportDeliveryService
     */
    private $service;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        Logger $logger,
        ExportDeliveryService $eds
    ) {
        $this->service = $eds;
        $this->logger = $logger;

        parent::__construct($scheduledTaskRepository);
    }

    public static function getHandledMessages(): iterable
    {
        return [ExportTask::class];
    }

    public function run(): void
    {
        $this->service->run();
    }
}
