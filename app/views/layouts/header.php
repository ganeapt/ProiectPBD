<?php
use App\Core\Security;
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'ProiectPBD') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); color: #e2e8f0; }
        .sidebar .nav-link { color: #94a3b8; border-radius: 8px; margin: 2px 10px; padding: 10px 14px; font-weight: 500; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: rgba(59, 130, 246, 0.18); color: #ffffff; }
        .sidebar .nav-link i { margin-right: 10px; font-size: 1.1rem; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .card-header { background-color: #ffffff; border-bottom: 1px solid #f1f5f9; font-weight: 600; border-radius: 12px 12px 0 0 !important; }
        .kpi-card { transition: transform 0.2s; }
        .kpi-card:hover { transform: translateY(-3px); }
        .table th { font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; background-color: #f8fafc; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php if (!empty($user)): ?>
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse py-3 px-0">
            <div class="px-3 mb-4">
                <a href="/" class="text-decoration-none text-white d-flex align-items-center">
                    <i class="bi bi-bank2 fs-4 text-primary me-2"></i>
                    <div>
                        <div class="fw-bold fs-5 lh-1">ProiectPBD</div>
                        <small class="text-secondary" style="font-size:0.72rem;">Fonduri Europene</small>
                    </div>
                </a>
            </div>
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/' ? 'active' : '' ?>" href="/"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array($currentPath, ['/proiecte', '/proiect']) ? 'active' : '' ?>" href="/proiecte"><i class="bi bi-folder2-open"></i> Proiecte</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/apeluri' ? 'active' : '' ?>" href="/apeluri"><i class="bi bi-megaphone"></i> Apeluri Finanțare</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/rambursari' ? 'active' : '' ?>" href="/rambursari"><i class="bi bi-cash-stack"></i> Cereri Rambursare</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/evaluari' ? 'active' : '' ?>" href="/evaluari"><i class="bi bi-clipboard-check"></i> Evaluări</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/organizatii' ? 'active' : '' ?>" href="/organizatii"><i class="bi bi-building"></i> Organizații &amp; Utilizatori</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === '/rapoarte' ? 'active' : '' ?>" href="/rapoarte"><i class="bi bi-bar-chart-line"></i> Rapoarte &amp; Audit</a>
                </li>
            </ul>
            <hr class="border-secondary mx-3">
            <div class="px-3 py-2">
                <div class="small text-secondary">Autentificat ca:</div>
                <div class="fw-semibold text-white"><?= e($user['prenume'] . ' ' . $user['nume']) ?></div>
                <span class="badge bg-primary mt-1"><?= e(str_replace('_', ' ', $user['rol'])) ?></span>
                <form method="POST" action="/logout" class="mt-3">
                    <?= Security::csrfField() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm w-100">
                        <i class="bi bi-box-arrow-right"></i> Deconectare
                    </button>
                </form>
            </div>
        </nav>
        <?php endif; ?>

        <main class="<?= !empty($user) ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4' : 'col-12 py-4' ?>">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
