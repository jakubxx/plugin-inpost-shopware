<?php declare(strict_types=1);

namespace WebLivesInPost\Tasks\Handlers;


use Monolog\Logger;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use WebLivesInPost\Services\ImportDeliveryService;
use WebLivesInPost\Tasks\ImportTask;

class ImportTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var ImportDeliveryService
     */
    private $service;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        Logger $logger,
        ImportDeliveryService $ids
    ) {
        $this->service = $ids;
        $this->logger = $logger;

        parent::__construct($scheduledTaskRepository);
    }

    public static function getHandledMessages(): iterable
    {
        return [ImportTask::class];
    }

    public function run(): void
    {
        $this->service->run();
    }
}
