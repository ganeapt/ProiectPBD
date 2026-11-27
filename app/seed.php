<?php
declare(strict_types=1);

require_once __DIR__ . '/config/Database.php';

use App\Config\Database;

$pdo = Database::getConnection();
$pdo->beginTransaction();

try {
    // 1. Organizatii
    $orgs = [
        [1, 'Universitatea Tehnica din Cluj-Napoca - CUNBM', 'RO14587211', 'RO49TREZ06120F330500XXXX', 'Str. Victor Babes 62A, Baia Mare', 'Universitate'],
        [2, 'SC SoftTech Solutions Innovation SRL', 'RO28994120', 'RO12BTRL02401202889941XX', 'Str. Memorandumului 21, Cluj-Napoca', 'IMM'],
        [3, 'Institutul National de Cercetare in Informatica ICI', 'RO15904423', 'RO88TREZ7005069XXX001234', 'Bd. Maresal Averescu 8-10, Bucuresti', 'Institut_Cercetare'],
        [4, 'Asociatia Centrul de Inovare Digitala Transilvania', 'RO34001928', 'RO33INGB0000999901452288', 'Str. Teodor Mihali 56, Cluj-Napoca', 'ONG'],
        [5, 'SC CloudEnterprise Systems SA', 'RO18442091', 'RO77RZBR0000060011223344', 'Str. Barbu Vacarescu 102, Bucuresti', 'Companie_Mare'],
    ];
    $stmtOrg = $pdo->prepare("INSERT IGNORE INTO organizatie (id_org, denumire, cui, iban, adresa, tip_organizatie) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($orgs as $o) $stmtOrg->execute($o);

    // 2. Utilizatori (parola: ProiectPBD2026!)
    $hash = password_hash('ProiectPBD2026!', PASSWORD_BCRYPT);
    $users = [
        [1, 1, 'Popescu', 'Mihai', 'admin.sys@mysmis-demo.ro', $hash, 'Administrator', 1],
        [2, 2, 'Ionescu', 'Elena', 'elena.ionescu@softtech.ro', $hash, 'Solicitant', 1],
        [3, 1, 'Radu', 'Cristian', 'c.radu@cunbm.utcluj.ro', $hash, 'Solicitant', 1],
        [4, 3, 'Dumitrescu', 'Adrian', 'a.dumitrescu@ici.ro', $hash, 'Evaluator', 1],
        [5, 4, 'Vasilescu', 'Andreea', 'andreea.v@transilvania-hub.ro', $hash, 'Evaluator', 1],
        [6, 1, 'Marinescu', 'Dan', 'dan.marinescu@oipoc.ro', $hash, 'Ofiter_AM', 1],
    ];
    $stmtUser = $pdo->prepare("INSERT IGNORE INTO utilizator (id_user, id_org, nume, prenume, email, parola_hash, rol, activ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) $stmtUser->execute($u);

    // 3. Apeluri de proiecte
    $apeluri = [
        [1, 'PNRR/2026/C9/I8', 'Digitalizarea IMM-urilor prin Tehnologii Avansate AI si Cloud Computing', 50000000.00, 10.00, '2026-01-15', '2026-12-30'],
        [2, 'POCIDIF/2026/1.1/A', 'Centre de Excelenta in Cercetare Aplicata si Transfer Tehnologic', 80000000.00, 15.00, '2026-02-01', '2026-12-31'],
        [3, 'HORIZON-EUROPE-2026-AI', 'Sisteme Inteligente pentru Securitatea Datelor Industriale Transfrontaliere', 120000000.00, 0.00, '2026-03-01', '2026-12-15'],
    ];
    $stmtApel = $pdo->prepare("INSERT IGNORE INTO apel_proiect (id_apel, cod_apel, titlu, buget_total, procent_cofinantare, data_start, data_limita) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($apeluri as $a) $stmtApel->execute($a);

    // 4. Proiecte
    $proiecte = [
        [1, 1, 2, 'Platforma Nationala de Analiza si Automatizare a Fluxurilor de Afaceri bazata pe Inteligenta Artificiala', 'SMART-BIZ-AI', 24, 'Implementare', '2026-03-10 14:30:00', 4500000.00],
        [2, 2, 1, 'Centru Regional de Cercetare Avansata in Securitate Cibernetica si Internet of Things', 'CYBER-SEC-IOT', 36, 'Contractat', '2026-04-12 11:15:00', 8200000.00],
        [3, 1, 5, 'Modernizarea Sistemelor ERP de Productie prin Solutii Cloud Hibrid', 'CLOUD-PROD', 18, 'In_Evaluare', '2026-05-20 09:45:00', 3100000.00],
        [4, 3, 3, 'Consortiu Transnational pentru Transparenta si Etica Datelor Digitale Industriale', 'ETHIC-DATA', 30, 'Draft', null, 6500000.00],
    ];
    $stmtPrj = $pdo->prepare("INSERT IGNORE INTO proiect (id_proiect, id_apel, id_org_lider, titlu, acronim, durata_luni, stare, data_depunere, valoare_totala) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($proiecte as $p) $stmtPrj->execute($p);

    // 5. Parteneri
    $parteneri = [[1, 1], [1, 4], [2, 2], [2, 3]];
    $stmtPart = $pdo->prepare("INSERT IGNORE INTO proiect_partener (id_proiect, id_org) VALUES (?, ?)");
    foreach ($parteneri as $pt) $stmtPart->execute($pt);

    // 6. Linii bugetare
    $linii = [
        [1, 1, 'Personal', 2200000.00, 0.00],
        [2, 1, 'Echipamente', 1100000.00, 50000.00],
        [3, 1, 'Servicii_Subcontractate', 600000.00, 20000.00],
        [4, 1, 'Deplasari', 150000.00, 10000.00],
        [5, 1, 'Regie', 450000.00, 0.00],
        [6, 2, 'Personal', 4000000.00, 0.00],
        [7, 2, 'Echipamente', 2800000.00, 100000.00],
        [8, 2, 'Servicii_Subcontractate', 800000.00, 50000.00],
    ];
    $stmtLinie = $pdo->prepare("INSERT IGNORE INTO linie_bugetara (id_linie, id_proiect, categorie_cheltuiala, suma_eligibila, suma_neeligibila) VALUES (?, ?, ?, ?, ?)");
    foreach ($linii as $l) $stmtLinie->execute($l);

    // 7. Activitati
    $activitati = [
        [1, 1, 1, 'A1.1', 'Analiza cerintelor operationale si proiectarea arhitecturii AI', '2026-04-01', '2026-07-31', 100],
        [2, 1, 2, 'A1.2', 'Achizitia infrastructurii hardware si configurarea clusterului GPU', '2026-06-01', '2026-09-30', 100],
        [3, 1, 1, 'A2.1', 'Dezvoltarea modulelor NLP si Computer Vision', '2026-08-01', '2027-01-31', 65],
        [4, 2, 6, 'A1.1', 'Recrutarea echipei de cercetare si definirea metodologiei', '2026-05-01', '2026-08-31', 90],
    ];
    $stmtAct = $pdo->prepare("INSERT IGNORE INTO activitate (id_act, id_proiect, id_linie, numar_ordine, titlu, data_inceput, data_sfarsit, procent_realizat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($activitati as $a) $stmtAct->execute($a);

    // 8. Cereri de rambursare
    $cereri = [
        [1, 1, 6, 'CR-01', '2026-08-05', 450000.00, 445000.00, 'Aprobata'],
        [2, 1, 6, 'CR-02', '2026-11-10', 820000.00, 795000.00, 'Aprobata'],
        [3, 1, 6, 'CR-03', '2027-02-15', 620000.00, null, 'In_Verificare'],
        [4, 2, 6, 'CR-01', '2026-09-15', 380000.00, 380000.00, 'Aprobata'],
    ];
    $stmtCr = $pdo->prepare("INSERT IGNORE INTO cerere_rambursare (id_cr, id_proiect, id_ofiter, numar_cerere, data_depunere, suma_solicitata, suma_aprobata, stare_cr) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($cereri as $c) $stmtCr->execute($c);

    // 9. Livrabile
    $livrabile = [
        [1, 1, 1, 'Raport de analiza a cerintelor de business', '2026-07-15', 'Aprobat'],
        [2, 2, 2, 'Proces-verbal de receptie noduri GPU', '2026-09-15', 'Aprobat'],
        [3, 3, 3, 'Modul software prototip NLP v1.0', '2027-01-20', 'Livrat'],
        [4, 4, 4, 'Metodologie experimentala validata', '2026-08-20', 'Aprobat'],
    ];
    $stmtLiv = $pdo->prepare("INSERT IGNORE INTO livrabil (id_livrabil, id_act, id_cr, denumire, termen_asumat, stare) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($livrabile as $lv) $stmtLiv->execute($lv);

    // 10. Evaluari
    $evaluari = [
        [1, 1, 4, 94.50, 'Admis', 'Propunere matura, excelenta stiintifica bine articulata, buget corect dimensionat.', '2026-03-25'],
        [2, 2, 4, 88.00, 'Admis', 'Infrastructura de cercetare relevanta, capacitate institutionala dovedita.', '2026-04-28'],
        [3, 3, 5, 68.50, 'Clarificari', 'Se solicita clarificari privind devizul echipamentelor.', '2026-06-02'],
    ];
    $stmtEval = $pdo->prepare("INSERT IGNORE INTO evaluare (id_eval, id_proiect, id_evaluator, punctaj_total, decizie, observatii, data_evaluare) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($evaluari as $ev) $stmtEval->execute($ev);

    // 11. Documente justificative
    $docs = [
        [1, 1, 1, 'Stat_Salarii', 'STAT-2026-04-06', '2026-07-05', 180000.00, 180000.00, 180000.00],
        [2, 1, 4, 'Factura', 'FACT-DEP-9941', '2026-06-25', 90000.00, 85000.00, 85000.00],
        [3, 2, 2, 'Factura', 'FACT-SRV-2026-88', '2026-09-02', 650000.00, 620000.00, 620000.00],
        [4, 2, 3, 'Factura', 'FACT-AUD-104', '2026-10-15', 180000.00, 175000.00, 150000.00],
        [5, 3, 1, 'Stat_Salarii', 'STAT-2026-10-12', '2027-01-08', 350000.00, 350000.00, null],
    ];
    $stmtDoc = $pdo->prepare("INSERT IGNORE INTO document_justificativ (id_doc, id_cr, id_linie, tip_document, numar_document, data_emiterii, valoare_totala, valoare_eligibila_solicitata, valoare_aprobata) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($docs as $d) $stmtDoc->execute($d);

    $pdo->commit();
    echo "Seeding completat cu succes!\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Eroare la seeding: " . $e->getMessage() . "\n");
    exit(1);
}
