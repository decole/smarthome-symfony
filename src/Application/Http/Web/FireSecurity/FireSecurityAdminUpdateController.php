<?php

declare(strict_types=1);

namespace App\Application\Http\Web\FireSecurity;

use App\Domain\FireSecurity\Service\FireSecurityCrudService;
use App\Domain\Identity\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class FireSecurityAdminUpdateController extends AbstractController
{
    public function __construct(private FireSecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/fire-security/update/{id}', name: "fire_secure_admin_update_by_id")]
    public function update(string $id, Request $request): Response
    {
        $errors = [];

        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $dto = $this->crud->entityByDto($id);

        if ($request->isMethod('post')) {
            $dto = $this->crud->createFireSecurityDto($request);

            $errors = $this->crud->validate($dto, true);

            if (count($errors) === 0) {
                $this->crud->update($id, $dto);

                return $this->redirectToRoute('fire_secure_admin');
            }
        }

        return $this->render('crud/firesecurity/firesecurity.save.entity.html.twig', [
            'action' => 'update',
            'entityId' => $id,
            'security' => $dto,
            'errors' => $errors ?? [],
        ]);
    }
}