<?php

namespace ClasseTechnique;

class FilAriane
{
    /**
     * Liste des éléments du fil
     * @var array<int, array{label: string, href: ?string}>
     */
    private array $lesFils = [];

    /**
     * Chemin courant découpé
     * @var array<int, string>
     */
    private array $lesSegments = [];

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->initialiserSegments();
        $this->initialiserFil();
    }

    /**
     * Initialise les segments depuis l'URL courante
     */
    private function initialiserSegments(): void
    {
        $cheminCourant = str_replace(
            '\\',
            '/',
            trim(dirname($_SERVER['PHP_SELF']), '/')
        );

        $this->lesSegments = ($cheminCourant === '')
            ? []
            : array_values(
                array_filter(
                    explode('/', $cheminCourant),
                    static fn($segment) => $segment !== ''
                )
            );
    }

    /**
     * Initialise le fil d'Ariane
     */
    private function initialiserFil(): void
    {
        // Racine
        $this->lesFils[] = [
            'label' => 'Menu',
            'href'  => '/'
        ];

        foreach ($this->lesSegments as $indice => $segment) {

            $label = ucfirst(str_replace(['-', '_'], ' ', $segment));

            $this->lesFils[] = [
                'label' => $label,
                'href'  => null
            ];
        }
    }

    /**
     * Permet de remplacer le dernier élément par un titre personnalisé
     */
    public function definirTitreFinal(?string $titre): self
    {
        if (!empty($titre) && !empty($this->lesFils)) {
            $this->lesFils[array_key_last($this->lesFils)]['label'] = $titre;
        }

        return $this;
    }

    /**
     * Retourne le fil complet
     */
    public function obtenir(): array
    {
        return $this->lesFils;
    }

    /**
     * Affichage HTML direct (optionnel mais pratique)
     */
    public function afficher(): void
    {
        foreach ($this->lesFils as $indice => $element) {

            if ($indice > 0) {
                echo '<span> &gt; </span>';
            }

            if ($element['href'] !== null) {
                echo '<a href="' . htmlspecialchars($element['href']) . '" style="color:white;text-decoration:none;">'
                    . htmlspecialchars($element['label']) .
                    '</a>';
            } else {
                echo '<span>' . htmlspecialchars($element['label']) . '</span>';
            }
        }
    }
}