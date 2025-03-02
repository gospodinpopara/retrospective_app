<?php

declare(strict_types=1);

namespace App\DTO\Input\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationInput
{
    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Invalid email format.')]
    private string $email;

    #[Assert\NotBlank(message: 'Password is required.')]
    #[Assert\Length(
        min: 8,
        max: 50,
        minMessage: 'Password must be at least {{ limit }} characters long.',
        maxMessage: 'Password cannot be longer than {{ limit }} characters.',
    )]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/",
        message: 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
    )]
    private string $password;

    #[Assert\NotBlank(message: 'Confirm password is required.')]
    #[Assert\EqualTo(
        propertyPath: 'password',
        message: 'Passwords do not match.',
    )]
    private string $confirmPassword;

    #[Assert\NotBlank(message: 'First name is required.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'First name must be at least {{ limit }} characters.',
        maxMessage: 'First name cannot be longer than {{ limit }} characters.',
    )]
    private string $firstName;

    #[Assert\NotBlank(message: 'Last name is required.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Last name must be at least {{ limit }} characters.',
        maxMessage: 'Last name cannot be longer than {{ limit }} characters.',
    )]
    private string $lastName;

    public function __construct(string $email, string $password, string $confirmPassword, string $firstName, string $lastName)
    {
        $this->email = $email;
        $this->password = $password;
        $this->confirmPassword = $confirmPassword;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
