<?php

declare(strict_types=1);

namespace App\DTO\Input\Card;

use App\Entity\Card;
use App\Utils\DTOValidationUtil;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CardCreateInput
{
    #[Assert\NotBlank(message: 'Title cannot be blank.')]
    #[Assert\Length(max: 255, maxMessage: 'Title cannot exceed 255 characters.')]
    private string $title;

    #[Assert\NotBlank(message: 'Description cannot be blank.')]
    #[Assert\Length(max: 1000, maxMessage: 'Description cannot exceed 1000 characters.')]
    private string $description;

    #[Assert\NotBlank(message: 'Type cannot be blank.')]
    #[Assert\Choice(choices: Card::CARD_TYPES, message: 'Invalid type provided.')]
    private string $type;

    #[Assert\NotBlank(message: 'Retrospective ID cannot be blank.')]
    #[Assert\Type(type: 'integer', message: 'Retrospective ID must be an integer.')]
    private int $retrospectiveId;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRetrospectiveId(): int
    {
        return $this->retrospectiveId;
    }

    public function setRetrospectiveId(int $retrospectiveId): self
    {
        $this->retrospectiveId = $retrospectiveId;

        return $this;
    }

    public static function createFromArray(array $input): self
    {
        $serializer = new Serializer([new ObjectNormalizer()]);

        $inputDto = $serializer->denormalize($input, self::class);

        DTOValidationUtil::validateDto($inputDto);

        return $inputDto;
    }
}
