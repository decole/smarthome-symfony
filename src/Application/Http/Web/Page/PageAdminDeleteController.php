<?php

namespace App\Application\Http\Web\Page;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\PageCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageAdminDeleteController extends AbstractController
{
    public function __construct(private PageCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/pages/admin/delete/{id}', name: "page_admin_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('page_admin');
    }
}