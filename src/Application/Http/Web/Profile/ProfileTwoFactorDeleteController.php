<?php

namespace App\Application\Http\Web\Profile;

use App\Domain\SecureSystem\Service\TwoFactorCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProfileTwoFactorDeleteController extends AbstractController
{
    public function __construct(private readonly TwoFactorCrudService $service)
    {
    }
}