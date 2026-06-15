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
import { useState, useCallback, useRef } from 'react';
import { save as crudSave, remove as crudRemove, getOne } from '../api/crud';
import { toast } from '../components/ui/Toast';
import type { Row } from '../types';

interface UseCrudFormOptions<R extends Row> {
    controller: string;
    defaultValue: Omit<R, 'id'>;
    /** Nome amigavel da entidade ("Cliente", "OS", "Lancamento") — usado nos toasts */
    entityName?: string;
    onSuccess?: (action: 'create' | 'update' | 'delete', row: R | null) => void;
    onError?: (err: Error) => void;
    /** Mostra toast automaticamente apos salvar/excluir (default: true) */
    showToasts?: boolean;
}

export function useCrudForm<R extends Row>({
    controller,
    defaultValue,
    entityName,
    onSuccess,
    onError,
    showToasts = true,
}: UseCrudFormOptions<R>) {
    const [form, setFormInternal] = useState<R>({ ...(defaultValue as R) });
    // Ref espelha o form para que submit() leia o valor mais recente
    // (state eh batched, ref nao — isso evita o form "velho" no submit)
    const formRef = useRef<R>(form);
    function setForm(v: Record<string, unknown>) {
        setFormInternal((prev) => {
            const next = { ...prev, ...v } as R;
            formRef.current = next;
            return next;
        });
    }
    const [editing, setEditing] = useState(false);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const label = entityName || controller.replace(/s$/, '');

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
                setFormInternal(row);
                formRef.current = row;
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
            const data = formRef.current;
            const isUpdate = !!(data as { id?: unknown }).id;
            await crudSave<R>(controller, data);
            onSuccess?.(isUpdate ? 'update' : 'create', data);
            if (showToasts) {
                toast.success(`${label} ${isUpdate ? 'atualizado' : 'criado'} com sucesso`);
            }
            close();
            return true;
        } catch (e) {
            const msg = e instanceof Error ? e.message : 'Erro ao salvar';
            setError(msg);
            onError?.(e instanceof Error ? e : new Error(String(e)));
            if (showToasts) toast.error(msg);
            return false;
        } finally {
            setLoading(false);
        }
    }

    async function remove(id: number | string): Promise<boolean> {
        if (!window.confirm(`Tem certeza que deseja excluir este ${label}?`)) return false;
        setLoading(true);
        try {
            await crudRemove(controller, Number(id));
            onSuccess?.('delete', null);
            if (showToasts) toast.success(`${label} excluido com sucesso`);
            return true;
        } catch (e) {
            const msg = e instanceof Error ? e.message : 'Erro ao excluir';
            setError(msg);
            onError?.(e instanceof Error ? e : new Error(String(e)));
            if (showToasts) toast.error(msg);
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
