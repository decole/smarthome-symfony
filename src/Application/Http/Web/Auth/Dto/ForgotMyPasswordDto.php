<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Auth\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ForgotMyPasswordDto
{
    #[Assert\Email]
    public mixed $email;

    public string $_csrf_token;
}