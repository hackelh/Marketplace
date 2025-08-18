<div>
  {{-- KPIs Row --}}
  <div class="row g-3">
    <div class="col-6 col-lg-2">
      <div class="small-box bg-primary">
        <div class="inner">
          <h3>{{ $stats['users'] ?? 0 }}</h3>
          <p>Utilisateurs</p>
        </div>
        <div class="icon"><i class="bi bi-people"></i></div>
        <a href="{{ route('admin.utilisateurs') }}" class="small-box-footer">Gérer <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-2">
      <div class="small-box bg-dark">
        <div class="inner">
          <h3>{{ $stats['admins'] ?? 0 }}</h3>
          <p>Admins</p>
        </div>
        <div class="icon"><i class="bi bi-shield-lock"></i></div>
        <a href="{{ route('admin.utilisateurs', ['role' => 'admin']) }}" class="small-box-footer text-light">Administration <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-2">
      <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ $stats['vendeurs'] ?? 0 }}</h3>
          <p>Vendeurs</p>
        </div>
        <div class="icon"><i class="bi bi-shop"></i></div>
        <a href="{{ route('admin.utilisateurs', ['role' => 'vendeur']) }}" class="small-box-footer text-dark">Voir vendeurs <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-2">
      <div class="small-box bg-secondary">
        <div class="inner">
          <h3>{{ $stats['tailleurs'] ?? 0 }}</h3>
          <p>Tailleurs</p>
        </div>
        <div class="icon"><i class="bi bi-scissors"></i></div>
        <a href="{{ route('admin.utilisateurs', ['role' => 'tailleur']) }}" class="small-box-footer text-light">Voir tailleurs <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-2">
      <div class="small-box bg-light">
        <div class="inner">
          <h3>{{ $stats['clients'] ?? 0 }}</h3>
          <p>Clients</p>
        </div>
        <div class="icon"><i class="bi bi-person-check"></i></div>
        <a href="{{ route('admin.utilisateurs', ['role' => 'client']) }}" class="small-box-footer text-dark">Voir clients <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-6 col-lg-2">
      <div class="small-box bg-warning">
        <div class="inner">
          <h3>{{ $stats['categories'] ?? 0 }}</h3>
          <p>Catégories</p>
        </div>
        <div class="icon"><i class="bi bi-tags"></i></div>
        <a href="{{ route('admin.categories') }}" class="small-box-footer">Gérer <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>

  {{-- Role Distribution & Growth --}}
  @php
    $roleCounts = $stats['role_counts'] ?? [
      'admin' => $stats['admins'] ?? 0,
      'vendeur' => $stats['vendeurs'] ?? 0,
      'tailleur' => $stats['tailleurs'] ?? 0,
      'client' => $stats['clients'] ?? 0,
    ];
    $totalUsers = max(1, (int)($stats['users'] ?? array_sum($roleCounts)));
    $growth7 = $stats['growth_7d'] ?? 0;
    $growth30 = $stats['growth_30d'] ?? 0;
    $verified = (int)($stats['verified_users'] ?? 0);
    $verifiedPct = min(100, round(($verified / $totalUsers) * 100));
  @endphp

  <div class="row g-3 mt-1">
    <div class="col-lg-7">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Répartition des rôles</strong>
          <span class="text-muted small">Total: {{ $totalUsers }}</span>
        </div>
        <div class="card-body">
          @foreach(['admin' => 'dark', 'vendeur' => 'info', 'tailleur' => 'secondary', 'client' => 'light'] as $roleName => $color)
            @php
              $count = (int)($roleCounts[$roleName] ?? 0);
              $pct = $totalUsers ? round(($count / $totalUsers) * 100) : 0;
            @endphp
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="fw-semibold text-capitalize">{{ $roleName }}</span>
                <span class="text-muted">{{ $count }} ({{ $pct }}%)</span>
              </div>
              <div class="progress" style="height:10px;">
                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header"><strong>Croissance & Qualité</strong></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6">
              <div class="p-3 border rounded h-100">
                <div class="text-muted small">Croissance 7j</div>
                <div class="fs-4 fw-bold {{ $growth7 >= 0 ? 'text-success' : 'text-danger' }}">
                  {{ $growth7 >= 0 ? '+' : '' }}{{ $growth7 }}
                </div>
                <div class="small text-muted">Nouveaux utilisateurs</div>
              </div>
            </div>
            <div class="col-6">
              <div class="p-3 border rounded h-100">
                <div class="text-muted small">Croissance 30j</div>
                <div class="fs-4 fw-bold {{ $growth30 >= 0 ? 'text-success' : 'text-danger' }}">
                  {{ $growth30 >= 0 ? '+' : '' }}{{ $growth30 }}
                </div>
                <div class="small text-muted">Nouveaux utilisateurs</div>
              </div>
            </div>
            <div class="col-12">
              <div class="p-3 border rounded">
                <div class="d-flex justify-content-between mb-2">
                  <div>
                    <div class="text-muted small">Utilisateurs vérifiés</div>
                    <div class="fw-semibold">{{ $verified }} / {{ $totalUsers }} ({{ $verifiedPct }}%)</div>
                  </div>
                  <div class="text-end">
                    <i class="bi bi-patch-check-fill text-success fs-3"></i>
                  </div>
                </div>
                <div class="progress" style="height:10px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $verifiedPct }}%" aria-valuenow="{{ $verifiedPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Section liée aux commandes retirée sur demande --}}

  {{-- Recent Admins (si disponible) --}}
  @if(!empty($recentAdmins ?? []))
    <div class="row g-3 mt-1">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Administrateurs récents</strong>
            <a href="{{ route('admin.utilisateurs') }}" class="btn btn-sm btn-outline-primary">Voir tous</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Créé le</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($recentAdmins as $a)
                    <tr>
                      <td class="fw-semibold">{{ $a->name }}</td>
                      <td>{{ $a->email }}</td>
                      <td>{{ $a->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
