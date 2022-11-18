<?php

namespace App\Application\Http\Web\Relay;

use App\Domain\Identity\Entity\User;
use App\Domain\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayAdminCreateController extends AbstractController
{
    public function __construct(private RelayCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/relay/admin/create', name: "relay_admin_create")]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $relayDto = $this->crud->createDto($request);

        if ($request->isMethod('post')) {

            $errors = $this->crud->validate($relayDto);

            if (count($errors) === 0) {
                $this->crud->create($relayDto);

                return $this->redirectToRoute('relay_admin');
            }
        }

        return $this->render('crud/relay/relay.save.entity.html.twig', [
            'action' => 'create',
            'relay' => $relayDto,
            'typeTranscribe' => Relay::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}