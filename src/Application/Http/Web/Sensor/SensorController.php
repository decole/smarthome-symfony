<?php


namespace App\Application\Http\Web\Sensor;


use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Sensor\Entity\Sensor;
use App\Infrastructure\Doctrine\Service\Sensor\SensorCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SensorController extends AbstractController
{
    public function __construct(private SensorCrudService $crud)
    {
    }

    #[Route('/sensors', name: "sensors")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('sensor/sensor.list.html.twig', [
            'sensors' => $this->crud->list(),
            'typeTranscribe' => Sensor::TYPE_TRANSCRIBES
        ]);
    }
}