<?php

namespace App\Application\Http\Web\Home;

use App\Application\Service\SitePage\SitePageService;
use App\Domain\Doctrine\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomePageController extends AbstractController
{
    public function __construct(private SitePageService $service)
    {
    }

    #[Route('/', name: "home")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('page/index.html.twig', [
            'title' => 'Главная',
            'devices' => $this->service->getDeviceList(name: 'home')
        ]);
    }
}