<?php

namespace App\Infrastructure\AliceSkill\Dialog;

use App\Infrastructure\AliceSkill\Dto\AliceSkillRequestDto;

abstract class AbstractDialog
{
    public function __construct(protected AliceSkillRequestDto $requestDto)
    {
    }
}