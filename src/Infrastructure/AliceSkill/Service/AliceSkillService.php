<?php

namespace App\Infrastructure\AliceSkill\Service;

use App\Infrastructure\AliceSkill\Dto\AliceSkillRequestDto;
use App\Infrastructure\AliceSkill\Dto\AliceSkillResponseDto;
use App\Infrastructure\AliceSkill\Factory\DialogFactory;

final class AliceSkillService
{
    public function getDialogAnswer(?array $request): AliceSkillResponseDto
    {
        return (new DialogFactory)->create($this->gidrate($request))->getAnswer();
    }

    private function gidrate(?array $request): AliceSkillRequestDto
    {
        return (new AliceSkillRequestDto($request));
    }
}