<div>
  <div class="row g-3">
    <div class="col-6 col-lg-3">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>{{ $stats['users'] ?? 0 }}</h3>
          <p>Utilisateurs</p>
        </div>
        <div class="icon"><i class="bi bi-people"></i></div>
        <a href="{{ route('admin.utilisateurs') }}" class="small-box-footer">Voir plus <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="small-box bg-success">
        <div class="inner">
          <h3>{{ $stats['tissus'] ?? 0 }}</h3>
          <p>Tissus</p>
        </div>
        <div class="icon"><i class="bi bi-box"></i></div>
        <a href="{{ route('admin.tissus') }}" class="small-box-footer">Voir plus <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>{{ $stats['categories'] ?? 0 }}</h3>
          <p>Catégories</p>
        </div>
        <div class="icon"><i class="bi bi-tags"></i></div>
        <a href="{{ route('admin.categories') }}" class="small-box-footer">Voir plus <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>{{ $stats['commandes'] ?? 0 }}</h3>
          <p>Commandes</p>
        </div>
        <div class="icon"><i class="bi bi-bag"></i></div>
        <a href="{{ route('admin.commandes') }}" class="small-box-footer">Voir plus <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><strong>Commandes récentes</strong></div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>N°</th>
                  <th>Client</th>
                  <th>Vendeur</th>
                  <th>Statut</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentOrders as $o)
                  <tr>
                    <td class="fw-semibold">{{ $o->numero_commande }}</td>
                    <td>{{ $o->client?->name ?? '-' }}</td>
                    <td>{{ $o->vendeur?->name ?? '-' }}</td>
                    <td><span class="badge text-bg-secondary">{{ $o->statut_libelle }}</span></td>
                    <td>{{ $o->date_commande?->format('d/m/Y H:i') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4">Aucune commande récente.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><strong>Résumé commandes</strong></div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              En attente
              <span class="badge text-bg-warning rounded-pill">{{ $stats['en_attente'] ?? 0 }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Terminées
              <span class="badge text-bg-success rounded-pill">{{ $stats['terminees'] ?? 0 }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
