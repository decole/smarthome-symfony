<?php

namespace App\Infrastructure\AliceSkill\Dialog;

use App\Infrastructure\AliceSkill\Dto\AliceSkillResponseDto;

interface AliceDialogInterface
{
    public static function getCommandVerbList(): array;

    public function getAnswer(): AliceSkillResponseDto;
}