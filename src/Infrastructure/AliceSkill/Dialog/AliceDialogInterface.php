<?php

declare(strict_types=1);

namespace App\Infrastructure\AliceSkill\Dialog;

use App\Infrastructure\AliceSkill\Dto\AliceSkillRequestDto;
use App\Infrastructure\AliceSkill\Dto\AliceSkillResponseDto;

interface AliceDialogInterface
{
    public static function getCommandVerbList(): array;

    public static function getInstance(AliceSkillRequestDto $dto): self;

    public function getAnswer(): AliceSkillResponseDto;
}