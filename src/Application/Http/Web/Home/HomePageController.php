<?php

namespace App\Application\Http\Web\Home;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\SitePageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HomePageController extends AbstractController
{
    public function __construct(private SitePageService $service, private TranslatorInterface $translator)
    {
    }

    #[Route('/', name: "home")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('page/index.html.twig', [
            'title' => $this->translator->trans('Home'),
            'devices' => $this->service->getDeviceList(name: 'home')
        ]);
    }
}