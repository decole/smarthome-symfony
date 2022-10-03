<?php

namespace App\Application\Http\Web\VisualNotification;

use App\Application\Service\VisualNotification\VisualNotificationService;
use App\Domain\Doctrine\Identity\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VisualNotificationUpdateController extends AbstractController
{
    public function __construct(private VisualNotificationService $service)
    {
    }

    #[Route('/visual-notify/change-status/{type}', name: "visual_notify_update_by_type")]
    public function update(int $type): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_USER);

        $this->service->setIsRead($type === 99 ? null : $type);

        return $this->redirectToRoute('visual_notify', ['type' => $type]);
    }
}