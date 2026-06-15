/**
 * Endpoints especificos do Dashboard.
 * Cada funcao retorna Promise<ApiResponse<T>> ja tratado.
 */
import { api } from './client';
import type { ApiResponse, DashboardStats } from '../types';

export async function getStats(): Promise<DashboardStats> {
    const { data } = await api.get<ApiResponse<DashboardStats>>('dashboard/api_stats');
    if (!data.success || !data.data) {
        throw new Error(data.error || 'Falha ao buscar stats');
    }
    return data.data;
}

export async function getRecentActivity(): Promise<Array<{
    tipo: string;
    mensagem: string;
    data: string;
    icon: string;
    cor: string;
}>> {
    const { data } = await api.get<ApiResponse<Array<{
        tipo: string; mensagem: string; data: string; icon: string; cor: string;
    }>>>('dashboard/api_activity');
    if (!data.success || !data.data) return [];
    return data.data;
}
