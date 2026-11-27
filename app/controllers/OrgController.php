<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OrgModel;
use App\Models\UserModel;

class OrgController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $orgModel = new OrgModel();
        $userModel = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireRole(['Administrator']);
            $this->verifyCsrfOrFail();
            $act = $_POST['action'] ?? '';
            try {
                if ($act === 'create_org') {
                    $orgModel->create(
                        trim($_POST['denumire']),
                        trim($_POST['cui']),
                        trim($_POST['iban']),
                        trim($_POST['adresa']),
                        $_POST['tip_organizatie']
                    );
                    $this->setFlash('success', 'Organizația a fost înregistrată!');
                } elseif ($act === 'create_user') {
                    $userModel->create(
                        (int)$_POST['id_org'],
                        trim($_POST['nume']),
                        trim($_POST['prenume']),
                        trim($_POST['email']),
                        $_POST['parola'],
                        $_POST['rol']
                    );
                    $this->setFlash('success', 'Utilizatorul a fost creat cu parolă criptată Bcrypt!');
                } elseif ($act === 'toggle_user') {
                    $userModel->toggleActive((int)$_POST['id_user']);
                    $this->setFlash('info', 'Statusul utilizatorului a fost actualizat.');
                }
            } catch (\PDOException $e) {
                $this->setFlash('danger', 'Eroare: ' . $e->getMessage());
            }
            $this->redirect('/organizatii');
        }

        $this->view('organizatii/index', [
            'organizatii' => $orgModel->getAll(),
            'utilizatori' => $userModel->getAll(),
            'pageTitle' => 'Organizații & Utilizatori - ProiectPBD',
        ]);
    }
}
