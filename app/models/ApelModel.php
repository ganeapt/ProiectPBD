<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class ApelModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllWithCounts(): array {
        return $this->db->query("
            SELECT a.*, COUNT(p.id_proiect) AS nr_proiecte
            FROM apel_proiect a
            LEFT JOIN proiect p ON a.id_apel = p.id_apel
            GROUP BY a.id_apel
            ORDER BY a.data_limita DESC
        ")->fetchAll();
    }

    public function countActive(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM apel_proiect WHERE data_limita >= CURRENT_DATE()")->fetchColumn();
    }

    public function create(string $cod, string $titlu, float $buget, float $cofinantare, string $start, string $limita): bool {
        $stmt = $this->db->prepare("
            INSERT INTO apel_proiect (cod_apel, titlu, buget_total, procent_cofinantare, data_start, data_limita)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$cod, $titlu, $buget, $cofinantare, $start, $limita]);
    }

    public function delete(int $idApel): bool {
        $stmt = $this->db->prepare("DELETE FROM apel_proiect WHERE id_apel = ?");
        return $stmt->execute([$idApel]);
    }
}
