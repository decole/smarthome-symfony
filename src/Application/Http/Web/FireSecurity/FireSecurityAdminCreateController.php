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

final class FireSecurityAdminCreateController extends AbstractController
{
    public function __construct(private FireSecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/fire-security/admin/create', name: "fire_secure_admin_create")]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $dto = $this->crud->createFireSecurityDto($request);

        if ($request->isMethod('post')) {

            $errors = $this->crud->validate($dto);

            if (count($errors) === 0) {
                $this->crud->create($dto);

                return $this->redirectToRoute('fire_secure_admin');
            }
        }

        return $this->render('crud/firesecurity/firesecurity.save.entity.html.twig', [
            'action' => 'create',
            'security' => $dto,
            'errors' => $errors ?? [],
        ]);
    }
}