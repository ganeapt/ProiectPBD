<?php use App\Core\Security; $canManage = in_array($user['rol'], ['Administrator', 'Ofiter_AM'], true); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Apeluri de Finanțare</h3>
        <p class="text-muted small mb-0">Gestiunea programelor și apelurilor deschise pentru depunere</p>
    </div>
    <?php if ($canManage): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalApel"><i class="bi bi-plus-circle"></i> Adaugă Apel</button>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Cod Apel</th><th>Titlu Apel</th><th>Buget Total</th><th>Cofinanțare</th><th>Perioadă</th><th>Proiecte</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($apeluri as $a): ?>
                <tr>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary fw-bold"><?= e($a['cod_apel']) ?></span></td>
                    <td class="fw-semibold"><?= e($a['titlu']) ?></td>
                    <td><?= formatMoney($a['buget_total']) ?></td>
                    <td><?= number_format((float)$a['procent_cofinantare'], 2) ?>%</td>
                    <td class="small text-muted"><?= e($a['data_start']) ?> &rarr; <?= e($a['data_limita']) ?></td>
                    <td><span class="badge bg-secondary rounded-pill"><?= (int)$a['nr_proiecte'] ?></span></td>
                    <td class="text-end">
                        <?php if ($canManage && $a['nr_proiecte'] == 0): ?>
                        <form method="POST" action="/apeluri" class="d-inline" onsubmit="return confirm('Sigur ștergi acest apel?');">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_apel" value="<?= (int)$a['id_apel'] ?>">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($canManage): ?>
<div class="modal fade" id="modalApel" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/apeluri" class="modal-content">
            <?= Security::csrfField() ?>
            <input type="hidden" name="action" value="create">
            <div class="modal-header"><h5 class="modal-title fw-bold">Apel Nou</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label small fw-semibold">Cod Apel</label><input type="text" name="cod_apel" class="form-control" required></div>
                <div class="mb-3"><label class="form-label small fw-semibold">Titlu Apel</label><input type="text" name="titlu" class="form-control" required></div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label small fw-semibold">Buget Total (RON)</label><input type="number" step="0.01" name="buget_total" class="form-control" required></div>
                    <div class="col-6"><label class="form-label small fw-semibold">Cofinanțare (%)</label><input type="number" step="0.01" name="procent_cofinantare" class="form-control" value="10.00" required></div>
                </div>
                <div class="row g-2">
                    <div class="col-6"><label class="form-label small fw-semibold">Data Start</label><input type="date" name="data_start" class="form-control" required></div>
                    <div class="col-6"><label class="form-label small fw-semibold">Data Limită</label><input type="date" name="data_limita" class="form-control" required></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Anulează</button><button type="submit" class="btn btn-primary">Salvează</button></div>
        </form>
    </div>
</div>
<?php endif; ?>
