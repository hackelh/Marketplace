<div>
  <div class="card mb-3">
    <div class="card-header d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between">
      <div class="d-flex gap-2 flex-wrap">
        <input type="text" class="form-control" style="min-width:260px" placeholder="Rechercher (nom, description)" wire:model.live.debounce.300ms="search">
        <input type="text" class="form-control" style="min-width:200px;max-width:220px" placeholder="#couleur (hex)" wire:model.live="couleur">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Nom</th>
              <th>Description</th>
              <th style="width:140px">Couleur</th>
              <th style="width:160px">Créée le</th>
            </tr>
          </thead>
          <tbody>
            @forelse($categories as $cat)
              <tr>
                <td class="fw-semibold">{{ $cat->nom }}</td>
                <td class="text-truncate" style="max-width:420px">{{ $cat->description }}</td>
                <td>
                  @if($cat->couleur_hex)
                    <span class="badge border" style="background: {{ $cat->couleur_hex }}">&nbsp;&nbsp;</span>
                    <span class="ms-2">{{ $cat->couleur_hex }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>{{ $cat->created_at?->format('d/m/Y H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4">Aucune catégorie trouvée.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      {{ $categories->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
