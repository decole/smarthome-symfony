<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Page;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\SitePageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageViewController extends AbstractController
{
    public function __construct(private readonly SitePageService $service)
    {
    }

    #[Route('/{name}')]
    public function view(string $name): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('page/index.html.twig', [
            'title' => $name,
            'devices' => $this->service->getDeviceList(name: $name),
        ]);
    }
}