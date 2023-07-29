<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Sensor;

use App\Domain\Identity\Entity\User;
use App\Domain\Sensor\Entity\Sensor;
use App\Domain\Sensor\Service\SensorCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SensorAdminListController extends AbstractController
{
    public function __construct(private SensorCrudService $crud)
    {
    }

    #[Route('/sensors/admin', name: "sensors_admin")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/sensor/sensor.list.html.twig', [
            'sensors' => $this->crud->list(),
            'typeTranscribe' => Sensor::TYPE_TRANSCRIBES
        ]);
    }
}