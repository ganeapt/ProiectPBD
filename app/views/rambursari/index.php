<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Centralizator Cereri de Rambursare</h3>
        <p class="text-muted small mb-0">Evidența decontărilor și verificarea financiară</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Proiect</th><th>Nr. Cerere</th><th>Data Depunerii</th><th>Sumă Solicitată</th><th>Sumă Aprobată</th><th>Ofițer AM</th><th>Stare</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($cereri as $cr): ?>
                <tr>
                    <td class="fw-bold text-primary"><?= e($cr['acronim']) ?></td>
                    <td class="fw-semibold"><?= e($cr['numar_cerere']) ?></td>
                    <td class="small"><?= e($cr['data_depunere']) ?></td>
                    <td class="small fw-semibold"><?= formatMoney($cr['suma_solicitata']) ?></td>
                    <td class="small fw-bold text-success"><?= formatMoney($cr['suma_aprobata']) ?></td>
                    <td class="small"><?= e($cr['ofiter_nume'] ?? '-') ?></td>
                    <td><?= badgeStareCR($cr['stare_cr']) ?></td>
                    <td class="text-end"><a href="/proiect?id=<?= (int)$cr['id_proiect'] ?>&tab=rambursari" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> Gestionează</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
