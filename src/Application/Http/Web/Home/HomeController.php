<?php

namespace App\Application\Http\Web\Home;

use App\Domain\Doctrine\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: "home")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $number = random_int(0, 100);

        return $this->render('home/index.html.twig', [
            'number' => $number,
        ]);
    }
}