/**
 * Kanban com drag & drop nativo HTML5 + React state.
 * Persistencia via API: /kanban/api_move (POST).
 *
 * Otimistico: atualiza UI primeiro, faz rollback se API falhar.
 */
import { useState, useEffect, type DragEvent } from 'react';
import { CCard, CCardBody, CSpinner, CBadge } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { getCards, moveCard } from '../api/kanban';
import type { KanbanCard, OsStatus } from '../types';

interface Column {
    id: OsStatus;
    label: string;
    color: string;
    icon: string;
}

const COLUMNS: Column[] = [
    { id: 'Aberto',           label: 'Aberto',           color: 'secondary', icon: 'cilFolderOpen' },
    { id: 'Orcamento',        label: 'Orcamento',        color: 'info',      icon: 'cilCalculator' },
    { id: 'Aprovado',         label: 'Aprovado',         color: 'success',   icon: 'cilCheck' },
    { id: 'Em Andamento',     label: 'Em Andamento',     color: 'primary',   icon: 'cilCog' },
    { id: 'Aguardando Pecas', label: 'Aguardando Pecas', color: 'warning',   icon: 'cilBox' },
    { id: 'Pronto',           label: 'Pronto',           color: 'info',      icon: 'cilClock' },
    { id: 'Finalizado',       label: 'Finalizado',       color: 'success',   icon: 'cilCheckCircle' },
    { id: 'Cancelado',        label: 'Cancelado',        color: 'danger',    icon: 'cilX' },
];

export default function Kanban() {
    const [columns, setColumns] = useState<Record<OsStatus, KanbanCard[]>>({} as Record<OsStatus, KanbanCard[]>);
    const [loading, setLoading] = useState(true);
    const [draggedCard, setDraggedCard] = useState<{ card: KanbanCard; from: OsStatus } | null>(null);
    const [dragOverCol, setDragOverCol] = useState<OsStatus | null>(null);

    useEffect(() => {
        let mounted = true;
        getCards()
            .then((data) => { if (mounted) setColumns(data); })
            .catch((err) => console.error('[Kanban] Erro:', err))
            .finally(() => { if (mounted) setLoading(false); });
        return () => { mounted = false; };
    }, []);

    function handleDragStart(card: KanbanCard, from: OsStatus) {
        setDraggedCard({ card, from });
    }

    async function handleDrop(toCol: OsStatus) {
        if (!draggedCard) return;
        const { card, from } = draggedCard;
        if (from === toCol) {
            setDraggedCard(null);
            setDragOverCol(null);
            return;
        }

        // Backup para rollback em caso de erro
        const previous = { ...columns };

        // Otimista: atualiza UI antes da API
        setColumns((prev) => {
            const next = { ...prev };
            next[from] = (next[from] || []).filter((c) => c.id !== card.id);
            next[toCol] = [...(next[toCol] || []), { ...card, status: toCol }];
            return next;
        });

        try {
            const ok = await moveCard(card.id, from, toCol);
            if (!ok) throw new Error('API retornou erro');
        } catch (err) {
            console.error('[Kanban] Rollback:', err);
            setColumns(previous);
            alert('Falha ao mover card. Tente novamente.');
        } finally {
            setDraggedCard(null);
            setDragOverCol(null);
        }
    }

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: 400 }}>
                <CSpinner color="primary" />
            </div>
        );
    }

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilApps" className="me-2" />
                    Kanban - Ordens de Servico
                </h2>
                <small className="text-muted">
                    Total: {Object.values(columns).reduce((sum, c) => sum + c.length, 0)} OS
                </small>
            </div>

            <div className="kanban-board">
                {COLUMNS.map((col) => {
                    const cards = columns[col.id] || [];
                    return (
                        <div
                            key={col.id}
                            className={`kanban-column ${dragOverCol === col.id ? 'drag-over' : ''}`}
                            onDragOver={(e: DragEvent) => {
                                e.preventDefault();
                                setDragOverCol(col.id);
                            }}
                            onDragLeave={() => setDragOverCol(null)}
                            onDrop={() => handleDrop(col.id)}
                        >
                            <div className={`kanban-column-header kanban-col-${col.id.replace(/ /g, '-').toLowerCase()}`}>
                                <span>
                                    <CIcon icon={col.icon} className="me-2" />
                                    {col.label}
                                </span>
                                <CBadge color={col.color} shape="rounded-pill">{cards.length}</CBadge>
                            </div>
                            <div className="kanban-column-body">
                                {cards.length === 0 && (
                                    <div className="text-muted text-center p-3 small">
                                        Arraste cards aqui
                                    </div>
                                )}
                                {cards.map((card) => (
                                    <div
                                        key={card.id}
                                        className={`kanban-card ${draggedCard?.card.id === card.id ? 'dragging' : ''}`}
                                        draggable
                                        onDragStart={() => handleDragStart(card, col.id)}
                                    >
                                        <div className="kanban-card-title">#{card.os_id} - {card.titulo}</div>
                                        <div className="kanban-card-desc">
                                            <CIcon icon="cilUser" className="me-1" />
                                            {card.cliente}
                                        </div>
                                        {card.valor && card.valor > 0 && (
                                            <div className="kanban-card-valor">
                                                R$ {card.valor.toFixed(2)}
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                })}
            </div>
        </>
    );
}
