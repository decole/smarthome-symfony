<?php

namespace App\Application\Http\Web\FireSecurity;

use App\Application\Service\SitePage\SitePageService;
use App\Domain\Doctrine\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FireSecurityPageController extends AbstractController
{
    public function __construct(private SitePageService $service)
    {
    }

    #[Route('/fire-security', name: "fire_secure_page")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('page/index.html.twig', [
            'title' => 'Пожарная система',
            'devices' => $this->service->getDeviceList(name: 'fire-security')
        ]);
    }
}
