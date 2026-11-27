<?php use App\Core\Security; $isAdmin = ($user['rol'] === 'Administrator'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Organizații &amp; Utilizatori</h3>
        <p class="text-muted small mb-0">Entitățile juridice din consorții și conturile de acces ale sistemului</p>
    </div>
    <?php if ($isAdmin): ?>
    <div>
        <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#modalOrg"><i class="bi bi-building-add"></i> Organizație Nouă</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser"><i class="bi bi-person-plus"></i> Utilizator Nou</button>
    </div>
    <?php endif; ?>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3"><i class="bi bi-building me-2 text-primary"></i>Organizații Înregistrate</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Denumire</th><th>CUI</th><th>IBAN</th><th>Tip</th><th>Adresă</th></tr></thead>
                    <tbody>
                        <?php foreach ($organizatii as $o): ?>
                        <tr>
                            <td class="fw-bold"><?= e($o['denumire']) ?></td>
                            <td><code><?= e($o['cui']) ?></code></td>
                            <td class="small"><code><?= e($o['iban']) ?></code></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info"><?= e(str_replace('_', ' ', $o['tip_organizatie'])) ?></span></td>
                            <td class="small text-muted"><?= e($o['adresa']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header py-3"><i class="bi bi-people me-2 text-success"></i>Utilizatori Sistem</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Nume &amp; Prenume</th><th>Email</th><th>Organizație</th><th>Rol</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($utilizatori as $u): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($u['prenume'] . ' ' . $u['nume']) ?></td>
                            <td class="small"><?= e($u['email']) ?></td>
                            <td class="small"><?= e($u['org_denumire']) ?></td>
                            <td><span class="badge bg-primary"><?= e(str_replace('_', ' ', $u['rol'])) ?></span></td>
                            <td><?= $u['activ'] ? '<span class="badge bg-success">Activ</span>' : '<span class="badge bg-secondary">Inactiv</span>' ?></td>
                            <td class="text-end">
                                <?php if ($isAdmin): ?>
                                <form method="POST" action="/organizatii" class="d-inline">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="action" value="toggle_user">
                                    <input type="hidden" name="id_user" value="<?= (int)$u['id_user'] ?>">
                                    <button class="btn btn-sm btn-outline-secondary"><?= $u['activ'] ? 'Dezactivează' : 'Activează' ?></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
<div class="modal fade" id="modalOrg" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/organizatii" class="modal-content">
            <?= Security::csrfField() ?>
            <input type="hidden" name="action" value="create_org">
            <div class="modal-header"><h5 class="modal-title fw-bold">Organizație Nouă</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label small fw-semibold">Denumire</label><input type="text" name="denumire" class="form-control" required></div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label small fw-semibold">CUI</label><input type="text" name="cui" class="form-control" required></div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tip</label>
                        <select name="tip_organizatie" class="form-select" required>
                            <option value="IMM">IMM</option><option value="Universitate">Universitate</option><option value="ONG">ONG</option><option value="Institut_Cercetare">Institut Cercetare</option><option value="Companie_Mare">Companie Mare</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label small fw-semibold">IBAN</label><input type="text" name="iban" class="form-control" required></div>
                <div class="mb-3"><label class="form-label small fw-semibold">Adresă</label><input type="text" name="adresa" class="form-control" required></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Anulează</button><button type="submit" class="btn btn-primary">Salvează</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/organizatii" class="modal-content">
            <?= Security::csrfField() ?>
            <input type="hidden" name="action" value="create_user">
            <div class="modal-header"><h5 class="modal-title fw-bold">Utilizator Nou</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label small fw-semibold">Nume</label><input type="text" name="nume" class="form-control" required></div>
                    <div class="col-6"><label class="form-label small fw-semibold">Prenume</label><input type="text" name="prenume" class="form-control" required></div>
                </div>
                <div class="mb-3"><label class="form-label small fw-semibold">Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Organizație</label>
                    <select name="id_org" class="form-select" required>
                        <?php foreach ($organizatii as $o): ?><option value="<?= (int)$o['id_org'] ?>"><?= e($o['denumire']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Rol</label>
                        <select name="rol" class="form-select" required>
                            <option value="Solicitant">Solicitant</option><option value="Evaluator">Evaluator</option><option value="Ofiter_AM">Ofițer AM</option><option value="Administrator">Administrator</option>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label small fw-semibold">Parolă (min 8 car.)</label><input type="password" name="parola" class="form-control" minlength="8" required></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Anulează</button><button type="submit" class="btn btn-primary">Salvează</button></div>
        </form>
    </div>
</div>
<?php endif; ?>
