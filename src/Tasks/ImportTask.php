<?php declare(strict_types=1);

namespace WebLivesInPost\Tasks;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ImportTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'web_lives.in_post.import_task';
    }

    public static function getDefaultInterval(): int
    {
        return 15 * 60; // 15 min
    }
}
