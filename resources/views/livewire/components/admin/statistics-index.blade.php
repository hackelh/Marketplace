<div>
  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><strong>Commandes par statut</strong></div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @php $labels = ['en_attente' => 'En attente', 'en_preparation' => 'En préparation', 'en_couture' => 'En couture', 'pret' => 'Prêt', 'livree' => 'Livrée', 'terminee' => 'Terminée', 'annulee' => 'Annulée']; @endphp
            @foreach($labels as $k => $label)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $label }}
                <span class="badge rounded-pill text-bg-secondary">{{ $byStatus[$k] ?? 0 }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><strong>Chiffre d'affaires (6 derniers mois)</strong></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Mois</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                @forelse($monthlyRevenue as $r)
                  <tr>
                    <td>{{ $r['ym'] }}</td>
                    <td class="text-end">{{ number_format($r['total'], 0, ',', ' ') }} FCFA</td>
                  </tr>
                @empty
                  <tr><td colspan="2" class="text-center py-3">Aucune donnée</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><strong>Top vendeurs (par commandes)</strong></div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead class="table-light"><tr><th>Vendeur</th><th class="text-end">Commandes</th></tr></thead>
              <tbody>
                @forelse($topVendors as $v)
                  <tr><td>{{ $v['name'] }}</td><td class="text-end">{{ $v['total'] }}</td></tr>
                @empty
                  <tr><td colspan="2" class="text-center py-3">Aucune donnée</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header"><strong>Répartition tissus par catégorie</strong></div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead class="table-light"><tr><th>Catégorie</th><th class="text-end">Tissus</th></tr></thead>
              <tbody>
                @forelse($categoriesDistribution as $c)
                  <tr><td>{{ $c['nom'] }}</td><td class="text-end">{{ $c['tissus_count'] }}</td></tr>
                @empty
                  <tr><td colspan="2" class="text-center py-3">Aucune donnée</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
