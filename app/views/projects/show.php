<?php
use App\Core\Security;
$id = (int)$proiect['id_proiect'];
$curState = $proiect['stare'];
$isSolicitant = ($user['rol'] === 'Solicitant');
$isEvaluator = ($user['rol'] === 'Evaluator');
$isOfiterAM = ($user['rol'] === 'Ofiter_AM');

$canEdit = $isSolicitant && ((int)$user['id_org'] === (int)$proiect['id_org_lider']);
$canEval = $isEvaluator && ($curState === 'In_Evaluare');
$canVerify = $isOfiterAM;

$amTransitions = [
    'Depus' => ['In_Evaluare'],
    'In_Evaluare' => ['Contractat', 'Respins'],
    'Contractat' => ['Implementare'],
    'Implementare' => ['Finalizat'],
];
$nextAmStates = $amTransitions[$curState] ?? [];
?>

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="/proiecte" class="btn btn-sm btn-light border"><i class="bi bi-arrow-left"></i> Înapoi</a>
            <span class="badge bg-light text-dark border"><?= e($proiect['cod_apel']) ?></span>
            <?= badgeStareProiect($curState) ?>
        </div>
        <h3 class="fw-bold mb-1"><?= e($proiect['acronim']) ?> — <?= e($proiect['titlu']) ?></h3>
        <p class="text-muted small mb-0">
            Lider: <strong><?= e($proiect['lider_denumire']) ?></strong> (<?= e($proiect['lider_cui']) ?>) |
            Durată: <strong><?= (int)$proiect['durata_luni'] ?> luni</strong> |
            Buget: <strong><?= formatMoney($proiect['valoare_totala']) ?></strong>
        </p>
    </div>

    <?php if ($isSolicitant && $curState === 'Draft'): ?>
    <form method="POST" action="/proiect/action" class="bg-white p-2 rounded-3 shadow-sm">
        <?= Security::csrfField() ?>
        <input type="hidden" name="id_proiect" value="<?= $id ?>">
        <input type="hidden" name="action" value="change_state">
        <input type="hidden" name="new_state" value="Depus">
        <button type="submit" class="btn btn-sm btn-success fw-semibold" onclick="return confirm('Depui oficial proiectul? După depunere nu mai poți modifica consorțiul sau bugetul.');">
            <i class="bi bi-send-check"></i> Depune Proiectul
        </button>
    </form>
    <?php elseif ($isOfiterAM && !empty($nextAmStates)): ?>
    <form method="POST" action="/proiect/action" class="d-flex gap-2 align-items-center bg-white p-2 rounded-3 shadow-sm">
        <?= Security::csrfField() ?>
        <input type="hidden" name="id_proiect" value="<?= $id ?>">
        <input type="hidden" name="action" value="change_state">
        <select name="new_state" class="form-select form-select-sm" style="width:auto;">
            <?php foreach ($nextAmStates as $st): ?>
                <option value="<?= $st ?>"><?= str_replace('_', ' ', $st) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-outline-primary">Avansează Stare</button>
    </form>
    <?php endif; ?>
</div>

<!-- Lifecycle Stepper -->
<?php
$steps = ['Draft', 'Depus', 'In_Evaluare', 'Contractat', 'Implementare', 'Finalizat'];
$curIdx = array_search($curState, $steps, true);
?>
<div class="card p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <?php foreach ($steps as $idx => $stepName):
            $isDone = ($curIdx !== false && $idx < $curIdx);
            $isCurrent = ($curState === $stepName);
            $badgeCls = $isCurrent ? 'bg-primary text-white' : ($isDone ? 'bg-success text-white' : 'bg-light text-muted border');
        ?>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill <?= $badgeCls ?> px-3 py-2">
                    <?= ($idx + 1) . '. ' . str_replace('_', ' ', $stepName) ?>
                </span>
                <?php if ($idx < count($steps) - 1): ?>
                    <i class="bi bi-chevron-right text-muted d-none d-md-inline"></i>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php if ($curState === 'Respins'): ?>
            <span class="badge rounded-pill bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Respins</span>
        <?php endif; ?>
    </div>
    <?php if ($isSolicitant && $curState === 'Draft'): ?>
        <div class="mt-3 pt-2 border-top small d-flex flex-wrap gap-3 align-items-center">
            <span class="fw-semibold text-muted">Pregătire depunere:</span>
            <span class="<?= !empty($linii) ? 'text-success' : 'text-danger' ?>">
                <i class="bi <?= !empty($linii) ? 'bi-check-circle-fill' : 'bi-exclamation-circle' ?>"></i>
                Linii bugetare (<?= count($linii) ?>)
            </span>
            <span class="<?= !empty($activitati) ? 'text-success' : 'text-warning' ?>">
                <i class="bi <?= !empty($activitati) ? 'bi-check-circle-fill' : 'bi-info-circle' ?>"></i>
                Activități planificate (<?= count($activitati) ?>)
            </span>
        </div>
    <?php endif; ?>
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link <?= $tab === 'consortiu' ? 'active fw-bold' : '' ?>" href="/proiect?id=<?= $id ?>&tab=consortiu"><i class="bi bi-diagram-3"></i> Consorțiu (<?= count($parteneri) ?>)</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'buget' ? 'active fw-bold' : '' ?>" href="/proiect?id=<?= $id ?>&tab=buget"><i class="bi bi-wallet2"></i> Linii Bugetare (<?= count($linii) ?>)</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'activitati' ? 'active fw-bold' : '' ?>" href="/proiect?id=<?= $id ?>&tab=activitati"><i class="bi bi-list-task"></i> Activități &amp; Livrabile (<?= count($activitati) ?>)</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'evaluari' ? 'active fw-bold' : '' ?>" href="/proiect?id=<?= $id ?>&tab=evaluari"><i class="bi bi-clipboard-check"></i> Evaluări (<?= count($evaluari) ?>)</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'rambursari' ? 'active fw-bold' : '' ?>" href="/proiect?id=<?= $id ?>&tab=rambursari"><i class="bi bi-cash-stack"></i> Cereri Rambursare (<?= count($cereri) ?>)</a></li>
</ul>

<?php if ($tab === 'consortiu'): ?>
<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-3">Parteneri în Consorțiu</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Organizație</th><th>CUI</th><th>Tip</th><th></th></tr></thead>
                    <tbody>
                        <tr class="table-primary">
                            <td class="fw-bold"><?= e($proiect['lider_denumire']) ?> <span class="badge bg-primary ms-1">Lider</span></td>
                            <td><code><?= e($proiect['lider_cui']) ?></code></td>
                            <td>-</td><td></td>
                        </tr>
                        <?php foreach ($parteneri as $pt): ?>
                        <tr>
                            <td class="fw-semibold"><?= e($pt['denumire']) ?></td>
                            <td><code><?= e($pt['cui']) ?></code></td>
                            <td><span class="badge bg-light text-dark border"><?= e($pt['tip_organizatie']) ?></span></td>
                            <td class="text-end">
                                <?php if ($canEdit): ?>
                                <form method="POST" action="/proiect/action" class="d-inline">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="id_proiect" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="remove_partener">
                                    <input type="hidden" name="id_org" value="<?= (int)$pt['id_org'] ?>">
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
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
    <?php if ($canEdit): ?>
    <div class="col-md-5">
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Adaugă Partener în Consorțiu</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_partener">
                <div class="mb-3">
                    <select name="id_org" class="form-select" required>
                        <?php foreach ($allOrgs as $o): ?>
                            <?php if ($o['id_org'] != $proiect['id_org_lider']): ?>
                                <option value="<?= (int)$o['id_org'] ?>"><?= e($o['denumire']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle"></i> Adaugă Partener</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'buget'): ?>
<div class="row g-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-3">Structura Bugetară</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Categorie</th><th>Sumă Eligibilă</th><th>Neeligibil</th><th>Decontat Aprobat</th><th>Disponibil</th></tr></thead>
                    <tbody>
                        <?php foreach ($linii as $l): $disp = (float)$l['suma_eligibila'] - (float)$l['consumat']; ?>
                        <tr>
                            <td class="fw-bold"><?= e(str_replace('_', ' ', $l['categorie_cheltuiala'])) ?></td>
                            <td><?= formatMoney($l['suma_eligibila']) ?></td>
                            <td class="text-muted"><?= formatMoney($l['suma_neeligibila']) ?></td>
                            <td class="text-success fw-semibold"><?= formatMoney($l['consumat']) ?></td>
                            <td class="fw-bold <?= $disp < 0 ? 'text-danger' : 'text-primary' ?>"><?= formatMoney($disp) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if ($canEdit): ?>
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Adaugă Linie Bugetară</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_linie">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Categorie</label>
                    <select name="categorie_cheltuiala" class="form-select form-select-sm" required>
                        <option value="Personal">Personal</option>
                        <option value="Echipamente">Echipamente</option>
                        <option value="Servicii_Subcontractate">Servicii Subcontractate</option>
                        <option value="Deplasari">Deplasări</option>
                        <option value="Regie">Regie</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Sumă Eligibilă (RON)</label>
                    <input type="number" step="0.01" name="suma_eligibila" class="form-control form-control-sm" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Sumă Neeligibilă (RON)</label>
                    <input type="number" step="0.01" name="suma_neeligibila" class="form-control form-control-sm" value="0.00">
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Salvează Linie</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'activitati'): ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header py-3">Activități Planificate</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Cod</th><th>Activitate</th><th>Linie Buget</th><th>Perioadă</th><th>Progres</th></tr></thead>
                    <tbody>
                        <?php foreach ($activitati as $a): ?>
                        <tr>
                            <td class="fw-bold"><?= e($a['numar_ordine']) ?></td>
                            <td class="small fw-semibold"><?= e($a['titlu']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= e($a['categorie_cheltuiala']) ?></span></td>
                            <td class="small text-muted"><?= e($a['data_inceput']) ?> &rarr; <?= e($a['data_sfarsit']) ?></td>
                            <td style="width:180px;">
                                <?php if ($canEdit): ?>
                                <form method="POST" action="/proiect/action" class="d-flex gap-1 align-items-center">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="id_proiect" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="update_progres">
                                    <input type="hidden" name="id_act" value="<?= (int)$a['id_act'] ?>">
                                    <input type="number" name="procent_realizat" value="<?= (int)$a['procent_realizat'] ?>" min="0" max="100" class="form-control form-control-sm" style="width:65px;">
                                    <span class="small">%</span>
                                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-check"></i></button>
                                </form>
                                <?php else: ?>
                                    <span class="fw-bold"><?= (int)$a['procent_realizat'] ?>%</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">Livrabile Asumate</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Activitate</th><th>Denumire Livrabil</th><th>Termen</th><th>CR</th><th>Stare</th></tr></thead>
                    <tbody>
                        <?php foreach ($livrabile as $lv): ?>
                        <tr>
                            <td class="fw-bold"><?= e($lv['numar_ordine']) ?></td>
                            <td class="small"><?= e($lv['denumire']) ?></td>
                            <td class="small text-muted"><?= e($lv['termen_asumat']) ?></td>
                            <td><?= $lv['numar_cerere'] ? '<span class="badge bg-info">' . e($lv['numar_cerere']) . '</span>' : '-' ?></td>
                            <td>
                                <?php if ($canEdit || $canVerify): ?>
                                <form method="POST" action="/proiect/action">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="id_proiect" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="update_livrabil_stare">
                                    <input type="hidden" name="id_livrabil" value="<?= (int)$lv['id_livrabil'] ?>">
                                    <select name="stare" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <?php foreach (['In_Lucru','Livrat','Aprobat','Respins'] as $st): ?>
                                            <option value="<?= $st ?>" <?= $lv['stare'] === $st ? 'selected' : '' ?>><?= str_replace('_', ' ', $st) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= e($lv['stare']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($canEdit): ?>
    <div class="col-lg-4">
        <div class="card p-3 mb-4">
            <h6 class="fw-bold mb-3">Adaugă Activitate</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_activitate">
                <div class="row g-2 mb-2">
                    <div class="col-4"><label class="form-label small fw-semibold">Nr.</label><input type="text" name="numar_ordine" class="form-control form-control-sm" required></div>
                    <div class="col-8">
                        <label class="form-label small fw-semibold">Linie Bugetară</label>
                        <select name="id_linie" class="form-select form-select-sm" required>
                            <?php foreach ($linii as $l): ?><option value="<?= (int)$l['id_linie'] ?>"><?= e($l['categorie_cheltuiala']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-2"><label class="form-label small fw-semibold">Titlu</label><input type="text" name="titlu" class="form-control form-control-sm" required></div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label small fw-semibold">Început</label><input type="date" name="data_inceput" class="form-control form-control-sm" required></div>
                    <div class="col-6"><label class="form-label small fw-semibold">Sfârșit</label><input type="date" name="data_sfarsit" class="form-control form-control-sm" required></div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Adaugă Activitate</button>
            </form>
        </div>
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Adaugă Livrabil</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_livrabil">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Activitate</label>
                    <select name="id_act" class="form-select form-select-sm" required>
                        <?php foreach ($activitati as $a): ?><option value="<?= (int)$a['id_act'] ?>"><?= e($a['numar_ordine'] . ' - ' . $a['titlu']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2"><label class="form-label small fw-semibold">Denumire</label><input type="text" name="denumire" class="form-control form-control-sm" required></div>
                <div class="mb-3"><label class="form-label small fw-semibold">Termen</label><input type="date" name="termen_asumat" class="form-control form-control-sm" required></div>
                <button type="submit" class="btn btn-success btn-sm w-100">Adaugă Livrabil</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'evaluari'): ?>
<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header py-3">Fișe de Evaluare Înregistrate</div>
            <div class="list-group list-group-flush">
                <?php if (empty($evaluari)): ?>
                    <div class="p-4 text-center text-muted small">Proiectul nu are încă evaluări.</div>
                <?php else: ?>
                    <?php foreach ($evaluari as $ev): ?>
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong><?= e($ev['evaluator_nume']) ?> <small class="text-muted">(<?= e($ev['evaluator_org']) ?>)</small></strong>
                            <div>
                                <span class="badge bg-dark me-1"><?= number_format((float)$ev['punctaj_total'], 2) ?> pct</span>
                                <span class="badge bg-<?= $ev['decizie'] === 'Admis' ? 'success' : ($ev['decizie'] === 'Respins' ? 'danger' : 'warning text-dark') ?>"><?= e($ev['decizie']) ?></span>
                            </div>
                        </div>
                        <p class="small text-muted mb-1"><?= e($ev['observatii']) ?></p>
                        <div class="small text-secondary">Data: <?= e($ev['data_evaluare']) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($canEval): ?>
    <div class="col-md-5">
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Adaugă Fișă de Evaluare</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_evaluare">
                <div class="row g-2 mb-2">
                    <div class="col-6"><label class="form-label small fw-semibold">Punctaj (0-100)</label><input type="number" step="0.01" min="0" max="100" name="punctaj_total" class="form-control form-control-sm" required></div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Decizie</label>
                        <select name="decizie" class="form-select form-select-sm" required>
                            <option value="Admis">Admis</option>
                            <option value="Clarificari">Clarificări</option>
                            <option value="Respins">Respins</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label small fw-semibold">Observații</label><textarea name="observatii" rows="3" class="form-control form-control-sm" required></textarea></div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Trimite Evaluare</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($tab === 'rambursari'): ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header py-3">Cereri de Rambursare</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nr.</th><th>Data</th><th>Solicitat</th><th>Aprobat</th><th>Ofițer AM</th><th>Stare</th><?php if ($canVerify): ?><th>Verificare</th><?php endif; ?></tr></thead>
                    <tbody>
                        <?php foreach ($cereri as $cr): ?>
                        <tr>
                            <td class="fw-bold"><?= e($cr['numar_cerere']) ?></td>
                            <td class="small"><?= e($cr['data_depunere']) ?></td>
                            <td class="small fw-semibold"><?= formatMoney($cr['suma_solicitata']) ?></td>
                            <td class="small fw-bold text-success"><?= formatMoney($cr['suma_aprobata']) ?></td>
                            <td class="small"><?= e($cr['ofiter_nume'] ?? '-') ?></td>
                            <td><?= badgeStareCR($cr['stare_cr']) ?></td>
                            <?php if ($canVerify): ?>
                            <td>
                                <form method="POST" action="/proiect/action" class="d-flex gap-1 align-items-center">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="id_proiect" value="<?= $id ?>">
                                    <input type="hidden" name="action" value="verify_cr">
                                    <input type="hidden" name="id_cr" value="<?= (int)$cr['id_cr'] ?>">
                                    <input type="number" step="0.01" name="suma_aprobata" value="<?= e((string)($cr['suma_aprobata'] ?? $cr['suma_solicitata'])) ?>" class="form-control form-control-sm" style="width:100px;">
                                    <select name="stare_cr" class="form-select form-select-sm" style="width:auto;">
                                        <?php foreach (['Depusa','In_Verificare','Aprobata','Respinsa'] as $st): ?>
                                            <option value="<?= $st ?>" <?= $cr['stare_cr'] === $st ? 'selected' : '' ?>><?= str_replace('_', ' ', $st) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check2"></i></button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3">Documente Justificative Atașate</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>CR</th><th>Tip</th><th>Nr. Document</th><th>Linie Buget</th><th>Total</th><th>Eligibil Sol.</th></tr></thead>
                    <tbody>
                        <?php foreach ($documente as $d): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= e($d['numar_cerere']) ?></span></td>
                            <td class="small fw-semibold"><?= e(str_replace('_', ' ', $d['tip_document'])) ?></td>
                            <td class="small"><code><?= e($d['numar_document']) ?></code></td>
                            <td class="small"><?= e($d['categorie_cheltuiala']) ?></td>
                            <td class="small"><?= formatMoney($d['valoare_totala']) ?></td>
                            <td class="small fw-bold text-primary"><?= formatMoney($d['valoare_eligibila_solicitata']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($canEdit): ?>
    <div class="col-lg-4">
        <div class="card p-3 mb-4">
            <h6 class="fw-bold mb-3">Depune Cerere de Rambursare</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_cr">
                <div class="mb-2"><label class="form-label small fw-semibold">Număr Cerere</label><input type="text" name="numar_cerere" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label small fw-semibold">Sumă Solicitată (RON)</label><input type="number" step="0.01" name="suma_solicitata" class="form-control form-control-sm" required></div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Asociază Livrabile</label>
                    <select name="livrabile[]" class="form-select form-select-sm" multiple size="3">
                        <?php foreach ($livrabile as $lv): ?><?php if (!$lv['id_cr']): ?><option value="<?= (int)$lv['id_livrabil'] ?>"><?= e($lv['numar_ordine'] . ': ' . $lv['denumire']) ?></option><?php endif; ?><?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm w-100">Depune Cerere</button>
            </form>
        </div>

        <div class="card p-3">
            <h6 class="fw-bold mb-3">Atașează Document Justificativ</h6>
            <form method="POST" action="/proiect/action">
                <?= Security::csrfField() ?>
                <input type="hidden" name="id_proiect" value="<?= $id ?>">
                <input type="hidden" name="action" value="add_doc">
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Cerere Rambursare</label>
                    <select name="id_cr" class="form-select form-select-sm" required>
                        <?php foreach ($cereri as $cr): ?><option value="<?= (int)$cr['id_cr'] ?>"><?= e($cr['numar_cerere']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Linie Bugetară</label>
                    <select name="id_linie" class="form-select form-select-sm" required>
                        <?php foreach ($linii as $l): ?><option value="<?= (int)$l['id_linie'] ?>"><?= e($l['categorie_cheltuiala']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Tip Document</label>
                        <select name="tip_document" class="form-select form-select-sm" required>
                            <option value="Factura">Factură</option><option value="Ordin_Plata">Ordin Plată</option><option value="Stat_Salarii">Stat Salarii</option><option value="Bon_Fiscal">Bon Fiscal</option><option value="Raport_Audit">Raport Audit</option>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label small fw-semibold">Nr. Document</label><input type="text" name="numar_document" class="form-control form-control-sm" required></div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4"><label class="form-label small fw-semibold">Data</label><input type="date" name="data_emiterii" class="form-control form-control-sm" required></div>
                    <div class="col-4"><label class="form-label small fw-semibold">Total</label><input type="number" step="0.01" name="valoare_totala" class="form-control form-control-sm" required></div>
                    <div class="col-4"><label class="form-label small fw-semibold">Eligibilă</label><input type="number" step="0.01" name="valoare_eligibila_solicitata" class="form-control form-control-sm" required></div>
                </div>
                <button type="submit" class="btn btn-success btn-sm w-100">Atașează Document</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
