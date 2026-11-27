<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class RambursareModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        return $this->db->query("
            SELECT cr.*, p.acronim, p.id_proiect, CONCAT(u.prenume, ' ', u.nume) AS ofiter_nume
            FROM cerere_rambursare cr
            JOIN proiect p ON cr.id_proiect = p.id_proiect
            LEFT JOIN utilizator u ON cr.id_ofiter = u.id_user
            ORDER BY cr.data_depunere DESC
        ")->fetchAll();
    }

    public function getPending(): array {
        return $this->db->query("
            SELECT cr.*, p.acronim
            FROM cerere_rambursare cr
            JOIN proiect p ON cr.id_proiect = p.id_proiect
            WHERE cr.stare_cr IN ('Depusa', 'In_Verificare')
            ORDER BY cr.data_depunere ASC
        ")->fetchAll();
    }

    public function getByProject(int $idProiect): array {
        $stmt = $this->db->prepare("
            SELECT cr.*, CONCAT(u.prenume, ' ', u.nume) AS ofiter_nume
            FROM cerere_rambursare cr
            LEFT JOIN utilizator u ON cr.id_ofiter = u.id_user
            WHERE cr.id_proiect = ?
            ORDER BY cr.id_cr DESC
        ");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function getDocumentsByProject(int $idProiect): array {
        $stmt = $this->db->prepare("
            SELECT dj.*, lb.categorie_cheltuiala, cr.numar_cerere
            FROM document_justificativ dj
            JOIN cerere_rambursare cr ON dj.id_cr = cr.id_cr
            JOIN linie_bugetara lb ON dj.id_linie = lb.id_linie
            WHERE cr.id_proiect = ?
            ORDER BY dj.id_doc DESC
        ");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function createClaim(int $idProiect, string $numar, float $sumaSolicitata, array $livrabilIds = []): int {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO cerere_rambursare (id_proiect, numar_cerere, data_depunere, suma_solicitata, stare_cr)
                VALUES (?, ?, CURRENT_DATE(), ?, 'Depusa')
            ");
            $stmt->execute([$idProiect, $numar, $sumaSolicitata]);
            $newCrId = (int)$this->db->lastInsertId();

            if (!empty($livrabilIds)) {
                $upd = $this->db->prepare("
                    UPDATE livrabil
                    SET id_cr = ?
                    WHERE id_livrabil = ? AND id_cr IS NULL
                      AND id_act IN (SELECT id_act FROM activitate WHERE id_proiect = ?)
                ");
                foreach ($livrabilIds as $lid) {
                    $upd->execute([$newCrId, (int)$lid, $idProiect]);
                }
            }
            $this->db->commit();
            return $newCrId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function addDocument(int $idCr, int $idLinie, string $tip, string $numar, string $dataEmiterii, float $valTotala, float $valEligibila): bool {
        $stmt = $this->db->prepare("
            INSERT INTO document_justificativ (id_cr, id_linie, tip_document, numar_document, data_emiterii, valoare_totala, valoare_eligibila_solicitata)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$idCr, $idLinie, $tip, $numar, $dataEmiterii, $valTotala, $valEligibila]);
    }

    public function verifyClaim(int $idCr, int $idOfiter, string $stareCr, ?float $sumaAprobata): bool {
        $stmt = $this->db->prepare("
            UPDATE cerere_rambursare SET id_ofiter = ?, stare_cr = ?, suma_aprobata = ? WHERE id_cr = ?
        ");
        return $stmt->execute([$idOfiter, $stareCr, $sumaAprobata, $idCr]);
    }

    public function getAdjustedDocumentsAudit(): array {
        return $this->db->query("
            SELECT dj.*, p.acronim, cr.numar_cerere, lb.categorie_cheltuiala,
                   (dj.valoare_eligibila_solicitata - dj.valoare_aprobata) AS taiat
            FROM document_justificativ dj
            JOIN cerere_rambursare cr ON dj.id_cr = cr.id_cr
            JOIN proiect p ON cr.id_proiect = p.id_proiect
            JOIN linie_bugetara lb ON dj.id_linie = lb.id_linie
            WHERE dj.valoare_aprobata IS NOT NULL AND dj.valoare_aprobata < dj.valoare_eligibila_solicitata
        ")->fetchAll();
    }
}
