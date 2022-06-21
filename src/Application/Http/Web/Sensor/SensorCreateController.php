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

final class SensorCreateController extends AbstractController
{
    public function __construct(private SensorCrudService $crud)
    {
    }

    #[Route('/sensors/create', name: "sensors_create")]
    public function createList(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('sensor/sensor.create.list.html.twig', [
            'sensorTypes' => $this->crud->getTypes(),
            'typeTranscribe' => Sensor::TYPE_TRANSCRIBES,
        ]);
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/sensors/create/{type}', name: "sensors_create_by_type")]
    public function createByType(string $type, Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $sensorDto = $this->crud->createSensorDto($type, $request);

        if ($request->isMethod('post')) {
            $errors = $this->crud->validate($sensorDto);

            if (count($errors) === 0) {
                $this->crud->create($sensorDto);

                return $this->redirectToRoute('sensors');
            }
        }

        return $this->render('sensor/sensor.save.entity.html.twig', [
            'action' => 'create',
            'sensor' => $sensorDto,
            'typeTranscribe' => Sensor::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}