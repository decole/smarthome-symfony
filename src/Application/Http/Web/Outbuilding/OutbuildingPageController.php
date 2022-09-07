<?php

namespace App\Application\Http\Web\Outbuilding;

use App\Application\Service\SitePage\SitePageService;
use App\Domain\Doctrine\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OutbuildingPageController extends AbstractController
{
    public function __construct(private SitePageService $service)
    {
    }

    #[Route('/outbuilding', name: "outbuilding")]
    public function outbuilding(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('page/index.html.twig', [
            'title' => 'Пристройка',
            'devices' => $this->service->getDeviceList(name: 'outbuilding')
        ]);
    }
}
