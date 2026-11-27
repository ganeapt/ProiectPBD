<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\EvaluareModel;

class EvaluareController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $model = new EvaluareModel();
        $this->view('evaluari/index', [
            'evaluari' => $model->getAll(),
            'pageTitle' => 'Evaluări Proiecte - ProiectPBD',
        ]);
    }
}
