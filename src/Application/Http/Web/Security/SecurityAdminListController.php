<?php

namespace App\Application\Http\Web\Security;

use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Security\Entity\Security;
use App\Infrastructure\Doctrine\Service\Security\SecurityCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityAdminListController extends AbstractController
{
    public function __construct(private SecurityCrudService $crud)
    {
    }

    #[Route('/security/admin', name: "security_admin")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/security/security.list.html.twig', [
            'security' => $this->crud->list(),
            'typeTranscribe' => Security::TYPE_TRANSCRIBES
        ]);
    }
}
