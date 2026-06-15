/**
 * Endpoints Kanban.
 * - api_cards: retorna { columns: { [status]: KanbanCard[] } }
 * - api_move: persiste movimentacao de card entre colunas
 */
import { api } from './client';
import type { ApiResponse, KanbanCard, OsStatus } from '../types';

export async function getCards(): Promise<Record<OsStatus, KanbanCard[]>> {
    const { data } = await api.get<ApiResponse<{ columns: Record<OsStatus, KanbanCard[]> }>>(
        'kanban/api_cards',
    );
    if (!data.success || !data.data) {
        throw new Error(data.error || 'Falha ao buscar cards');
    }
    return data.data.columns;
}

export async function moveCard(
    cardId: number,
    fromStatus: OsStatus,
    toStatus: OsStatus,
): Promise<boolean> {
    const { data } = await api.post<ApiResponse<boolean>>('kanban/api_move', {
        card_id: cardId,
        from: fromStatus,
        to: toStatus,
    });
    return data.success === true;
}
