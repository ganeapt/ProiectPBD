<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectModel;
use App\Models\ApelModel;
use App\Models\RambursareModel;

class DashboardController extends Controller {
    public function index(): void {
        $this->requireAuth();

        $projectModel = new ProjectModel();
        $apelModel = new ApelModel();
        $rambursareModel = new RambursareModel();

        $stats = $projectModel->getStats();
        $stats['apeluri_active'] = $apelModel->countActive();

        $this->view('dashboard/index', [
            'stats' => $stats,
            'proiecte' => $projectModel->getAll('', 6),
            'cereriPending' => $rambursareModel->getPending(),
            'pageTitle' => 'Dashboard - ProiectPBD',
        ]);
    }
}
