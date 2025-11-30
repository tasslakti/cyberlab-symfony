<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    // Lister toutes les catégories
    #[Route('/', name: 'categorie_index')]
    public function index(CategorieRepository $repo): Response
    {
        $categories = $repo->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }

    // Créer une catégorie
    #[Route('/new', name: 'categorie_new')]
    public function creerCategorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Modifier une catégorie
    #[Route('/edit/{id}', name: 'categorie_edit')]
    public function modifierCategorie(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/edit.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    // Supprimer une catégorie
    #[Route('/delete/{id}', name: 'categorie_delete')]
    public function supprimerCategorie(Categorie $categorie, EntityManagerInterface $em): Response
    {
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('categorie_index');
    }

    // Lister les labs d'une catégorie (si relation existante)
    #[Route('/{id}/labs', name: 'categorie_labs')]
    public function listerLabs(Categorie $categorie): Response
    {
        $labs = $categorie->getLabs(); // nécessite OneToMany avec Lab
        return $this->render('categorie/labs.html.twig', [
            'categorie' => $categorie,
            'labs' => $labs
        ]);
    }
}
