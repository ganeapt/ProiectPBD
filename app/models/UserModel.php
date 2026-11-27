<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, o.denumire AS org_denumire
            FROM utilizator u
            JOIN organizatie o ON u.id_org = o.id_org
            WHERE u.email = ? AND u.activ = 1
        ");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getAll(): array {
        return $this->db->query("
            SELECT u.id_user, u.id_org, u.nume, u.prenume, u.email, u.rol, u.activ, o.denumire AS org_denumire
            FROM utilizator u
            JOIN organizatie o ON u.id_org = o.id_org
            ORDER BY u.id_user DESC
        ")->fetchAll();
    }

    public function create(int $idOrg, string $nume, string $prenume, string $email, string $password, string $rol): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO utilizator (id_org, nume, prenume, email, parola_hash, rol, activ)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");
        return $stmt->execute([$idOrg, $nume, $prenume, $email, $hash, $rol]);
    }

    public function toggleActive(int $idUser): bool {
        $stmt = $this->db->prepare("UPDATE utilizator SET activ = 1 - activ WHERE id_user = ?");
        return $stmt->execute([$idUser]);
    }
}
