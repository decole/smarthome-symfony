<?php

namespace App\Application\Http\Api;

use App\Application\Service\SitePage\ApiUriTranscribeService;
use App\Domain\DeviceData\Service\DeviceDataCacheService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function __construct(
        private readonly ApiUriTranscribeService $service,
        private readonly DeviceDataCacheService $deviceDataCacheService
    ) {
    }

    /**
     * https://blog.programster.org/setting-up-google-2FA-on-php
     * https://medium.com/@richb_/easy-two-factor-authentication-2fa-with-google-authenticator-php-108388a1ea23
     * https://github.com/antonioribeiro/google2fa-qrcode
     * https://github.com/antonioribeiro/google2fa
     */
    #[Route('/test')]
    public function index(Request $request): Response
    {
        $google2fa = new \PragmaRX\Google2FA\Google2FA();

//        $userSecret = $google2fa->generateSecretKey();

        $userSecret = 'EUPNCWPCAQKJU33D';
        $code = '250265';
        $valid = $google2fa->verifyKey($userSecret, $code);

        dd($code, $valid);

        $title = "blog.programster.org";
        $usernameOrEmail = "admin@programster.org";

        $qrCodeData = $google2fa->getQRCodeUrl(
            $title,
            $usernameOrEmail,
            $userSecret
        );

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
            new \BaconQrCode\Renderer\Image\ImagickImageBackEnd()
        );

        $writer = new \BaconQrCode\Writer($renderer);
        $writer->writeFile($qrCodeData, '../var/cache/qrcode.png');

        return new JsonResponse([
            'secret' => $userSecret,
            'verify' => $valid,
        ]);
    }
}