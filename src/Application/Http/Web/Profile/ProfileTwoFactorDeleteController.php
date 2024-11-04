<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Domain\SecureSystem\Service\TwoFactorCrudService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProfileTwoFactorDeleteController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorCrudService $service,
    ) {
    }

    #[Route('/user/profile/two-factor-delete', name: "profile_two_factor_delete")]
    public function addTwoFactor(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $this->service->delete($user, $request);

        return $this->redirectToRoute('profile_two_factor_add');
    }
}