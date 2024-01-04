<?php declare(strict_types=1);

namespace WebLivesInPost\Tasks;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ExportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'web_lives.in_post.export_task';
    }

    public static function getDefaultInterval(): int
    {
        return 10 * 60; // 10 min
    }
}
