<?php

namespace App\Application\Http\Web\VisualNotification;

use App\Domain\Identity\Entity\User;
use App\Domain\VisualNotification\Service\VisualNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VisualNotificationListController extends AbstractController
{
    public function __construct(private VisualNotificationService $service)
    {
    }

    #[Route('/visual-notify/{type}', name: "visual_notify")]
    public function index(int $type): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        return $this->render('notification/index.html.twig', [
            'title' => 'Зафиксированные события',
            'type' => $type,
            'notifies' => $this->service->getNotifiesByType(type: $type === 99 ? null : $type),
        ]);
    }
}