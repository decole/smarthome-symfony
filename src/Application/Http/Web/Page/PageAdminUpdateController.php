<?php

declare(strict_types=1);

namespace App\Application\Http\Web\Page;

use App\Domain\Identity\Entity\User;
use App\Domain\Page\Service\PageCrudService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PageAdminUpdateController extends AbstractController
{
    public function __construct(private readonly PageCrudService $crud)
    {
    }

    #[Route('/pages/admin/update/{id}', name: "page_admin_update_by_id")]
    public function update(string $id, Request $request): Response
    {
        $errors = [];

        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $dto = $this->crud->entityByDto($id);

        if ($request->isMethod('post')) {
            $dto = $this->crud->createDto($request);

            $errors = $this->crud->validate($dto, true);

            if (count($errors) === 0) {
                $this->crud->update($id, $dto);

                return $this->redirectToRoute('page_admin');
            }
        }

        return $this->render('crud/page/page.save.entity.html.twig', [
            'action' => 'update',
            'entityId' => $id,
            'pageName' => $dto->name,
            'pageAlias' => $dto->alias,
            'pageIcon' => $dto->icon,
            'pageGroup' => $dto->groupId,
            'deviceList' => $this->crud->getSelectedDeviceList($dto),
            'errors' => $errors ?? [],
        ]);
    }
}