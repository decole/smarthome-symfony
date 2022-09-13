<?php

namespace App\Application\Http\Web\Watering;

use App\Application\Service\SitePage\SitePageService;
use App\Domain\Doctrine\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WateringPageController extends AbstractController
{
    public function __construct(private SitePageService $service)
    {
    }

    #[Route('/watering', name: "watering")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('watering/watering.index.html.twig', [
            'title' => 'Полив огорода',
            'devices' => $this->service->getDeviceList(name: 'watering')
        ]);
    }
}