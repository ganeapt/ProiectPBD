<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Dashboard General</h3>
        <p class="text-muted small mb-0">Monitorizarea proiectelor europene, a bugetelor și a cererilor de rambursare</p>
    </div>
    <?php if ($user['rol'] === 'Solicitant'): ?>
    <a href="/proiecte" class="btn btn-primary">
        <i class="bi bi-folder2-open"></i> Gestionează Proiecte
    </a>
    <?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card kpi-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total Proiecte</div>
                    <div class="fs-3 fw-bold text-primary"><?= (int)$stats['total_proiecte'] ?></div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary"><i class="bi bi-folder2-open fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Valoare Portofoliu</div>
                    <div class="fs-5 fw-bold text-dark"><?= formatMoney($stats['buget_total']) ?></div>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info"><i class="bi bi-pie-chart fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Total Rambursat</div>
                    <div class="fs-5 fw-bold text-success"><?= formatMoney($stats['total_aprobat']) ?></div>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success"><i class="bi bi-cash-coin fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card kpi-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-semibold">Apeluri Active</div>
                    <div class="fs-3 fw-bold text-warning"><?= (int)$stats['apeluri_active'] ?></div>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning"><i class="bi bi-megaphone fs-4"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-folder-check me-2 text-primary"></i>Proiecte Recente</span>
                <a href="/proiecte" class="btn btn-sm btn-outline-primary">Vezi toate</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Acronim</th><th>Apel</th><th>Lider Consorțiu</th><th>Valoare</th><th>Stare</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proiecte as $p): ?>
                        <tr>
                            <td class="fw-bold"><?= e($p['acronim']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= e($p['cod_apel']) ?></span></td>
                            <td class="small"><?= e($p['lider']) ?></td>
                            <td class="small fw-semibold"><?= formatMoney($p['valoare_totala']) ?></td>
                            <td><?= badgeStareProiect($p['stare']) ?></td>
                            <td class="text-end">
                                <a href="/proiect?id=<?= (int)$p['id_proiect'] ?>" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i> Detalii</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="bi bi-hourglass-split me-2 text-warning"></i>Cereri în Verificare</span>
                <a href="/rambursari" class="btn btn-sm btn-outline-secondary">Toate</a>
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($cereriPending)): ?>
                    <div class="p-4 text-center text-muted small">Nu există cereri în așteptare.</div>
                <?php else: ?>
                    <?php foreach ($cereriPending as $cr): ?>
                    <a href="/proiect?id=<?= (int)$cr['id_proiect'] ?>&tab=rambursari" class="list-group-item list-group-item-action py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?= e($cr['acronim']) ?> - <?= e($cr['numar_cerere']) ?></span>
                            <?= badgeStareCR($cr['stare_cr']) ?>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Solicitat: <?= formatMoney($cr['suma_solicitata']) ?></span>
                            <span><?= e($cr['data_depunere']) ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
