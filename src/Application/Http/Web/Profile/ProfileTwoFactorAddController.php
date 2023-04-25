<?php

namespace App\Application\Http\Web\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Domain\SecureSystem\Service\TwoFactorQrCodeService;
use App\Infrastructure\TwoFactor\Service\TwoFactorService;
use App\Domain\SecureSystem\Service\TwoFactorCrudService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

final class ProfileTwoFactorAddController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorQrCodeService $qrCodeService,
        private readonly TwoFactorService $validateService,
        private readonly TwoFactorCrudService $service,
        private readonly Security $security
    ) {
    }

    #[Route('/user/profile/two-factor-add', name: "profile_two_factor_add")]
    public function addTwoFactor(Request $request): Response
    {
        $user = $this->security->getUser();
        $error = null;
        $success = false;

        if ($user === null) {
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
            'user' => $this->security->getUser(),
            'qr' => !$success ? $this->qrCodeService->generateImageSource($user, $secret) : null,
            'success' => $success === true ? 'Two factor saved' : null,
            'isShowQrCode' => !$success,
            'error' => $error,
        ]);
    }
}