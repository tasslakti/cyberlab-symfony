<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * L'entité Categorie représente une catégorie de lab sur la plateforme CyberLab.
 */
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    // L'identifiant unique de la catégorie
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Le nom de la catégorie
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    // -----------------------------
    // GETTER et SETTER
    // -----------------------------

    // Retourne l'id de la catégorie
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter pour l'id (normalement on ne l'utilise pas, Doctrine gère ça automatiquement)
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    // Retourne le nom de la catégorie
    public function getNom(): ?string
    {
        return $this->nom;
    }

    // Définit le nom de la catégorie
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // -----------------------------
    // Fonctions selon ton diagramme
    // -----------------------------

    // Créer une catégorie (à utiliser depuis un controller)
    public function creerCategorie(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    // Modifier le nom de la catégorie
    public function modifierCategorie(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

}
