<?php

namespace App\Application\Http\Web\Relay;

use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RelayController extends AbstractController
{
    public function __construct(private RelayCrudService $crud)
    {
    }

    #[Route('/relays', name: "relays")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('relay/relay.list.html.twig', [
            'relays' => $this->crud->list(),
            'typeTranscribe' => Relay::TYPE_TRANSCRIBES
        ]);
    }
}