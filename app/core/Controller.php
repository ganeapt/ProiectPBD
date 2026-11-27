<?php
declare(strict_types=1);

namespace App\Core;

class Controller {
    protected function view(string $viewPath, array $data = [], bool $useLayout = true): void {
        extract($data, EXTR_SKIP);
        $user = $_SESSION['user'] ?? null;
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            die("View not found: " . Security::e($viewPath));
        }

        if ($useLayout) {
            require __DIR__ . '/../views/layouts/header.php';
            require $viewFile;
            require __DIR__ . '/../views/layouts/footer.php';
        } else {
            require $viewFile;
        }
    }

    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function requireAuth(): array {
        if (empty($_SESSION['user']['id_user'])) {
            $this->redirect('/login');
        }
        // Re-validate active status & role from DB
        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT u.id_user, u.id_org, u.nume, u.prenume, u.email, u.rol, u.activ, o.denumire AS org_denumire FROM utilizator u JOIN organizatie o ON u.id_org = o.id_org WHERE u.id_user = ?");
        $stmt->execute([(int)$_SESSION['user']['id_user']]);
        $fresh = $stmt->fetch();
        if (!$fresh || (int)$fresh['activ'] !== 1) {
            $_SESSION = [];
            session_destroy();
            $this->redirect('/login');
        }
        $_SESSION['user'] = $fresh;
        return $fresh;
    }

    protected function requireRole(array $allowedRoles): void {
        $user = $this->requireAuth();
        if (!in_array($user['rol'], $allowedRoles, true)) {
            $this->setFlash('danger', 'Acces interzis: Nu aveți permisiunile necesare pentru această acțiune.');
            $this->redirect('/');
        }
    }

    protected function verifyCsrfOrFail(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
                http_response_code(403);
                die('Eroare de securitate: Token CSRF invalid sau expirat. Reîncarcă pagina și încearcă din nou.');
            }
        }
    }

    protected function requirePostAndCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('405 Method Not Allowed');
        }
        $this->verifyCsrfOrFail();
    }
}
