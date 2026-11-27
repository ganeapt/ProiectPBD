<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ApelModel;

class ApelController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $apelModel = new ApelModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireRole(['Administrator', 'Ofiter_AM']);
            $this->verifyCsrfOrFail();
            $act = $_POST['action'] ?? '';
            try {
                if ($act === 'create') {
                    $apelModel->create(
                        trim($_POST['cod_apel']),
                        trim($_POST['titlu']),
                        (float)$_POST['buget_total'],
                        (float)$_POST['procent_cofinantare'],
                        $_POST['data_start'],
                        $_POST['data_limita']
                    );
                    $this->setFlash('success', 'Apelul de proiecte a fost adăugat!');
                } elseif ($act === 'delete') {
                    $apelModel->delete((int)$_POST['id_apel']);
                    $this->setFlash('success', 'Apelul a fost șters.');
                }
            } catch (\PDOException $e) {
                $this->setFlash('danger', 'Eroare: ' . $e->getMessage());
            }
            $this->redirect('/apeluri');
        }

        $this->view('apeluri/index', [
            'apeluri' => $apelModel->getAllWithCounts(),
            'pageTitle' => 'Apeluri de Finanțare - ProiectPBD',
        ]);
    }
}
