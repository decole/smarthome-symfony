<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Security;

use App\Domain\Identity\Entity\User;
use App\Domain\Security\Service\SecurityCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityAdminDeleteController extends AbstractController
{
    public function __construct(private SecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/security/admin/delete/{id}', name: "security_admin_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('security_admin');
    }
}