<?php

namespace App\Application\Http\Web\VisualNotification;

use App\Application\Http\Web\VisualNotification\Dto\VisualNotificationHistoryInputDto;
use App\Domain\VisualNotification\Service\VisualNotificationHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VisualNotifyHistoryController extends AbstractController
{
    public function __construct(private readonly VisualNotificationHistoryService $service)
    {
    }

    #[Route('/history/visual-notify', name: 'notify-history')]
    public function history(Request $request): Response
    {
        $dto = new VisualNotificationHistoryInputDto();

        $dto->page = $request->get('page', 1);

        $dto = $this->service->paginate($dto);

        return $this->render('notification/history.html.twig', [
            'notifies' => $dto->collection,
            'prev' => $dto->prev,
            'next' => $dto->next,
            'page' => $dto->current,
            'pageCount' => $dto->count,
        ]);
    }
}