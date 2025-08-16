<!doctype html>
<html lang="fr">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>
    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->
    <!--begin::Accessibility Features-->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="/AdminLTE-4.0.0-rc4/dist/css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="/AdminLTE-4.0.0-rc4/dist/css/adminlte.css" />
    <!--end::Required Plugin(AdminLTE)-->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="{{ route('admin.dashboard') }}" class="nav-link">Accueil</a></li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle fs-4"></i>
                <span class="d-none d-md-inline ms-2">{{ auth()->user()->name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <i class="bi bi-person-circle fs-1"></i>
                  <p>
                    {{ auth()->user()->name }} - Admin
                    <small>Membre depuis {{ auth()->user()->created_at->format('M. Y') }}</small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-default btn-flat float-end">Déconnexion</button>
                  </form>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="/AdminLTE-4.0.0-rc4/dist/assets/img/AdminLTELogo.png"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Marketplace Admin</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              aria-label="Main navigation"
              data-accordion="false"
              id="navigation"
            >
              <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.utilisateurs', false) ?? '#' }}" class="nav-link {{ request()->routeIs('admin.utilisateurs') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-people"></i>
                  <p>Utilisateurs</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.categories', false) ?? '#' }}" class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-tags"></i>
                  <p>Catégories</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.tissus', false) ?? '#' }}" class="nav-link {{ request()->routeIs('admin.tissus') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-box"></i>
                  <p>Tissus</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.commandes', false) ?? '#' }}" class="nav-link {{ request()->routeIs('admin.commandes') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-bag"></i>
                  <p>Commandes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.statistiques', false) ?? '#' }}" class="nav-link {{ request()->routeIs('admin.statistiques') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-graph-up"></i>
                  <p>Statistiques</p>
                </a>
              </li>
            </ul>
            <!--end::Sidebar Menu-->

          </nav>
            
        </div>
        <!--end::Sidebar Wrapper-->
        
        <!-- Footer du sidebar avec bouton de déconnexion -->
        <footer class="sidebar-footer p-3 border-top bg-dark mt-auto" style="position:sticky;bottom:0;width:100%;z-index:10;">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center py-3 fw-bold text-white">
                    <i class="bi bi-box-arrow-right me-2 fs-5 text-white"></i> 
                    DÉCONNEXION
                </button>
            </form>
        </footer>
  
      </aside>
      <!--end::Sidebar-->
      <!--begin::App Content-->
      <div class="app-content">
        <!--begin::Content Header-->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">@yield('title', 'Dashboard')</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Accueil</a></li>
                  <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb', 'Dashboard')</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!--end::Content Header-->
        <!--begin::Content-->
        <div class="content">
          <div class="container-fluid">
            @yield('content')
          </div>
        </div>
        <!--end::Content-->
      </div>
      <!--end::App Content-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Vendor Scripts (required before AdminLTE)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <!--end::Vendor Scripts-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="/AdminLTE-4.0.0-rc4/dist/js/adminlte.js"></script>
    <!--end::Required Plugin(AdminLTE)-->
    @livewireScripts
    @stack('scripts')
    
    <!-- Hot Reload pour le développement -->
    @if(config('app.debug'))
        @viteReactRefresh
    @endif
  </body>
  <!--end::Body-->
</html>