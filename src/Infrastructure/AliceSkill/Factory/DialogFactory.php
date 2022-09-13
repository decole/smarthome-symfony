<?php

namespace App\Infrastructure\AliceSkill\Factory;

use App\Infrastructure\AliceSkill\Dialog\AliceDialogInterface;
use App\Infrastructure\AliceSkill\Dialog\HelloDialog;
use App\Infrastructure\AliceSkill\Dialog\PingDialog;
use App\Infrastructure\AliceSkill\Dto\AliceSkillRequestDto;
use App\Infrastructure\AliceSkill\Exception\AliceSkillException;

final class DialogFactory
{
    private const MAP = [
        PingDialog::class,
        HelloDialog::class,
    ];

    /**
     * @throws AliceSkillException
     */
    public function create(AliceSkillRequestDto $dto): AliceDialogInterface
    {
        /** @var AliceDialogInterface $dialogClass */
        foreach (self::MAP as $dialogClass) {
            if (in_array($dto->getCommand(), $dialogClass::getCommandVerbList(), true)) {
                return new $dialogClass($dto);
            }
        }

        throw AliceSkillException::dialogNotFound();
    }
}