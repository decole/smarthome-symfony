<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Plc;

use App\Domain\Identity\Entity\User;
use App\Domain\PLC\Service\PlcCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlcAdminUpdateController extends AbstractController
{
    public function __construct(private readonly PlcCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/plc/admin/update/{id}', name: "plc_admin_update_by_id")]
    public function update(string $id, Request $request): Response
    {
        $errors = [];

        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $dto = $this->crud->entityByDto($id);

        if ($request->isMethod('post')) {
            $dto = $this->crud->createDto($request);
            $dto->savedId = $id;

            $errors = $this->crud->validate($dto, true);

            if (count($errors) === 0) {
                $this->crud->update($id, $dto);

                return $this->redirectToRoute('plc_admin');
            }
        }

        return $this->render('crud/plc/plc.save.entity.html.twig', [
            'action' => 'update',
            'entityId' => $id,
            'plc' => $dto,
            'errors' => $errors ?? [],
        ]);
    }
}