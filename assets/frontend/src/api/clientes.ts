/**
 * Endpoints Clientes.
 */
import { api } from './client';
import type { ApiResponse, Cliente } from '../types';

export async function getClientes(): Promise<Cliente[]> {
    const { data } = await api.get<ApiResponse<Cliente[]>>('clientes/api_listar');
    if (!data.success || !data.data) return [];
    return data.data;
}

export async function getCliente(id: number): Promise<Cliente | null> {
    const { data } = await api.get<ApiResponse<Cliente>>(`clientes/api_visualizar/${id}`);
    return data.data || null;
}
