<?php

namespace App\Application\Http\Web\Profile;

use App\Domain\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class ProfileViewController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    #[Route('/profile', name: "profile_view")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/profile/profile.view.html.twig', [
            'user' => $this->security->getUser(),
        ]);
    }
}