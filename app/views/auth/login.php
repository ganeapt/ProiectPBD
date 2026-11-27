<?php use App\Core\Security; ?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; }</style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-2">
                            <i class="bi bi-shield-lock-fill fs-2"></i>
                        </div>
                        <h4 class="fw-bold mb-1">ProiectPBD</h4>
                        <p class="text-muted small mb-0">Autentificare Securizată</p>
                    </div>

                    <?php if (!empty($flash)): ?>
                        <div class="alert alert-<?= e($flash['type']) ?> py-2 small"><?= e($flash['message']) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/login">
                        <?= Security::csrfField() ?>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Adresă Email</label>
                            <input type="email" name="email" class="form-control" placeholder="nume@organizatie.ro" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Parolă</label>
                            <input type="password" name="parola" class="form-control" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Intră în cont
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <span class="small text-muted">Nu ai cont?</span>
                        <a href="/register" class="small fw-semibold text-decoration-none ms-1">Înregistrează-te</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
