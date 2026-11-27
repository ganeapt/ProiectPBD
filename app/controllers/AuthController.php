<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Security;
use App\Models\UserModel;
use App\Models\OrgModel;

class AuthController extends Controller {
    public function login(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect('/');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrFail();

            $lockout = Security::checkRateLimit('login_attempts', 5, 60);
            if ($lockout !== null) {
                $error = "Prea multe încercări eșuate. Încearcă din nou peste {$lockout} secunde.";
            } else {
                $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
                $parola = $_POST['parola'] ?? '';

                if (!$email || $parola === '') {
                    $error = 'Te rugăm să introduci un email valid și parola.';
                } else {
                    $userModel = new UserModel();
                    $user = $userModel->findByEmail($email);

                    if ($user && password_verify($parola, $user['parola_hash'])) {
                        Security::clearAttempts('login_attempts');
                        session_regenerate_id(true);
                        unset($user['parola_hash']);
                        $_SESSION['user'] = $user;
                        $this->setFlash('success', "Bun venit, {$user['prenume']} {$user['nume']}!");
                        $this->redirect('/');
                    } else {
                        Security::recordFailedAttempt('login_attempts', 5, 60);
                        $error = 'Email sau parolă incorectă (sau cont dezactivat).';
                    }
                }
            }
        }

        $this->view('auth/login', ['error' => $error, 'pageTitle' => 'Autentificare - ProiectPBD'], false);
    }

    public function register(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect('/');
        }

        $orgModel = new OrgModel();
        $organizatii = $orgModel->getAll();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrfOrFail();

            $nume = trim($_POST['nume'] ?? '');
            $prenume = trim($_POST['prenume'] ?? '');
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $parola = $_POST['parola'] ?? '';
            $parolaConfirm = $_POST['parola_confirm'] ?? '';
            $idOrg = (int)($_POST['id_org'] ?? 0);

            if (!$nume || !$prenume || !$email || strlen($parola) < 8) {
                $error = 'Toate câmpurile sunt obligatorii, iar parola trebuie să aibă minimum 8 caractere.';
            } elseif ($parola !== $parolaConfirm) {
                $error = 'Parolele nu coincid.';
            } else {
                $userModel = new UserModel();
                if ($userModel->findByEmail($email)) {
                    $error = 'Există deja un cont cu această adresă de email.';
                } else {
                    // Self-registration is restricted to Solicitant role for security
                    $userModel->create($idOrg, $nume, $prenume, $email, $parola, 'Solicitant');
                    $newUser = $userModel->findByEmail($email);
                    if ($newUser) {
                        session_regenerate_id(true);
                        unset($newUser['parola_hash']);
                        $_SESSION['user'] = $newUser;
                        $this->setFlash('success', "Contul a fost creat și ești autentificat! Bun venit, {$newUser['prenume']}!");
                        $this->redirect('/');
                    }
                    $this->redirect('/login');
                }
            }
        }

        $this->view('auth/register', [
            'organizatii' => $organizatii,
            'error' => $error,
            'pageTitle' => 'Înregistrare - ProiectPBD'
        ], false);
    }

    public function logout(): void {
        $this->requirePostAndCsrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $this->redirect('/login');
    }
}
