<?php

namespace WebLivesInPost\Models\Parcel;

class TemplateParcel extends Parcel
{
    public const SMALL = 'small';
    public const MEDIUM = 'medium';
    public const LARGE = 'large';

    public function __construct(
        string $template
    ) {
        $this->setTemplate($template);
    }
}
