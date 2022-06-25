<?php


namespace App\Application\Http\Web\Relay;


use App\Domain\Doctrine\Identity\Entity\User;
use App\Domain\Doctrine\Relay\Entity\Relay;
use App\Infrastructure\Doctrine\Service\Relay\RelayCrudService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RelayCreateController extends AbstractController
{
    public function __construct(private RelayCrudService $crud)
    {
    }

    /**
     * @throws OptimisticLockException|ORMException
     */
    #[Route('/relays/create', name: "relays_create")]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $relayDto = $this->crud->createRelayDto($request);

        if ($request->isMethod('post')) {

            $errors = $this->crud->validate($relayDto);

            if (count($errors) === 0) {
                $this->crud->create($relayDto);

                return $this->redirectToRoute('relays');
            }
        }

        return $this->render('relay/relay.save.entity.html.twig', [
            'action' => 'create',
            'relay' => $relayDto,
            'typeTranscribe' => Relay::TYPE_TRANSCRIBES,
            'errors' => $errors ?? [],
        ]);
    }
}