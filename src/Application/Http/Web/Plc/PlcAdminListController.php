<?php

namespace App\Application\Http\Web\Plc;

use App\Domain\Identity\Entity\User;
use App\Domain\PLC\Service\PlcCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PlcAdminListController extends AbstractController
{
    public function __construct(private PlcCrudService $crud)
    {
    }

    #[Route('/plc/admin', name: "plc_admin")]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('crud/plc/plc.list.html.twig', [
            'plc_list' => $this->crud->list(),
        ]);
    }
}