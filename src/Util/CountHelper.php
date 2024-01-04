<?php

namespace WebLivesInPost\Util;

class CountHelper
{
    const COUNTER_FAILED = 'failed';
    const COUNTER_SUCCESS = 'success';

    /**
     * @var int
     */
    private $failed;

    /**
     * @var int
     */
    private $success;

    public function __construct()
    {
        $this->failed = 0;
        $this->success = 0;
    }

    /**
     * @return int
     */
    public function getFailed(): int
    {
        return $this->failed;
    }

    /**
     * @return void
     */
    public function addFailed(): void
    {
        $this->failed++;
    }

    /**
     * @return int
     */
    public function getSuccess(): int
    {
        return $this->success;
    }

    /**
     * @return void
     */
    public function addSuccess(): void
    {
        $this->success++;
    }

    /**
     * Get counts as array
     * @return array
     */
    public function getCounts(): array
    {
        return [
            self::COUNTER_FAILED => $this->getFailed(),
            self::COUNTER_SUCCESS => $this->getSuccess()
        ];
    }
}
