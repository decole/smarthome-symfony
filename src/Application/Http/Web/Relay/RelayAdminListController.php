<?php

namespace App\Application\Http\Web\Relay;

use App\Domain\Identity\Entity\User;
use App\Domain\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayAdminListController extends AbstractController
{
    public function __construct(private RelayCrudService $crud)
    {
    }

    #[Route('/relay/admin', name: "relay_admin")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/relay/relay.list.html.twig', [
            'relays' => $this->crud->list(),
            'typeTranscribe' => Relay::TYPE_TRANSCRIBES
        ]);
    }
}