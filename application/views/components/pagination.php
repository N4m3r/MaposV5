<?php
/**
 * Componente: Pagination
 * Props: total, perPage, currentPage, baseUrl, showInfo
 */

$total = $total ?? 0;
$perPage = $perPage ?? 20;
$currentPage = $currentPage ?? 1;
$baseUrl = $baseUrl ?? '';
$showInfo = $showInfo ?? true;

$totalPages = (int) ceil($total / $perPage);

if ($totalPages <= 1) {
    return;
}

$start = max(1, $currentPage - 2);
$end = min($totalPages, $currentPage + 2);
?>

<?php if ($showInfo): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        Mostrando <?= (($currentPage - 1) * $perPage) + 1 ?> - <?= min($currentPage * $perPage, $total) ?> de <?= $total ?> registros
    </div>
</div>
<?php endif; ?>

<nav aria-label="Navegação de página">
    <ul class="pagination justify-content-center">
        <!-- Botão Anterior -->
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : '#' ?>">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>

        <?php if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $baseUrl . '?page=1' ?>">1</a>
            </li>
            <?php if ($start > 2): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                <a class="page-link" href="<?= $baseUrl . '?page=' . $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?

        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
            <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?= $baseUrl . '?page=' . $totalPages ?>"><?= $totalPages ?></a>
            </li>
        <?php endif; ?

        <!-- Botão Próximo -->
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $currentPage < $totalPages ? $baseUrl . '?page=' . ($currentPage + 1) : '#' ?>">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
