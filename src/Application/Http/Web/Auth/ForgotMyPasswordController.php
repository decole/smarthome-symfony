<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Auth;

use App\Application\Http\Web\Auth\Dto\ForgotMyPasswordDto;
use App\Domain\SecureSystem\Service\RestorePasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use JsonException;

final class ForgotMyPasswordController extends AbstractController
{
    public function __construct(
        private readonly RestorePasswordService $service,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws JsonException|TransportExceptionInterface
     */
    #[Route(path: '/forgot_password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response
    {
        $banner = false;

        if ($request->get('email')) {
            $dto = $this->serializer->deserialize(
                json_encode($request->request->all(), JSON_THROW_ON_ERROR),
                ForgotMyPasswordDto::class,
                'json'
            );

            $this->service->sendRestoreTokenByEmail($dto);

            $banner = true;
        }

        return $this->render('login/forgotPassword.html.twig', [
            'isEnableRegistration' => $this->getParameter('app.registration'),
            'host' => $this->getParameter('app.host'),
            'banner' => $banner,
        ]);
    }
}