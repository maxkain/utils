<?php

declare(strict_types=1);

namespace App\Utils\Crud;

class Breadcrumb
{
    public function __construct(
        private string $title,
        private ?string $href = null
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): static
    {
        $this->href = $href;

        return $this;
    }
}
