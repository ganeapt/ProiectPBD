<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class OrgModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM organizatie ORDER BY denumire ASC")->fetchAll();
    }

    public function create(string $denumire, string $cui, string $iban, string $adresa, string $tip): bool {
        $stmt = $this->db->prepare("
            INSERT INTO organizatie (denumire, cui, iban, adresa, tip_organizatie)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$denumire, $cui, $iban, $adresa, $tip]);
    }
}
