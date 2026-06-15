/**
 * CRUD genérico baseado no ApiCrudTrait do backend.
 *
 * - list<T>(controller, params): GET <controller>/api_list?...
 * - get<T>(controller, id):     GET <controller>/api_get/<id>
 * - save<T>(controller, data):  POST <controller>/api_save
 * - remove(controller, id):     POST <controller>/api_delete/<id>
 */
import { api } from './client';
import type { ApiResponse, ListResult, Row } from '../types';

export interface ListParams {
    search?: string;
    page?: number;
    limit?: number;
    orderBy?: string;
    orderDir?: 'asc' | 'desc';
    [key: string]: string | number | boolean | undefined;
}

export async function list<R extends Row = Row>(
    controller: string,
    params: ListParams = {},
): Promise<ListResult<R>> {
    const { data } = await api.get<ApiResponse<R[]> & { total: number; page: number; limit: number }>(
        `${controller}/api_list`,
        { params },
    );
    if (!data.success) {
        return { data: [], total: 0, page: 1, limit: 25 };
    }
    return {
        data: (data.data || []) as R[],
        total: data.total ?? 0,
        page: data.page ?? 1,
        limit: data.limit ?? 25,
    };
}

export async function getOne<R extends Row = Row>(controller: string, id: number): Promise<R | null> {
    const { data } = await api.get<ApiResponse<R>>(`${controller}/api_get/${id}`);
    return data.data || null;
}

export async function save<R extends Row = Row>(controller: string, payload: Partial<R>): Promise<R | null> {
    const { data } = await api.post<ApiResponse<R>>(`${controller}/api_save`, payload);
    if (!data.success) throw new Error(data.error || 'Falha ao salvar');
    return payload.id ? ({ id: payload.id, ...payload } as R) : null;
}

export async function remove(controller: string, id: number): Promise<boolean> {
    const { data } = await api.post<ApiResponse>(`${controller}/api_delete/${id}`);
    return !!data.success;
}
