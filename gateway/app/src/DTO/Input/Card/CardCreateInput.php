<?php

declare(strict_types=1);

namespace App\DTO\Input\Card;

class CardCreateInput
{
    private string $title;
    private string $description;
    private string $type;

    public function __construct(string $title, string $description, string $type)
    {
        $this->title = trim($title);
        $this->description = trim($description);
        $this->type = $type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
