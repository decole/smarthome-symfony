<?php

declare(strict_types=1);

namespace App\Application\Http\Web\FireSecurity;

use App\Domain\FireSecurity\Service\FireSecurityCrudService;
use App\Domain\Identity\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FireSecurityAdminDeleteController extends AbstractController
{
    public function __construct(private FireSecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/fire-security/delete/{id}', name: "fire_secure_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('fire_secure_admin');
    }
}