<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Rapoarte Analitice &amp; Piste de Audit</h3>
        <p class="text-muted small mb-0">Situația execuției bugetare, gradul de absorbție și corecțiile financiare</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header py-3"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Execuție Bugetară &amp; Grad de Absorbție per Proiect</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Proiect</th><th>Buget Contractat</th><th>Solicitat Decontare</th><th>Aprobat Plată</th><th>Grad Absorbție</th><th>Stare</th></tr></thead>
            <tbody>
                <?php foreach ($executie as $ex):
                    $pct = $ex['buget_contractat'] > 0 ? round(($ex['total_aprobat'] / $ex['buget_contractat']) * 100, 2) : 0;
                ?>
                <tr>
                    <td class="fw-bold text-primary"><?= e($ex['acronim']) ?></td>
                    <td class="small fw-semibold"><?= formatMoney($ex['buget_contractat']) ?></td>
                    <td class="small"><?= formatMoney($ex['total_solicitat']) ?></td>
                    <td class="small fw-bold text-success"><?= formatMoney($ex['total_aprobat']) ?></td>
                    <td style="width:200px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;"><div class="progress-bar bg-success" style="width: <?= min(100, $pct) ?>%"></div></div>
                            <span class="small fw-bold"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td><?= badgeStareProiect($ex['stare']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header py-3"><i class="bi bi-shield-exclamation me-2 text-danger"></i>Pistă de Audit: Documente Justificative Ajustate</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Proiect</th><th>Cerere</th><th>Tip &amp; Nr. Document</th><th>Categorie</th><th>Solicitat Eligibil</th><th>Aprobat</th><th>Sumă Tăiată</th></tr></thead>
            <tbody>
                <?php if (empty($ajustate)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4 small">Nu există documente cu corecții financiare.</td></tr>
                <?php else: ?>
                    <?php foreach ($ajustate as $aj): ?>
                    <tr>
                        <td class="fw-bold"><?= e($aj['acronim']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= e($aj['numar_cerere']) ?></span></td>
                        <td class="small"><?= e($aj['tip_document']) ?> <code><?= e($aj['numar_document']) ?></code></td>
                        <td class="small"><?= e($aj['categorie_cheltuiala']) ?></td>
                        <td class="small"><?= formatMoney($aj['valoare_eligibila_solicitata']) ?></td>
                        <td class="small text-success fw-semibold"><?= formatMoney($aj['valoare_aprobata']) ?></td>
                        <td class="small text-danger fw-bold">-<?= formatMoney($aj['taiat']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
