<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Home;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\SitePageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'home_page', defaults: ['_format' => 'html'], methods: ['GET'])]
    #[Cache(smaxage: 10)]
    public function index(SitePageService $service): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);
        $name = 'home';

        return $this->render('page/index.html.twig', [
            'title' => $name,
            'devices' => $service->getDeviceList(name: $name),
        ]);
    }
}
