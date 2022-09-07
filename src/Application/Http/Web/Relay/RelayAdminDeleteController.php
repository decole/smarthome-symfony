<?php

namespace App\Application\Http\Web\Relay;

use App\Domain\Doctrine\Identity\Entity\User;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayAdminDeleteController extends AbstractController
{
    public function __construct(private RelayCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/relay/admin/delete/{id}', name: "relay_admin_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('relay_admin');
    }
}
