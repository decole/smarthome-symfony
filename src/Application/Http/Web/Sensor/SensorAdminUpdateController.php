<?php

namespace App\Application\Http\Web\Sensor;

use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Service\Sensor\SensorCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SensorAdminUpdateController extends AbstractController
{
    public function __construct(private SensorCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/sensors/admin/update/{id}', name: "sensors_admin_update_by_id")]
    public function update(string $id, Request $request): Response
    {
        $errors = [];

        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $sensorDto = $this->crud->entityByDto($id);

        if ($request->isMethod('post')) {
            $sensorDto = $this->crud->createDto($sensorDto->type, $request);

            $errors = $this->crud->validate($sensorDto, true);

            if (count($errors) === 0) {
                $this->crud->update($id, $sensorDto);

                return $this->redirectToRoute('sensors_admin');
            }
        }

        return $this->render('crud/sensor/sensor.save.entity.html.twig', [
            'action' => 'update',
            'entityId' => $id,
            'sensor' => $sensorDto,
            'typeTranscribe' => Sensor::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}