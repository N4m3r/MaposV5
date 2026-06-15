/**
 * Testes do hook useCrudForm.
 *
 * Cobre:
 *  - openCreate / openEdit
 *  - submit (create + update)
 *  - remove (com confirm)
 *  - close (limpa estado)
 *  - toasts (sucesso e erro)
 */
import { renderHook, act } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { useCrudForm } from '../src/hooks/useCrudForm';

// Mock do modulo crud
vi.mock('../src/api/crud', () => ({
    save: vi.fn(),
    remove: vi.fn(),
    getOne: vi.fn(),
}));

// Mock do Toast (singleton)
vi.mock('../src/components/ui/Toast', () => ({
    toast: {
        success: vi.fn(),
        error: vi.fn(),
        warning: vi.fn(),
        info: vi.fn(),
        show: vi.fn(),
        dismiss: vi.fn(),
    },
}));

import { save as crudSave, remove as crudRemove, getOne as crudGetOne } from '../src/api/crud';
import { toast } from '../src/components/ui/Toast';

describe('useCrudForm', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('openCreate abre o modal com valores default', () => {
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
            }),
        );

        expect(result.current.open).toBe(false);
        act(() => result.current.openCreate());
        expect(result.current.open).toBe(true);
        expect(result.current.editing).toBe(false);
        expect(result.current.form.nome).toBe('');
    });

    it('openEdit carrega o registro e abre o modal', async () => {
        (crudGetOne as any).mockResolvedValue({ id: 1, nome: 'Foo' });
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
            }),
        );

        await act(async () => {
            await result.current.openEdit(1);
        });

        expect(crudGetOne).toHaveBeenCalledWith('clientes', 1);
        expect(result.current.open).toBe(true);
        expect(result.current.editing).toBe(true);
        expect(result.current.form).toEqual({ id: 1, nome: 'Foo' });
    });

    it('submit() com id chama update e mostra toast de sucesso', async () => {
        (crudSave as any).mockResolvedValue({ id: 1, nome: 'Foo' });
        const onSuccess = vi.fn();
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
                entityName: 'Cliente',
                onSuccess,
            }),
        );

        // openCreate primeiro (zera form), depois setForm (id+nome), depois submit
        act(() => result.current.openCreate());
        act(() => result.current.setForm({ id: 1, nome: 'Foo' }));

        let ok: boolean = false;
        await act(async () => {
            ok = await result.current.submit();
        });

        expect(ok).toBe(true);
        expect(crudSave).toHaveBeenCalledWith('clientes', { id: 1, nome: 'Foo' });
        expect(toast.success).toHaveBeenCalledWith('Cliente atualizado com sucesso');
        expect(onSuccess).toHaveBeenCalledWith('update', { id: 1, nome: 'Foo' });
        expect(result.current.open).toBe(false);
    });

    it('submit() sem id chama create e mostra toast de sucesso', async () => {
        (crudSave as any).mockResolvedValue({ id: 2, nome: 'Bar' });
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
                entityName: 'Cliente',
            }),
        );

        act(() => {
            result.current.setForm({ nome: 'Bar' });
            result.current.openCreate();
        });

        await act(async () => {
            await result.current.submit();
        });

        expect(toast.success).toHaveBeenCalledWith('Cliente criado com sucesso');
    });

    it('submit() em erro mostra toast de erro e mantem modal aberto', async () => {
        (crudSave as any).mockRejectedValue(new Error('Falha de validacao'));
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
            }),
        );

        act(() => result.current.openCreate());

        let ok: boolean = true;
        await act(async () => {
            ok = await result.current.submit();
        });

        expect(ok).toBe(false);
        expect(toast.error).toHaveBeenCalledWith('Falha de validacao');
        expect(result.current.open).toBe(true);
        expect(result.current.error).toBe('Falha de validacao');
    });

    it('remove() pede confirmacao e chama delete em sucesso', async () => {
        vi.spyOn(window, 'confirm').mockReturnValue(true);
        (crudRemove as any).mockResolvedValue(true);
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
                entityName: 'Cliente',
            }),
        );

        let ok: boolean = false;
        await act(async () => {
            ok = await result.current.remove(5);
        });

        expect(ok).toBe(true);
        expect(crudRemove).toHaveBeenCalledWith('clientes', 5);
        expect(toast.success).toHaveBeenCalledWith('Cliente excluido com sucesso');
    });

    it('remove() cancela se usuario rejeitar o confirm', async () => {
        vi.spyOn(window, 'confirm').mockReturnValue(false);
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
            }),
        );

        let ok: boolean = true;
        await act(async () => {
            ok = await result.current.remove(5);
        });

        expect(ok).toBe(false);
        expect(crudRemove).not.toHaveBeenCalled();
    });

    it('close() limpa o estado', () => {
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
            }),
        );

        act(() => result.current.openCreate());
        act(() => result.current.setForm({ nome: 'Dirty' }));
        act(() => result.current.close());

        expect(result.current.open).toBe(false);
        expect(result.current.form.nome).toBe('');
    });

    it('showToasts=false suprime os toasts', async () => {
        (crudSave as any).mockResolvedValue({});
        const { result } = renderHook(() =>
            useCrudForm<{ id?: number; nome: string }>({
                controller: 'clientes',
                defaultValue: { nome: '' },
                showToasts: false,
            }),
        );

        act(() => result.current.openCreate());
        await act(async () => {
            await result.current.submit();
        });

        expect(toast.success).not.toHaveBeenCalled();
    });
});
