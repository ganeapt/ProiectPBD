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
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-1">Creare Cont Solicitant</h4>
                        <p class="text-muted small mb-0">ProiectPBD - Gestiune Proiecte Europene</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/register">
                        <?= Security::csrfField() ?>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Nume</label>
                                <input type="text" name="nume" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Prenume</label>
                                <input type="text" name="prenume" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Organizație</label>
                            <select name="id_org" class="form-select" required>
                                <?php foreach ($organizatii as $o): ?>
                                    <option value="<?= (int)$o['id_org'] ?>"><?= e($o['denumire']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Parolă (min. 8 caractere)</label>
                                <input type="password" name="parola" class="form-control" minlength="8" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Confirmă Parola</label>
                                <input type="password" name="parola_confirm" class="form-control" minlength="8" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">Înregistrează Cont</button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <span class="small text-muted">Ai deja cont?</span>
                        <a href="/login" class="small fw-semibold text-decoration-none ms-1">Autentifică-te</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
