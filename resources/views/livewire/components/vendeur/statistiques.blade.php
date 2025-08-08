<div class="row">
    <!-- Statistiques générales -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \App\Models\Tissu::where('user_id', auth()->id())->count() }}</h3>
                <p>Total Tissus</p>
            </div>
            <div class="icon">
                <i class="bi bi-box"></i>
            </div>
            <a href="{{ route('vendeur.gestion-tissus') }}" class="small-box-footer">
                Plus d'infos <i class="bi bi-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ \App\Models\Tissu::where('user_id', auth()->id())->where('stock', '>', 0)->count() }}</h3>
                <p>Tissus en Stock</p>
            </div>
            <div class="icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <a href="{{ route('vendeur.gestion-tissus') }}" class="small-box-footer">
                Plus d'infos <i class="bi bi-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ \App\Models\Tissu::where('user_id', auth()->id())->where('stock', 0)->count() }}</h3>
                <p>Ruptures de Stock</p>
            </div>
            <div class="icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <a href="{{ route('vendeur.gestion-tissus') }}" class="small-box-footer">
                Plus d'infos <i class="bi bi-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ \App\Models\Categorie::count() }}</h3>
                <p>Catégories</p>
            </div>
            <div class="icon">
                <i class="bi bi-tags"></i>
            </div>
            <a href="{{ route('vendeur.categories') }}" class="small-box-footer">
                Plus d'infos <i class="bi bi-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Graphique des tissus par catégorie -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pie-chart"></i>
                    Tissus par Catégorie
                </h3>
            </div>
            <div class="card-body">
                <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Graphique des stocks -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-bar-chart"></i>
                    État des Stocks
                </h3>
            </div>
            <div class="card-body">
                <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top 5 des tissus les plus chers -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Top 5 - Tissus les plus chers
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Tissu::where('user_id', auth()->id())->orderBy('prix', 'desc')->limit(5)->get() as $tissu)
                            <tr>
                                <td>{{ $tissu->nom }}</td>
                                <td>
                                    @if($tissu->categorie)
                                        <span class="badge" style="background-color: {{ $tissu->categorie->couleur_hex ?? '#007bff' }}; color: white;">
                                            {{ $tissu->categorie->nom }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($tissu->prix, 2) }} CFA</strong></td>
                                <td>
                                    @if($tissu->stock > 0)
                                        <span class="badge bg-success">{{ $tissu->stock }}</span>
                                    @else
                                        <span class="badge bg-danger">0</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tissus en rupture de stock -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    Tissus en Rupture de Stock
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Tissu::where('user_id', auth()->id())->where('stock', 0)->limit(5)->get() as $tissu)
                            <tr>
                                <td>{{ $tissu->nom }}</td>
                                <td>
                                    @if($tissu->categorie)
                                        <span class="badge" style="background-color: {{ $tissu->categorie->couleur_hex ?? '#007bff' }}; color: white;">
                                            {{ $tissu->categorie->nom }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ number_format($tissu->prix,) }FCFA} </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-warning">
                                        <i class="bi bi-plus"></i> Réapprovisionner
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-success">
                                    <i class="bi bi-check-circle"></i> Aucune rupture de stock !
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données pour les graphiques
const categories = @json(\App\Models\Categorie::withCount(['tissus' => function($query) { $query->where('user_id', auth()->id()); }])->get());
const tissus = @json(\App\Models\Tissu::where('user_id', auth()->id())->get());

// Graphique circulaire - Tissus par catégorie
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: categories.map(cat => cat.nom),
        datasets: [{
            data: categories.map(cat => cat.tissus_count),
            backgroundColor: [
                '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#17a2b8'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Graphique en barres - État des stocks
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ['En Stock', 'Rupture'],
        datasets: [{
            label: 'Nombre de tissus',
            data: [
                tissus.filter(t => t.stock > 0).length,
                tissus.filter(t => t.stock === 0).length
            ],
            backgroundColor: ['#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script> 