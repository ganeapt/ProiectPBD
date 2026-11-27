<?php
declare(strict_types=1);

use App\Core\Security;

function e(?string $val): string {
    return Security::e($val);
}

function formatMoney(float|string|null $amount): string {
    if ($amount === null) return '-';
    return number_format((float)$amount, 2, ',', '.') . ' RON';
}

function badgeStareProiect(string $stare): string {
    $map = [
        'Draft' => 'secondary',
        'Depus' => 'info',
        'In_Evaluare' => 'warning text-dark',
        'Contractat' => 'primary',
        'Implementare' => 'success',
        'Finalizat' => 'dark',
        'Respins' => 'danger',
    ];
    $cls = $map[$stare] ?? 'secondary';
    return "<span class=\"badge bg-{$cls}\">" . e(str_replace('_', ' ', $stare)) . "</span>";
}

function badgeStareCR(string $stare): string {
    $map = [
        'Depusa' => 'info',
        'In_Verificare' => 'warning text-dark',
        'Aprobata' => 'success',
        'Respinsa' => 'danger',
    ];
    $cls = $map[$stare] ?? 'secondary';
    return "<span class=\"badge bg-{$cls}\">" . e(str_replace('_', ' ', $stare)) . "</span>";
}
