<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Profile;

use App\Domain\SecureSystem\Service\TwoFactorCrudService;
use App\Infrastructure\TwoFactor\Service\TwoFactorQrCodeService;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProfileTwoFactorAddController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorQrCodeService $qrCodeService,
        private readonly TwoFactorService $validateService,
        private readonly TwoFactorCrudService $service,
    ) {
    }

    #[Route('/user/profile/two-factor-add', name: "profile_two_factor_add")]
    public function addTwoFactor(Request $request): Response
    {
        $user = $this->getUser();
        $error = null;
        $success = false;

        if (!$user instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $secret = $this->validateService->getTemporarySecret($request);

        if ($request->isMethod('post')) {
            if ($this->validateService->validateCode(
                secret: $secret,
                code: $request->request->get('code')
            )) {
                $this->service->add(
                    user: $user,
                    secret: $secret
                );
                $success = true;
            } else {
                $error = 'Not correct code';
            }
        }

        return $this->render('crud/profile/profile.add.two.factor.html.twig', [
            'user' => $this->getUser(),
            'qr' => $success ? null : $this->qrCodeService->generateImageSource($user, $secret),
            'success' => $success ? 'Two factor saved' : null,
            'isShowQrCode' => !$success,
            'error' => $error,
        ]);
    }
}