<?php

namespace App\Application\Http\Web\Page;

use App\Domain\Doctrine\Identity\Entity\User;
use App\Infrastructure\Doctrine\Service\Page\PageCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageAdminListController extends AbstractController
{
    public function __construct(private PageCrudService $crud)
    {
    }

    #[Route('/pages/admin', name: "page_admin")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/page/page.list.html.twig', [
            'pages' => $this->crud->list(),
        ]);
    }
}