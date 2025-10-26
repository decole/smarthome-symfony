<?php

declare(strict_types=1);

namespace App\Infrastructure\TwoFactor\Service;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\Security\Core\User\UserInterface;

final class TwoFactorQrCodeService
{
    public function __construct(private string $host) {}

    public function generateImageSource(UserInterface $user, string $secret): string
    {
        $qrCodeData = (new Google2FA())->getQRCodeUrl(
            $this->host,
            $user->getUserIdentifier(),
            $secret,
        );

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd(),
        );

        $writer = new Writer($renderer);

        return base64_encode($writer->writeString($qrCodeData));
    }
}
