<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Main;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\SitePageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
final class MainController extends AbstractController
{
    private const NAME = 'main';

    #[Route('/main', name: 'main', methods: ['GET'])]
    public function index(
        #[CurrentUser]
        User $user,
        SitePageService $service,
    ): Response {
        return $this->render('page/index.html.twig', [
            'title' => 'Главная',
            'devices' => $service->getDeviceList(name: self::NAME),
        ]);
    }
}
