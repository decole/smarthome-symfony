<?php

namespace App\Application\Http\Web\FireSecurity;

use App\Domain\Doctrine\Identity\Entity\User;
use App\Infrastructure\Doctrine\Service\FireSecurity\FireSecurityCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FireSecurityAdminListController extends AbstractController
{
    public function __construct(private FireSecurityCrudService $crud)
    {
    }

    #[Route('/fire-security/admin', name: "fire_secure_admin")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/firesecurity/firesecurity.list.html.twig', [
            'security' => $this->crud->list(),
        ]);
    }
}
