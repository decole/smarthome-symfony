<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Relay;

use App\Domain\Identity\Entity\User;
use App\Domain\Relay\Entity\Relay;
use App\Domain\Relay\Service\RelayCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayAdminUpdateController extends AbstractController
{
    public function __construct(private RelayCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/relay/admin/update/{id}', name: "relay_admin_update_by_id")]
    public function update(string $id, Request $request): Response
    {
        $errors = [];

        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $relayDto = $this->crud->entityByDto($id);

        if ($request->isMethod('post')) {
            $relayDto = $this->crud->createDto($request);

            $errors = $this->crud->validate($relayDto, true);

            if (count($errors) === 0) {
                $this->crud->update($id, $relayDto);

                return $this->redirectToRoute('relay_admin');
            }
        }

        return $this->render('crud/relay/relay.save.entity.html.twig', [
            'action' => 'update',
            'entityId' => $id,
            'relay' => $relayDto,
            'typeTranscribe' => Relay::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}