<?php

namespace App\Traits;

use App\Models\HistoriqueTissu;

trait HasHistorique
{
    /**
     * Enregistrer un mouvement dans l'historique
     */
    public function enregistrerMouvement(
        string $typeMouvement,
        int $quantiteMouvement,
        string $motif = null,
        string $reference = null,
        string $notes = null
    ): void {
        $quantiteAvant = $this->stock;
        $quantiteApres = $this->stock + $quantiteMouvement;

        HistoriqueTissu::create([
            'tissu_id' => $this->id,
            'user_id' => auth()->id(),
            'type_mouvement' => $typeMouvement,
            'quantite_avant' => $quantiteAvant,
            'quantite_apres' => $quantiteApres,
            'quantite_mouvement' => $quantiteMouvement,
            'motif' => $motif,
            'reference' => $reference,
            'notes' => $notes,
        ]);

        // Mettre à jour le stock
        $this->update(['stock' => $quantiteApres]);
    }

    /**
     * Ajouter du stock
     */
    public function ajouterStock(int $quantite, string $motif = null, string $reference = null, string $notes = null): void
    {
        $this->enregistrerMouvement('ajout', $quantite, $motif, $reference, $notes);
    }

    /**
     * Diminuer le stock (vente)
     */
    public function diminuerStock(int $quantite, string $motif = null, string $reference = null, string $notes = null): void
    {
        $this->enregistrerMouvement('vente', -$quantite, $motif, $reference, $notes);
    }

    /**
     * Ajuster le stock (inventaire, correction)
     */
    public function ajusterStock(int $nouvelleQuantite, string $motif = null, string $notes = null): void
    {
        $difference = $nouvelleQuantite - $this->stock;
        $typeMouvement = $difference > 0 ? 'ajustement' : 'ajustement';
        
        $this->enregistrerMouvement($typeMouvement, $difference, $motif, null, $notes);
    }

    /**
     * Enregistrer la création du tissu
     */
    public static function enregistrerCreation(Tissu $tissu): void
    {
        HistoriqueTissu::create([
            'tissu_id' => $tissu->id,
            'user_id' => auth()->id(),
            'type_mouvement' => 'creation',
            'quantite_avant' => 0,
            'quantite_apres' => $tissu->stock,
            'quantite_mouvement' => $tissu->stock,
            'motif' => 'Création du tissu',
            'notes' => "Tissu créé : {$tissu->nom}",
        ]);
    }

    /**
     * Enregistrer la modification du tissu
     */
    public function enregistrerModification(array $changements): void
    {
        $notes = [];
        foreach ($changements as $champ => $valeur) {
            if ($champ !== 'stock' && $this->getOriginal($champ) !== $valeur) {
                $notes[] = "{$champ} : {$this->getOriginal($champ)} → {$valeur}";
            }
        }

        if (!empty($notes)) {
            HistoriqueTissu::create([
                'tissu_id' => $this->id,
                'user_id' => auth()->id(),
                'type_mouvement' => 'modification',
                'quantite_avant' => $this->getOriginal('stock'),
                'quantite_apres' => $this->stock,
                'quantite_mouvement' => $this->stock - $this->getOriginal('stock'),
                'motif' => 'Modification du tissu',
                'notes' => implode(', ', $notes),
            ]);
        }
    }

    /**
     * Obtenir l'historique complet du tissu
     */
    public function getHistoriqueComplet()
    {
        return $this->historique()
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir l'historique par type de mouvement
     */
    public function getHistoriqueParType(string $type)
    {
        return $this->historique()
            ->where('type_mouvement', $type)
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtenir l'historique par période
     */
    public function getHistoriqueParPeriode($debut, $fin)
    {
        return $this->historique()
            ->whereBetween('created_at', [$debut, $fin])
            ->with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->get();
    }
} 