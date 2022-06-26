<?php


namespace App\Application\Http\Web\Security;


use App\Domain\Doctrine\Identity\Entity\User;
use App\Infrastructure\Doctrine\Service\Security\SecurityCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityDeleteController extends AbstractController
{
    public function __construct(private SecurityCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/security/delete/{id}', name: "security_delete_by_id")]
    public function delete(string $id): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->crud->delete($id);

        return $this->redirectToRoute('security');
    }
}