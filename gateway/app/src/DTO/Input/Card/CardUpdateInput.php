<?php

declare(strict_types=1);

namespace App\DTO\Input\Card;

use App\Entity\Card;
use App\Utils\DTOValidationUtil;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CardUpdateInput
{
    #[Assert\Type(type: 'integer', message: 'Card ID must be an integer.')]
    #[Assert\NotBlank(message: 'Card ID cannot be blank.')]
    private int $cardId;

    #[Assert\Length(max: 255, maxMessage: 'Title cannot exceed 255 characters.')]
    private ?string $title = null;

    #[Assert\Length(max: 1000, maxMessage: 'Description cannot exceed 1000 characters.')]
    private ?string $description = null;

    #[Assert\Choice(choices: Card::CARD_TYPES, message: 'Invalid type provided.')]
    private ?string $type = null;

    public function getCardId(): int
    {
        return $this->cardId;
    }

    public function setCardId(int $cardId): self
    {
        $this->cardId = $cardId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
