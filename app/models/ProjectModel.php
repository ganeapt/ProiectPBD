<?php
declare(strict_types=1);

namespace App\Models;

use App\Config\Database;
use PDO;

class ProjectModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getStats(): array {
        return [
            'total_proiecte' => (int)$this->db->query("SELECT COUNT(*) FROM proiect")->fetchColumn(),
            'buget_total' => (float)$this->db->query("SELECT COALESCE(SUM(valoare_totala), 0) FROM proiect")->fetchColumn(),
            'total_aprobat' => (float)$this->db->query("SELECT COALESCE(SUM(suma_aprobata), 0) FROM cerere_rambursare WHERE stare_cr = 'Aprobata'")->fetchColumn(),
        ];
    }

    public function getAll(string $stare = '', int $limit = 0): array {
        $sql = "
            SELECT p.*, a.cod_apel, o.denumire AS lider,
                   (SELECT COUNT(*) FROM proiect_partener pp WHERE pp.id_proiect = p.id_proiect) AS nr_parteneri
            FROM proiect p
            JOIN apel_proiect a ON p.id_apel = a.id_apel
            JOIN organizatie o ON p.id_org_lider = o.id_org
        ";
        $params = [];
        if ($stare !== '') {
            $sql .= " WHERE p.stare = ?";
            $params[] = $stare;
        }
        $sql .= " ORDER BY p.id_proiect DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT p.*, a.cod_apel, a.titlu AS titlu_apel, o.denumire AS lider_denumire, o.cui AS lider_cui
            FROM proiect p
            JOIN apel_proiect a ON p.id_apel = a.id_apel
            JOIN organizatie o ON p.id_org_lider = o.id_org
            WHERE p.id_proiect = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $idApel, int $idOrgLider, string $titlu, string $acronim, int $durata, float $valoare): int {
        $stmt = $this->db->prepare("
            INSERT INTO proiect (id_apel, id_org_lider, titlu, acronim, durata_luni, stare, valoare_totala)
            VALUES (?, ?, ?, ?, ?, 'Draft', ?)
        ");
        $stmt->execute([$idApel, $idOrgLider, $titlu, $acronim, $durata, $valoare]);
        return (int)$this->db->lastInsertId();
    }

    public function updateState(int $id, string $newState): bool {
        if ($newState === 'Depus') {
            $stmt = $this->db->prepare("UPDATE proiect SET stare = ?, data_depunere = NOW() WHERE id_proiect = ?");
        } else {
            $stmt = $this->db->prepare("UPDATE proiect SET stare = ? WHERE id_proiect = ?");
        }
        return $stmt->execute([$newState, $id]);
    }

    public function getPartners(int $idProiect): array {
        $stmt = $this->db->prepare("SELECT o.* FROM proiect_partener pp JOIN organizatie o ON pp.id_org = o.id_org WHERE pp.id_proiect = ?");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function addPartner(int $idProiect, int $idOrg): bool {
        $stmt = $this->db->prepare("INSERT INTO proiect_partener (id_proiect, id_org) VALUES (?, ?)");
        return $stmt->execute([$idProiect, $idOrg]);
    }

    public function removePartner(int $idProiect, int $idOrg): bool {
        $stmt = $this->db->prepare("DELETE FROM proiect_partener WHERE id_proiect = ? AND id_org = ?");
        return $stmt->execute([$idProiect, $idOrg]);
    }

    public function getBudgetLines(int $idProiect): array {
        $stmt = $this->db->prepare("
            SELECT lb.*, COALESCE(SUM(dj.valoare_aprobata), 0) AS consumat
            FROM linie_bugetara lb
            LEFT JOIN document_justificativ dj ON lb.id_linie = dj.id_linie
            WHERE lb.id_proiect = ?
            GROUP BY lb.id_linie
        ");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function addBudgetLine(int $idProiect, string $categorie, float $eligibila, float $neeligibila): bool {
        $stmt = $this->db->prepare("INSERT INTO linie_bugetara (id_proiect, categorie_cheltuiala, suma_eligibila, suma_neeligibila) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$idProiect, $categorie, $eligibila, $neeligibila]);
    }

    public function getActivities(int $idProiect): array {
        $stmt = $this->db->prepare("
            SELECT a.*, lb.categorie_cheltuiala
            FROM activitate a
            JOIN linie_bugetara lb ON a.id_linie = lb.id_linie
            WHERE a.id_proiect = ?
            ORDER BY a.numar_ordine ASC
        ");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function addActivity(int $idProiect, int $idLinie, string $nrOrdine, string $titlu, string $start, string $end): bool {
        $stmt = $this->db->prepare("INSERT INTO activitate (id_proiect, id_linie, numar_ordine, titlu, data_inceput, data_sfarsit, procent_realizat) VALUES (?, ?, ?, ?, ?, ?, 0)");
        return $stmt->execute([$idProiect, $idLinie, $nrOrdine, $titlu, $start, $end]);
    }

    public function updateActivityProgress(int $idAct, int $idProiect, int $procent): bool {
        $stmt = $this->db->prepare("UPDATE activitate SET procent_realizat = ? WHERE id_act = ? AND id_proiect = ?");
        return $stmt->execute([$procent, $idAct, $idProiect]);
    }

    public function getDeliverables(int $idProiect): array {
        $stmt = $this->db->prepare("
            SELECT l.*, a.numar_ordine, cr.numar_cerere
            FROM livrabil l
            JOIN activitate a ON l.id_act = a.id_act
            LEFT JOIN cerere_rambursare cr ON l.id_cr = cr.id_cr
            WHERE a.id_proiect = ?
            ORDER BY l.termen_asumat ASC
        ");
        $stmt->execute([$idProiect]);
        return $stmt->fetchAll();
    }

    public function addDeliverable(int $idAct, string $denumire, string $termen): bool {
        $stmt = $this->db->prepare("INSERT INTO livrabil (id_act, denumire, termen_asumat, stare) VALUES (?, ?, ?, 'In_Lucru')");
        return $stmt->execute([$idAct, $denumire, $termen]);
    }

    public function updateDeliverableState(int $idLivrabil, int $idProiect, string $stare): bool {
        $stmt = $this->db->prepare("
            UPDATE livrabil l
            JOIN activitate a ON l.id_act = a.id_act
            SET l.stare = ?
            WHERE l.id_livrabil = ? AND a.id_proiect = ?
        ");
        return $stmt->execute([$stare, $idLivrabil, $idProiect]);
    }

    public function getBudgetExecutionReport(): array {
        return $this->db->query("
            SELECT 
                p.id_proiect, p.acronim, p.valoare_totala AS buget_contractat, p.stare,
                COALESCE((SELECT SUM(cr.suma_solicitata) FROM cerere_rambursare cr WHERE cr.id_proiect = p.id_proiect), 0) AS total_solicitat,
                COALESCE((SELECT SUM(cr.suma_aprobata) FROM cerere_rambursare cr WHERE cr.id_proiect = p.id_proiect AND cr.stare_cr = 'Aprobata'), 0) AS total_aprobat
            FROM proiect p
            ORDER BY p.valoare_totala DESC
        ")->fetchAll();
    }
}
