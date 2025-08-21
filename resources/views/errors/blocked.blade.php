<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Compte bloqué</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/AdminLTE-4.0.0-rc4/dist/css/adminlte.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body class="bg-body-tertiary d-flex align-items-center" style="min-height:100vh;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body p-5 text-center">
            <i class="bi bi-slash-circle text-danger" style="font-size:3rem;"></i>
            <h1 class="mt-3">Compte bloqué</h1>
            <p class="text-muted">Votre compte a été bloqué par l’administration. Veuillez contacter le support si vous pensez qu’il s’agit d’une erreur.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">
              <i class="bi bi-box-arrow-in-right me-1"></i> Retour à la connexion
            </a>
          </div>
        </div>
        <p class="text-center text-muted mt-3">Code erreur: 403</p>
      </div>
    </div>
  </div>
</body>
</html>