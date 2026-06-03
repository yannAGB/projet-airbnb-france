<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_notification_')]
final class ApiNotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {}

    /* -------------------------------------------------- */
    /*         GET /api/notifications/latest              */
    /* -------------------------------------------------- */
    #[Route('/notifications/latest', name: 'latest', methods: ['GET'])]
    public function latest(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Non authentifié'],
                Response::HTTP_UNAUTHORIZED);
        }

        $notification = $this->notificationRepository->findLatestForUser($user);

        if (!$notification) {
            return $this->json(['success' => true, 'data' => null], Response::HTTP_OK);
        }

        $sender    = $notification->getSender();
        $initiales = strtoupper(
            substr($sender?->getFirstName() ?? '?', 0, 1) .
            substr($sender?->getLastName()  ?? '?', 0, 1)
        );

        return $this->json([
            'success' => true,
            'data'    => [
                'id'         => $notification->getId(),
                'sender'     => [
                    'id'        => $sender?->getId(),
                    'firstName' => $sender?->getFirstName(),
                    'lastName'  => $sender?->getLastName(),
                    'initiales' => $initiales,
                ],
                'title'      => $notification->getTitle(),
                'content'    => $notification->getContent(),
                'is_read'    => $notification->isRead(),
                'created_at' => $notification->getCreatedAt()?->format('Y-m-d H:i:s'),
            ],
        ], Response::HTTP_OK);
    }

    /* -------------------------------------------------- */
    /*         GET /api/notifications/count               */
    /* -------------------------------------------------- */
    #[Route('/notifications/count', name: 'count', methods: ['GET'])]
    public function count(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'Non authentifié'],
                Response::HTTP_UNAUTHORIZED);
        }

        $count = $this->notificationRepository->countUnreadForUser($user);

        return $this->json([
            'success' => true,
            'data'    => ['count' => $count],
        ], Response::HTTP_OK);
    }
}