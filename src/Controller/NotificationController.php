<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'notification_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $notifications = $em->getRepository(Notification::class)->findBy(['user' => $user]);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/create', name: 'notification_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $notification = new Notification();
        $notification->setText("Nouvelle réservation !");
        $notification->setDatetime(new \DateTime());
        $notification->setLu(false);
        $notification->setUser($this->getUser());

        $em->persist($notification);
        $em->flush();

        $this->addFlash('success', 'Notification créée !');

        return $this->redirectToRoute('notification_index');
    }
}
