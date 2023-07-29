<?php

declare(strict_types=1);

namespace App\Infrastructure\AliceSkill\Dialog;

use App\Infrastructure\AliceSkill\Dto\AliceSkillRequestDto;

abstract class AbstractDialog
{
    public function __construct(protected readonly AliceSkillRequestDto $requestDto)
    {
    }
}