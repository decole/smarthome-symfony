<?php

namespace App\Application\Http\Web\Common;

use App\Application\Service\SitePage\SitePageService;
use App\Domain\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DeviceListPageController extends AbstractController
{
    public function __construct(private SitePageService $service)
    {
    }

    #[Route('/devices', name: "devices")]
    public function devices(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('page/index.html.twig', [
            'title' => 'Все данные',
            'devices' => $this->service->getAllDeviceList()
        ]);
    }
}