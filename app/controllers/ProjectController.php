<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectModel;
use App\Models\ApelModel;
use App\Models\OrgModel;
use App\Models\EvaluareModel;
use App\Models\RambursareModel;

class ProjectController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $filterStare = $_GET['stare'] ?? '';

        $projectModel = new ProjectModel();
        $apelModel = new ApelModel();
        $orgModel = new OrgModel();

        $this->view('projects/index', [
            'proiecte' => $projectModel->getAll($filterStare),
            'apeluri' => $apelModel->getAllWithCounts(),
            'organizatii' => $orgModel->getAll(),
            'filterStare' => $filterStare,
            'pageTitle' => 'Portofoliu Proiecte - ProiectPBD',
        ]);
    }

    public function create(): void {
        $this->requireRole(['Solicitant']);
        $this->requirePostAndCsrf();
        $user = $_SESSION['user'];

        $projectModel = new ProjectModel();
        try {
            $newId = $projectModel->create(
                (int)$_POST['id_apel'],
                (int)$user['id_org'],
                trim($_POST['titlu']),
                strtoupper(trim($_POST['acronim'])),
                (int)$_POST['durata_luni'],
                (float)$_POST['valoare_totala']
            );
            $this->setFlash('success', 'Proiectul a fost creat în starea Draft!');
            $this->redirect("/proiect?id={$newId}");
        } catch (\PDOException $e) {
            error_log('Project create error: ' . $e->getMessage());
            $this->setFlash('danger', 'Eroare la crearea proiectului. Verificați datele introduse.');
            $this->redirect('/proiecte');
        }
    }

    public function show(): void {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $tab = $_GET['tab'] ?? 'consortiu';

        $projectModel = new ProjectModel();
        $proiect = $projectModel->findById($id);
        if (!$proiect) {
            $this->setFlash('danger', 'Proiectul nu a fost găsit.');
            $this->redirect('/proiecte');
        }

        $orgModel = new OrgModel();
        $evalModel = new EvaluareModel();
        $rambModel = new RambursareModel();

        $this->view('projects/show', [
            'proiect' => $proiect,
            'tab' => $tab,
            'parteneri' => $projectModel->getPartners($id),
            'allOrgs' => $orgModel->getAll(),
            'linii' => $projectModel->getBudgetLines($id),
            'activitati' => $projectModel->getActivities($id),
            'livrabile' => $projectModel->getDeliverables($id),
            'evaluari' => $evalModel->getByProject($id),
            'cereri' => $rambModel->getByProject($id),
            'documente' => $rambModel->getDocumentsByProject($id),
            'pageTitle' => $proiect['acronim'] . ' - Detalii Proiect',
        ]);
    }

    public function handleAction(): void {
        $user = $this->requireAuth();
        $this->requirePostAndCsrf();

        $id = (int)($_POST['id_proiect'] ?? 0);
        $act = $_POST['action'] ?? '';
        $redirectTab = $_POST['tab'] ?? 'consortiu';

        $projectModel = new ProjectModel();
        $proiect = $projectModel->findById($id);
        if (!$proiect) {
            $this->setFlash('danger', 'Proiectul nu există.');
            $this->redirect('/proiecte');
        }

        // Cross-Tenant Ownership Check for Solicitant
        if ($user['rol'] === 'Solicitant' && (int)$proiect['id_org_lider'] !== (int)$user['id_org']) {
            $this->setFlash('danger', 'Acces interzis: Nu puteți modifica proiectele altei organizații.');
            $this->redirect("/proiect?id={$id}");
        }

        $evalModel = new EvaluareModel();
        $rambModel = new RambursareModel();

        try {
            switch ($act) {
                case 'change_state':
                    $newState = $_POST['new_state'] ?? '';
                    $curState = $proiect['stare'];

                    if ($user['rol'] === 'Solicitant') {
                        if ($curState === 'Draft' && $newState === 'Depus') {
                            $linii = $projectModel->getBudgetLines($id);
                            if (empty($linii)) {
                                throw new \RuntimeException('Proiectul trebuie să aibă cel puțin o linie bugetară înainte de depunere.');
                            }
                            $projectModel->updateState($id, 'Depus');
                            $this->setFlash('success', 'Proiectul a fost depus oficial!');
                        } else {
                            throw new \RuntimeException('Solicitantul poate doar să depună un proiect din starea Draft.');
                        }
                    } elseif ($user['rol'] === 'Ofiter_AM') {
                        $allowedTransitions = [
                            'Depus' => ['In_Evaluare'],
                            'In_Evaluare' => ['Contractat', 'Respins'],
                            'Contractat' => ['Implementare'],
                            'Implementare' => ['Finalizat'],
                        ];
                        if (!in_array($newState, $allowedTransitions[$curState] ?? [], true)) {
                            throw new \RuntimeException("Tranziție invalidă din '{$curState}' în '{$newState}'.");
                        }
                        $projectModel->updateState($id, $newState);
                        $this->setFlash('success', "Starea proiectului a fost actualizată în '{$newState}'.");
                    } else {
                        throw new \RuntimeException('Nu aveți permisiunea de a schimba starea proiectului.');
                    }
                    break;

                case 'add_partener':
                    $this->requireRole(['Solicitant']);
                    if ($proiect['stare'] !== 'Draft') {
                        throw new \RuntimeException('Consorțiul poate fi modificat doar în starea Draft.');
                    }
                    $projectModel->addPartner($id, (int)$_POST['id_org']);
                    $this->setFlash('success', 'Partener adăugat în consorțiu.');
                    $redirectTab = 'consortiu';
                    break;

                case 'remove_partener':
                    $this->requireRole(['Solicitant']);
                    if ($proiect['stare'] !== 'Draft') {
                        throw new \RuntimeException('Consorțiul poate fi modificat doar în starea Draft.');
                    }
                    $projectModel->removePartner($id, (int)$_POST['id_org']);
                    $this->setFlash('info', 'Partener eliminat din consorțiu.');
                    $redirectTab = 'consortiu';
                    break;

                case 'add_linie':
                    $this->requireRole(['Solicitant']);
                    if ($proiect['stare'] !== 'Draft') {
                        throw new \RuntimeException('Liniile bugetare pot fi adăugate doar în starea Draft.');
                    }
                    $projectModel->addBudgetLine($id, $_POST['categorie_cheltuiala'], (float)$_POST['suma_eligibila'], (float)$_POST['suma_neeligibila']);
                    $this->setFlash('success', 'Linia bugetară a fost adăugată.');
                    $redirectTab = 'buget';
                    break;

                case 'add_activitate':
                    $this->requireRole(['Solicitant']);
                    if (!in_array($proiect['stare'], ['Draft', 'Implementare'], true)) {
                        throw new \RuntimeException('Activitățile pot fi adăugate doar în Draft sau Implementare.');
                    }
                    $projectModel->addActivity($id, (int)$_POST['id_linie'], trim($_POST['numar_ordine']), trim($_POST['titlu']), $_POST['data_inceput'], $_POST['data_sfarsit']);
                    $this->setFlash('success', 'Activitatea a fost adăugată.');
                    $redirectTab = 'activitati';
                    break;

                case 'update_progres':
                    $this->requireRole(['Solicitant']);
                    if ($proiect['stare'] !== 'Implementare') {
                        throw new \RuntimeException('Progresul poate fi actualizat doar în starea Implementare.');
                    }
                    $projectModel->updateActivityProgress((int)$_POST['id_act'], $id, (int)$_POST['procent_realizat']);
                    $this->setFlash('success', 'Progresul activității a fost actualizat.');
                    $redirectTab = 'activitati';
                    break;

                case 'add_livrabil':
                    $this->requireRole(['Solicitant']);
                    if (!in_array($proiect['stare'], ['Draft', 'Implementare'], true)) {
                        throw new \RuntimeException('Livrabilele pot fi adăugate doar în Draft sau Implementare.');
                    }
                    $projectModel->addDeliverable((int)$_POST['id_act'], trim($_POST['denumire']), $_POST['termen_asumat']);
                    $this->setFlash('success', 'Livrabilul a fost adăugat.');
                    $redirectTab = 'activitati';
                    break;

                case 'update_livrabil_stare':
                    $newLivState = $_POST['stare'] ?? '';
                    if ($user['rol'] === 'Solicitant') {
                        if (!in_array($newLivState, ['In_Lucru', 'Livrat'], true)) {
                            throw new \RuntimeException('Solicitantul poate marca livrabilul doar În Lucru sau Livrat.');
                        }
                    } elseif ($user['rol'] === 'Ofiter_AM') {
                        if (!in_array($newLivState, ['Aprobat', 'Respins'], true)) {
                            throw new \RuntimeException('Ofițerul AM poate doar să aprobe sau să respingă livrabilul.');
                        }
                    } else {
                        throw new \RuntimeException('Nu aveți permisiunea de a modifica starea livrabilului.');
                    }
                    $projectModel->updateDeliverableState((int)$_POST['id_livrabil'], $id, $newLivState);
                    $this->setFlash('success', 'Starea livrabilului a fost actualizată.');
                    $redirectTab = 'activitati';
                    break;

                case 'add_evaluare':
                    $this->requireRole(['Evaluator']);
                    if ($proiect['stare'] !== 'In_Evaluare') {
                        throw new \RuntimeException('Evaluarea se poate face doar pentru proiecte aflate în starea In_Evaluare.');
                    }
                    $evalModel->create($id, (int)$user['id_user'], (float)$_POST['punctaj_total'], $_POST['decizie'], trim($_POST['observatii']));
                    $this->setFlash('success', 'Evaluarea a fost înregistrată!');
                    $redirectTab = 'evaluari';
                    break;

                case 'add_cr':
                    $this->requireRole(['Solicitant']);
                    if ($proiect['stare'] !== 'Implementare') {
                        throw new \RuntimeException('Cererile de rambursare se pot depune doar în starea Implementare.');
                    }
                    $rambModel->createClaim($id, trim($_POST['numar_cerere']), (float)$_POST['suma_solicitata'], $_POST['livrabile'] ?? []);
                    $this->setFlash('success', 'Cererea de rambursare a fost depusă!');
                    $redirectTab = 'rambursari';
                    break;

                case 'add_doc':
                    $this->requireRole(['Solicitant']);
                    if ($proiect['stare'] !== 'Implementare') {
                        throw new \RuntimeException('Documentele justificative se pot atașa doar în starea Implementare.');
                    }
                    $rambModel->addDocument((int)$_POST['id_cr'], (int)$_POST['id_linie'], $_POST['tip_document'], trim($_POST['numar_document']), $_POST['data_emiterii'], (float)$_POST['valoare_totala'], (float)$_POST['valoare_eligibila_solicitata']);
                    $this->setFlash('success', 'Documentul justificativ a fost atașat!');
                    $redirectTab = 'rambursari';
                    break;

                case 'verify_cr':
                    $this->requireRole(['Ofiter_AM']);
                    $sumaAprob = $_POST['suma_aprobata'] !== '' ? (float)$_POST['suma_aprobata'] : null;
                    $rambModel->verifyClaim((int)$_POST['id_cr'], (int)$user['id_user'], $_POST['stare_cr'], $sumaAprob);
                    $this->setFlash('success', 'Cererea de rambursare a fost verificată.');
                    $redirectTab = 'rambursari';
                    break;
            }
        } catch (\Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }

        $this->redirect("/proiect?id={$id}&tab={$redirectTab}");
    }
}
