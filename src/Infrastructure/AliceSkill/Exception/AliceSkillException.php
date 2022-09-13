<?php

namespace App\Infrastructure\AliceSkill\Exception;

use Exception;

class AliceSkillException extends Exception
{
    public static function dialogNotFound(): self
    {
        return new self('Dialog not found');
    }
}