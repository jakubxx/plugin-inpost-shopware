<?php declare(strict_types=1);

namespace WebLivesInPost\Resources\snippet;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_pl_PL implements SnippetFileInterface
{
    public function getName(): string
    {
        return 'storefront.pl-PL';
    }

    public function getPath(): string
    {
        return __DIR__ . '/' . $this->getName() . '.json';
    }

    public function getIso(): string
    {
        return 'pl-PL';
    }

    public function getAuthor(): string
    {
        return 'WebLives';
    }

    public function isBase(): bool
    {
        return false;
    }
}
