<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Sensor;

use App\Domain\Identity\Entity\User;
use App\Domain\Sensor\Service\SensorCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SensorAdminDeleteController extends AbstractController
{
    public function __construct(private SensorCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/sensors/admin/delete/{id}', name: "sensors_admin_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('sensors_admin');
    }
}