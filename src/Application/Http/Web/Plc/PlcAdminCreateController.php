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

final class PlcAdminCreateController extends AbstractController
{
    public function __construct(private readonly PlcCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/plc/admin/create', name: "plc_admin_create")]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $dto = $this->crud->createDto($request);

        if ($request->isMethod('post')) {

            $errors = $this->crud->validate($dto);

            if (count($errors) === 0) {
                $this->crud->create($dto);

                return $this->redirectToRoute('plc_admin');
            }
        }

        return $this->render('crud/plc/plc.save.entity.html.twig', [
            'action' => 'create',
            'plc' => $dto,
            'errors' => $errors ?? [],
        ]);
    }
}