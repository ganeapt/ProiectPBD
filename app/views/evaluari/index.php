<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Centralizator Evaluări</h3>
        <p class="text-muted small mb-0">Punctaje, decizii și observații ale evaluatorilor independenți</p>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Apel</th><th>Proiect</th><th>Evaluator</th><th>Punctaj</th><th>Decizie</th><th>Data</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($evaluari as $ev): ?>
                <tr>
                    <td><span class="badge bg-light text-dark border"><?= e($ev['cod_apel']) ?></span></td>
                    <td class="fw-bold text-primary"><?= e($ev['acronim']) ?></td>
                    <td class="small"><?= e($ev['evaluator_nume']) ?></td>
                    <td class="fw-bold"><?= number_format((float)$ev['punctaj_total'], 2) ?></td>
                    <td><span class="badge bg-<?= $ev['decizie'] === 'Admis' ? 'success' : ($ev['decizie'] === 'Respins' ? 'danger' : 'warning text-dark') ?>"><?= e($ev['decizie']) ?></span></td>
                    <td class="small text-muted"><?= e($ev['data_evaluare']) ?></td>
                    <td class="text-end"><a href="/proiect?id=<?= (int)$ev['id_proiect'] ?>&tab=evaluari" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Detalii</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
