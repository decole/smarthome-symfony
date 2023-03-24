<?php

namespace App\Application\Http\Web\Security;

use App\Domain\Identity\Entity\User;
use App\Domain\Security\Entity\Security;
use App\Domain\Security\Service\SecurityCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityAdminUpdateController extends AbstractController
{
    public function __construct(private SecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/security/admin/update/{id}', name: "security_admin_update_by_id")]
    public function update(string $id, Request $request): Response
    {
        $errors = [];

        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $securityDto = $this->crud->entityByDto($id);

        if ($request->isMethod('post')) {
            $securityDto = $this->crud->createSecurityDto($request);

            $errors = $this->crud->validate($securityDto, true);

            if (count($errors) === 0) {
                $this->crud->update($id, $securityDto);

                return $this->redirectToRoute('security_admin');
            }
        }

        return $this->render('crud/security/security.save.entity.html.twig', [
            'action' => 'update',
            'entityId' => $id,
            'security' => $securityDto,
            'typeTranscribe' => Security::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}