<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class EvaluareModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query("
            SELECT e.*, p.acronim, p.id_proiect, a.cod_apel, CONCAT(u.prenume, ' ', u.nume) AS evaluator_nume
            FROM evaluare e
            JOIN proiect p ON e.id_proiect = p.id_proiect
            JOIN apel_proiect a ON p.id_apel = a.id_apel
            JOIN utilizator u ON e.id_evaluator = u.id_user
            ORDER BY e.data_evaluare DESC
        ")->fetchAll();
    }

    public function getByProject(int $idProiect): array {
        $stmt = $this->db->prepare("
            SELECT e.*, CONCAT(u.prenume, ' ', u.nume) AS evaluator_nume, o.denumire AS evaluator_org
            FROM evaluare e
            JOIN utilizator u ON e.id_evaluator = u.id_user
            JOIN organizatie o ON u.id_org = o.id_org
            WHERE e.id_proiect = ?
            ORDER BY e.data_evaluare DESC
        ");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function create(int $idProiect, int $idEvaluator, float $punctaj, string $decizie, string $observatii): bool {
        $stmt = $this->db->prepare("
            INSERT INTO evaluare (id_proiect, id_evaluator, punctaj_total, decizie, observatii, data_evaluare)
            VALUES (?, ?, ?, ?, ?, CURRENT_DATE())
        ");
        return $stmt->execute([$idProiect, $idEvaluator, $punctaj, $decizie, $observatii]);
    }
}
