<?php

namespace App\Application\Http\Web\Page;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\PageCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageAdminCreateController extends AbstractController
{
    public function __construct(private PageCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/pages/admin/create', name: "page_admin_create")]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $dto = $this->crud->createDto($request);

        if ($request->isMethod('post')) {

            $errors = $this->crud->validate($dto);

            if (count($errors) === 0) {
                $this->crud->create($dto);

                return $this->redirectToRoute('page_admin');
            }
        }

        return $this->render('crud/page/page.save.entity.html.twig', [
            'action' => 'create',
            'pageName' => $dto->name,
            'pageAlias' => $dto->alias,
            'pageIcon' => $dto->icon,
            'pageGroup' => $dto->groupId,
            'deviceList' => $this->crud->getSelectedDeviceList($dto),
            'errors' => $errors ?? [],
        ]);
    }
}