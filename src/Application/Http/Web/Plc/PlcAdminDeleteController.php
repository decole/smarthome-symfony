<?php

namespace App\Application\Http\Web\Plc;

use App\Domain\Identity\Entity\User;
use App\Domain\PLC\Service\PlcCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlcAdminDeleteController extends AbstractController
{
    public function __construct(private readonly PlcCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/plc/admin/delete/{id}', name: "plc_admin_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('plc_admin');
    }
}