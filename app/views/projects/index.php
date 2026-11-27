<?php use App\Core\Security; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Portofoliu Proiecte</h3>
        <p class="text-muted small mb-0">Gestionarea ciclului de viață al proiectelor europene</p>
    </div>
    <?php if ($user['rol'] === 'Solicitant'): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProiect">
        <i class="bi bi-plus-circle"></i> Proiect Nou (Draft)
    </button>
    <?php endif; ?>
</div>

<div class="card mb-4 p-3">
    <form method="GET" action="/proiecte" class="row g-2 align-items-center">
        <div class="col-auto"><span class="small fw-semibold text-muted">Filtrează după stare:</span></div>
        <div class="col-auto">
            <select name="stare" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Toate stările</option>
                <?php foreach (['Draft','Depus','In_Evaluare','Contractat','Implementare','Finalizat','Respins'] as $st): ?>
                    <option value="<?= $st ?>" <?= $filterStare === $st ? 'selected' : '' ?>><?= str_replace('_', ' ', $st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($filterStare): ?>
            <div class="col-auto"><a href="/proiecte" class="btn btn-sm btn-link text-decoration-none">Resetează</a></div>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Acronim</th><th>Titlu Proiect</th><th>Apel</th><th>Lider &amp; Parteneri</th><th>Durată</th><th>Valoare Totală</th><th>Stare</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($proiecte as $p): ?>
                <tr>
                    <td class="fw-bold text-primary"><?= e($p['acronim']) ?></td>
                    <td class="small" style="max-width:280px;"><?= e($p['titlu']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= e($p['cod_apel']) ?></span></td>
                    <td class="small">
                        <?= e($p['lider']) ?>
                        <?php if ($p['nr_parteneri'] > 0): ?>
                            <span class="badge bg-secondary ms-1">+<?= (int)$p['nr_parteneri'] ?> parteneri</span>
                        <?php endif; ?>
                    </td>
                    <td class="small"><?= (int)$p['durata_luni'] ?> luni</td>
                    <td class="fw-semibold small"><?= formatMoney($p['valoare_totala']) ?></td>
                    <td><?= badgeStareProiect($p['stare']) ?></td>
                    <td class="text-end">
                        <a href="/proiect?id=<?= (int)$p['id_proiect'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-folder2-open"></i> Deschide</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($user['rol'] === 'Solicitant'): ?>
<div class="modal fade" id="modalProiect" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="/proiecte/create" class="modal-content">
            <?= Security::csrfField() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Creează Proiect Nou (Draft)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Apel de Finanțare</label>
                        <select name="id_apel" class="form-select" required>
                            <?php foreach ($apeluri as $a): ?>
                                <option value="<?= (int)$a['id_apel'] ?>"><?= e($a['cod_apel'] . ' - ' . $a['titlu']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Organizație Lider</label>
                        <input type="text" class="form-control bg-light" value="<?= e($user['org_denumire']) ?>" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Titlu Complet Proiect</label>
                    <input type="text" name="titlu" class="form-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Acronim</label>
                        <input type="text" name="acronim" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Durată (luni)</label>
                        <input type="number" name="durata_luni" class="form-control" value="24" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Valoare Totală (RON)</label>
                        <input type="number" step="0.01" name="valoare_totala" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Anulează</button>
                <button type="submit" class="btn btn-primary">Creează Proiect</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
