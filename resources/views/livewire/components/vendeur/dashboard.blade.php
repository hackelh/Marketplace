@extends('layouts.adminlte')

@section('title', 'Dashboard Vendeur')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Bienvenue, {{ auth()->user()->name }} !</h2>
            <div class="text-muted">Nous sommes le {{ now()->format('d/m/Y') }}</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                <i class="bi bi-person-circle fs-2 text-primary"></i>
            </div>
        </div>
    </div>

    <!-- Alerte des nouvelles commandes -->
    @php
        $nouvellesCommandes = \App\Models\Commande::where('vendeur_id', auth()->id())
            ->where('statut', 'en_attente')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    @endphp
    
    @if($nouvellesCommandes > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div>
                <strong>Nouvelles commandes !</strong>
                <span class="ms-2">Vous avez {{ $nouvellesCommandes }} nouvelle(s) commande(s) en attente de traitement.</span>
                <div class="mt-1">
                    <a href="{{ route('vendeur.commandes.en-cours') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-eye"></i> Voir les commandes
                    </a>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Statistiques Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-box fs-3 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ \App\Models\Tissu::where('user_id', auth()->id())->count() }}</div>
                        <div class="text-muted">Tissus</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-tags fs-3 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ \App\Models\Categorie::count() }}</div>
                        <div class="text-muted">Catégories</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-archive fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ \App\Models\Tissu::where('user_id', auth()->id())->sum('stock') }}</div>
                        <div class="text-muted">Stock total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-exclamation-triangle fs-3 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ \App\Models\Tissu::where('user_id', auth()->id())->where('stock', 0)->count() }}</div>
                        <div class="text-muted">Ruptures</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-4s">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-bag fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ \App\Models\Commande::where('vendeur_id', auth()->id())->where('statut', 'en_attente')->count() }}</div>
                        <div class="text-muted">Commandes en attente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">
                    Répartition des tissus par catégorie
                </div>
                <div class="card-body">
                    <canvas id="pieChart" style="min-height: 250px; height: 250px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">
                    État du stock
                </div>
                <div class="card-body">
                    <canvas id="barChart" style="min-height: 250px; height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides et derniers tissus -->
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Actions rapides</div>
                <div class="card-body d-flex flex-column gap-3">
                    <a href="{{ route('vendeur.ajouter-tissu') }}" class="btn btn-primary w-100">
                        <i class="bi bi-plus"></i> Ajouter un tissu
                    </a>
                    <a href="{{ route('vendeur.gestion-tissus') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-box"></i> Gérer les tissus
                    </a>
                    <a href="{{ route('vendeur.categories') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-tags"></i> Gérer les catégories
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Derniers tissus ajoutés</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Tissu::with('categorie')->where('user_id', auth()->id())->latest()->limit(5)->get() as $tissu)
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
                                    <td>{{ number_format($tissu->prix, 2) }} CFA</td>
                                    <td>
                                        @if($tissu->stock > 0)
                                            <span class="badge bg-success">{{ $tissu->stock }}</span>
                                        @else
                                            <span class="badge bg-danger">0</span>
                                        @endif
                                    </td>
                                    <td>{{ $tissu->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-box"></i> Aucun tissu ajouté récemment
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
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const categories = @json(\App\Models\Categorie::withCount(['tissus' => function($q){$q->where('user_id', auth()->id());}])->get());
const tissus = @json(\App\Models\Tissu::where('user_id', auth()->id())->get());

// Pie chart
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
    options: {responsive: true, maintainAspectRatio: false}
});
// Bar chart
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
        scales: {y: {beginAtZero: true}}
    }
});
</script>
<!-- Animation CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
