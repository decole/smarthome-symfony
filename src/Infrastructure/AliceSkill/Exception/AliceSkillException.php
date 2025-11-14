<?php

declare(strict_types=1);

namespace App\Infrastructure\AliceSkill\Exception;

class AliceSkillException extends \Exception
{
    public static function dialogNotFound(): self
    {
        return new self('Dialog not found');
    }
}
