<div>
  <div class="card mb-3">
    <div class="card-header d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between">
      <div class="d-flex gap-2 flex-wrap">
        <input type="text" class="form-control" style="min-width:260px" placeholder="Rechercher (N° commande, client, vendeur, tailleur)" wire:model.live.debounce.300ms="search">
        <select class="form-select" wire:model.live="statut">
          <option value="">Tous les statuts</option>
          <option value="en_attente">En attente</option>
          <option value="en_preparation">En préparation</option>
          <option value="en_couture">En couture</option>
          <option value="pret">Prêt</option>
          <option value="livree">Livrée</option>
          <option value="terminee">Terminée</option>
          <option value="annulee">Annulée</option>
        </select>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:140px">N°</th>
              <th>Client</th>
              <th>Vendeur</th>
              <th>Tailleur</th>
              <th style="width:120px">Montant</th>
              <th style="width:130px">Statut</th>
              <th style="width:160px">Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($commandes as $c)
              <tr>
                <td class="fw-semibold">{{ $c->numero_commande }}</td>
                <td>{{ $c->client?->name ?? '-' }}</td>
                <td>{{ $c->vendeur?->name ?? '-' }}</td>
                <td>{{ $c->tailleur?->name ?? '-' }}</td>
                <td>{{ number_format((float)$c->montant_total, 2, ',', ' ') }} FCFA</td>
                <td>
                  <span class="badge text-bg-{{ $c->statut_couleur }}">{{ $c->statut_libelle }}</span>
                </td>
                <td>{{ $c->date_commande?->format('d/m/Y H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4">Aucune commande trouvée.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      {{ $commandes->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
