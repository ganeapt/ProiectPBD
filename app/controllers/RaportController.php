<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectModel;
use App\Models\RambursareModel;

class RaportController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $projectModel = new ProjectModel();
        $rambModel = new RambursareModel();

        $this->view('rapoarte/index', [
            'executie' => $projectModel->getBudgetExecutionReport(),
            'ajustate' => $rambModel->getAdjustedDocumentsAudit(),
            'pageTitle' => 'Rapoarte & Audit - ProiectPBD',
        ]);
    }
}
