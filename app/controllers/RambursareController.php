<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\RambursareModel;

class RambursareController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $model = new RambursareModel();
        $this->view('rambursari/index', [
            'cereri' => $model->getAll(),
            'pageTitle' => 'Cereri de Rambursare - ProiectPBD',
        ]);
    }
}
