<?php
declare(strict_types=1);

// Front Controller & Router
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $parts = explode('\\', $relative);
    $file = array_pop($parts) . '.php';
    $dir = strtolower(implode(DIRECTORY_SEPARATOR, $parts));
    $path = __DIR__ . '/../' . ($dir ? $dir . DIRECTORY_SEPARATOR : '') . $file;
    if (file_exists($path)) {
        require_once $path;
    }
});

require_once __DIR__ . '/../core/ViewHelpers.php';

use App\Core\Security;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProjectController;
use App\Controllers\ApelController;
use App\Controllers\OrgController;
use App\Controllers\RambursareController;
use App\Controllers\EvaluareController;
use App\Controllers\RaportController;

Security::initSession();

// Security Headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; font-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; frame-ancestors 'self'; form-action 'self';");
header('Cache-Control: no-store, no-cache, must-revalidate, private');
header('Pragma: no-cache');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$uri = rtrim($uri, '/') ?: '/';

switch ($uri) {
    case '/':
        (new DashboardController())->index();
        break;
    case '/login':
        (new AuthController())->login();
        break;
    case '/register':
        (new AuthController())->register();
        break;
    case '/logout':
        (new AuthController())->logout();
        break;
    case '/proiecte':
        (new ProjectController())->index();
        break;
    case '/proiecte/create':
        (new ProjectController())->create();
        break;
    case '/proiect':
        (new ProjectController())->show();
        break;
    case '/proiect/action':
        (new ProjectController())->handleAction();
        break;
    case '/apeluri':
        (new ApelController())->index();
        break;
    case '/organizatii':
        (new OrgController())->index();
        break;
    case '/rambursari':
        (new RambursareController())->index();
        break;
    case '/evaluari':
        (new EvaluareController())->index();
        break;
    case '/rapoarte':
        (new RaportController())->index();
        break;
    default:
        http_response_code(404);
        echo '404 - Pagina nu a fost găsită.';
        break;
}
