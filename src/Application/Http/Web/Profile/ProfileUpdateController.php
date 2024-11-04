<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Profile;

use App\Domain\Identity\Entity\User;
use App\Domain\Profile\Service\ProfileCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileUpdateController extends AbstractController
{
    public function __construct(private readonly ProfileCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/user/profile/update', name: "profile_update",methods: ['POST'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        if ($request->isMethod('post')) {
            $user = $this->getUser();

            assert($user instanceof User);

            $dto = $this->crud->createDto($user->getLogin(), $request);

            $errors = $this->crud->validate($dto);

            if (count($errors) === 0) {
                $this->crud->update($user, $dto);
            }
        }

        return $this->redirectToRoute('profile_view');
    }
}