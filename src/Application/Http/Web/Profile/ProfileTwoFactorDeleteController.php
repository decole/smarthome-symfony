<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Domain\SecureSystem\Service\TwoFactorCrudService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

final class ProfileTwoFactorDeleteController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorCrudService $service,
        private readonly Security $security
    ) {
    }

    #[Route('/user/profile/two-factor-delete', name: "profile_two_factor_delete")]
    public function addTwoFactor(Request $request): Response
    {
        $user = $this->security->getUser();

        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $this->service->delete($user, $request);

        return $this->redirectToRoute('profile_two_factor_add');
    }
}