/**
 * Kanban Board JavaScript
 * Funcionalidade de drag and drop para o Kanban
 */

document.addEventListener('DOMContentLoaded', function() {
    initKanban();
});

function initKanban() {
    const columns = document.querySelectorAll('.kanban-items');
    const cards = document.querySelectorAll('.kanban-card');

    let draggedCard = null;
    let sourceColumn = null;

    // Configura cards para drag
    cards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedCard = this;
            sourceColumn = this.parentElement;
            this.classList.add('dragging');

            // Define dados do drag
            e.dataTransfer.setData('text/plain', this.dataset.id);
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedCard = null;
            sourceColumn = null;

            // Remove highlight de todas as colunas
            columns.forEach(col => col.classList.remove('drag-over'));
        });

        // Touch events para mobile
        card.addEventListener('touchstart', handleTouchStart, { passive: false });
        card.addEventListener('touchmove', handleTouchMove, { passive: false });
        card.addEventListener('touchend', handleTouchEnd);
    });

    // Configura colunas para drop
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function(e) {
            if (e.relatedTarget && !this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
        });

        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            const osId = e.dataTransfer.getData('text/plain');
            const newStatus = this.dataset.status;

            if (draggedCard && newStatus) {
                // Move visualmente o card
                this.appendChild(draggedCard);

                // Atualiza status no servidor
                updateOsStatus(osId, newStatus, draggedCard);
            }
        });
    });

    // Atualiza contadores
    updateColumnCounts();
}

/**
 * Atualiza status da OS via API
 */
function updateOsStatus(osId, newStatus, card) {
    fetch(base_url + 'kanban/api_update_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            os_id: osId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Adiciona animação de sucesso
            card.classList.add('new-card');
            setTimeout(() => card.classList.remove('new-card'), 300);

            // Atualiza contadores
            updateColumnCounts();

            // Mostra notificação
            if (typeof $.notify !== 'undefined') {
                $.notify({
                    message: 'Status atualizado com sucesso!'
                }, {
                    type: 'success',
                    delay: 2000
                });
            }
        } else {
            // Reverte a movimentação
            revertCardMove(card);

            // Mostra erro
            if (typeof $.notify !== 'undefined') {
                $.notify({
                    message: 'Erro ao atualizar status: ' + data.error
                }, {
                    type: 'danger',
                    delay: 3000
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        revertCardMove(card);
    });
}

/**
 * Reverte movimento do card em caso de erro
 */
function revertCardMove(card) {
    // Recarrega a página para restaurar estado original
    location.reload();
}

/**
 * Atualiza contadores das colunas
 */
function updateColumnCounts() {
    const columns = document.querySelectorAll('.kanban-column');

    columns.forEach(column => {
        const items = column.querySelectorAll('.kanban-card');
        const countBadge = column.querySelector('.kanban-count');

        if (countBadge) {
            countBadge.textContent = items.length;
        }
    });
}

/**
 * Handlers para touch events (mobile)
 */
let touchStartY = 0;
let touchCurrentCard = null;

function handleTouchStart(e) {
    touchCurrentCard = this;
    touchStartY = e.touches[0].clientY;
    this.style.opacity = '0.5';
}

function handleTouchMove(e) {
    if (!touchCurrentCard) return;

    e.preventDefault();

    const touch = e.touches[0];
    const target = document.elementFromPoint(touch.clientX, touch.clientY);

    // Encontra a coluna
    const column = target ? target.closest('.kanban-items') : null;

    if (column) {
        // Remove highlight de todas as colunas
        document.querySelectorAll('.kanban-items').forEach(c => c.classList.remove('drag-over'));
        column.classList.add('drag-over');
    }
}

function handleTouchEnd(e) {
    if (!touchCurrentCard) return;

    touchCurrentCard.style.opacity = '1';

    const touch = e.changedTouches[0];
    const target = document.elementFromPoint(touch.clientX, touch.clientY);
    const column = target ? target.closest('.kanban-items') : null;

    if (column && column !== touchCurrentCard.parentElement) {
        const osId = touchCurrentCard.dataset.id;
        const newStatus = column.dataset.status;

        column.appendChild(touchCurrentCard);
        updateOsStatus(osId, newStatus, touchCurrentCard);
    }

    // Remove highlight
    document.querySelectorAll('.kanban-items').forEach(c => c.classList.remove('drag-over'));

    touchCurrentCard = null;
}

/**
 * Recarrega dados do Kanban
 */
function refreshKanban() {
    fetch(base_url + 'kanban/api_get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualiza a visualização
                location.reload();
            }
        })
        .catch(error => console.error('Error refreshing:', error));
}

// Auto-refresh a cada 60 segundos
setInterval(refreshKanban, 60000);
