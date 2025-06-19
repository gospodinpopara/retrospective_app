<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Retrospective;

class RetrospectiveInvite
{
    private int $id;
    private string $status;
    private Retrospective $retrospective;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRetrospective(): Retrospective
    {
        return $this->retrospective;
    }

    public function setRetrospective(Retrospective $retrospective): self
    {
        $this->retrospective = $retrospective;

        return $this;
    }
}
