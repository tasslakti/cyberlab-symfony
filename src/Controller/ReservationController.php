<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\LabRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservations', name: 'app_reservations')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $user = $this->getUser();

        // Si admin ou instructeur, voir toutes les réservations
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_INSTRUCTOR')) {
            $reservations = $reservationRepository->findAllOrderedByDate();
        } else {
            // Sinon, voir seulement ses réservations
            $reservations = $reservationRepository->findByUser($user);
        }

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservations/pending', name: 'app_reservations_pending')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function pendingReservations(ReservationRepository $reservationRepository): Response
    {
        $pendingReservations = $reservationRepository->findPendingReservations();

        return $this->render('reservation/pending.html.twig', [
            'reservations' => $pendingReservations,
        ]);
    }

    #[Route('/lab/{id}/reserve', name: 'app_reservation_create')]
    public function create(Request $request, LabRepository $labRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $lab = $labRepository->find($id);

        if (!$lab) {
            throw $this->createNotFoundException('Lab non trouvé');
        }

        $reservation = new Reservation();
        $reservation->setLab($lab);
        $reservation->setUser($this->getUser());

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation créée avec succès! En attente de confirmation.');

            return $this->redirectToRoute('app_reservations');
        }

        return $this->render('reservation/create.html.twig', [
            'form' => $form->createView(),
            'lab' => $lab,
        ]);
    }

    #[Route('/reservation/{id}/confirm', name: 'app_reservation_confirm')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function confirm(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if (!$reservation->canBeConfirmed()) {
            $this->addFlash('error', 'Cette réservation ne peut pas être confirmée.');
            return $this->redirectToRoute('app_reservations');
        }

        $reservation->setStatus(Reservation::STATUS_CONFIRMED);
        $reservation->setConfirmedBy($this->getUser());
        $reservation->setConfirmedAt(new \DateTime());

        $entityManager->flush();

        $this->addFlash('success', 'Réservation confirmée avec succès!');

        return $this->redirectToRoute('app_reservations');
    }

    #[Route('/reservation/{id}/cancel', name: 'app_reservation_cancel')]
    public function cancel(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Vérifier les permissions
        if ($reservation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_INSTRUCTOR')) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }

        if (!$reservation->canBeCancelled()) {
            $this->addFlash('error', 'Cette réservation ne peut pas être annulée.');
            return $this->redirectToRoute('app_reservations');
        }

        $reservation->setStatus(Reservation::STATUS_CANCELLED);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation annulée avec succès!');

        return $this->redirectToRoute('app_reservations');
    }

    #[Route('/reservation/{id}', name: 'app_reservation_show')]
    public function show(Reservation $reservation): Response
    {
        // Vérifier les permissions
        if ($reservation->getUser() !== $this->getUser() && !$this->isGranted('ROLE_INSTRUCTOR')) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/reservation/{id}/edit', name: 'app_reservation_edit')]
    #[IsGranted('ROLE_INSTRUCTOR')]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Réservation modifiée avec succès!');

            return $this->redirectToRoute('app_reservation_show', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/edit.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }
}