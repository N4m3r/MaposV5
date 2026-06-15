/**
 * Hook useCrudForm
 *
 * Encapsula o estado + submit + delete de um registro CRUD.
 * Usado junto com FormModal + DataTable.
 *
 * Exemplo:
 *   const { form, editing, open, loading, error, openCreate, openEdit, close, submit, remove, setForm } = useCrudForm<R>({
 *     controller: 'clientes',
 *     defaultValue: { nomeCliente: '', email: '' },
 *     onSuccess: () => dataTableRef.current?.reload(),
 *   });
 */
import { useState, useCallback } from 'react';
import { save as crudSave, remove as crudRemove, getOne } from '../api/crud';
import type { Row } from '../types';

interface UseCrudFormOptions<R extends Row> {
    controller: string;
    defaultValue: Omit<R, 'id'>;
    onSuccess?: (action: 'create' | 'update' | 'delete', row: R | null) => void;
    onError?: (err: Error) => void;
}

export function useCrudForm<R extends Row>({
    controller,
    defaultValue,
    onSuccess,
    onError,
}: UseCrudFormOptions<R>) {
    const [form, setFormInternal] = useState<R>({ ...(defaultValue as R) });
    function setForm(v: Record<string, unknown>) {
        setFormInternal((prev) => ({ ...prev, ...v }) as R);
    }
    const [editing, setEditing] = useState(false);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    function close() {
        setOpen(false);
        setError(null);
        setEditing(false);
        setForm({ ...(defaultValue as R) });
    }

    const openCreate = useCallback(() => {
        setEditing(false);
        setForm({ ...(defaultValue as R) });
        setError(null);
        setOpen(true);
    }, [defaultValue]);

    const openEdit = useCallback(async (id: number | string) => {
        setEditing(true);
        setLoading(true);
        setError(null);
        try {
            const row = await getOne<R>(controller, Number(id));
            if (row) {
                setForm(row);
                setOpen(true);
            } else {
                setError('Registro nao encontrado');
            }
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Erro ao carregar');
            onError?.(e instanceof Error ? e : new Error(String(e)));
        } finally {
            setLoading(false);
        }
    }, [controller, onError]);

    async function submit(): Promise<boolean> {
        setLoading(true);
        setError(null);
        try {
            await crudSave<R>(controller, form);
            onSuccess?.(form.id ? 'update' : 'create', form);
            close();
            return true;
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Erro ao salvar');
            onError?.(e instanceof Error ? e : new Error(String(e)));
            return false;
        } finally {
            setLoading(false);
        }
    }

    async function remove(id: number | string): Promise<boolean> {
        if (!window.confirm('Tem certeza que deseja excluir este registro?')) return false;
        setLoading(true);
        try {
            await crudRemove(controller, Number(id));
            onSuccess?.('delete', null);
            return true;
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Erro ao excluir');
            onError?.(e instanceof Error ? e : new Error(String(e)));
            return false;
        } finally {
            setLoading(false);
        }
    }

    return {
        form,
        setForm,
        editing,
        open,
        loading,
        error,
        openCreate,
        openEdit,
        close,
        submit,
        remove,
    };
}
