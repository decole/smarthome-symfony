<?php

namespace App\Application\Http\Web\Security;

use App\Domain\Identity\Entity\User;
use App\Domain\Security\Entity\Security;
use App\Infrastructure\Doctrine\Service\Security\SecurityCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityAdminCreateController extends AbstractController
{
    public function __construct(private SecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/security/admin/create', name: "security_admin_create")]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $securityDto = $this->crud->createSecurityDto($request);

        if ($request->isMethod('post')) {

            $errors = $this->crud->validate($securityDto);

            if (count($errors) === 0) {
                $this->crud->create($securityDto);

                return $this->redirectToRoute('security_admin');
            }
        }

        return $this->render('crud/security/security.save.entity.html.twig', [
            'action' => 'create',
            'security' => $securityDto,
            'typeTranscribe' => Security::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}