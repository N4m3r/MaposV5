/**
 * Testes do singleton de Toast.
 *
 * Como toast é um objeto singleton exportado (não uma classe), testamos
 * a interface publica e os efeitos colaterais (id crescente, dismiss).
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { toast } from '../src/components/ui/Toast';

describe('toast singleton', () => {
    beforeEach(() => {
        vi.useFakeTimers();
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it('toast.show retorna um id numerico crescente', () => {
        const id1 = toast.show('primeiro');
        const id2 = toast.show('segundo');
        const id3 = toast.show('terceiro');
        expect(id1).toBeTypeOf('number');
        expect(id2).toBeGreaterThan(id1);
        expect(id3).toBeGreaterThan(id2);
    });

    it('toast.show aceita titulo customizado', () => {
        // Nao lanca erro
        const id = toast.show('msg', 'info', 'Titulo custom');
        expect(id).toBeTypeOf('number');
    });

    it('toast.dismiss nao lanca erro para id inexistente', () => {
        expect(() => toast.dismiss(999999)).not.toThrow();
    });

    it('toast.success, error, warning, info existem como funcoes', () => {
        expect(toast.success).toBeTypeOf('function');
        expect(toast.error).toBeTypeOf('function');
        expect(toast.warning).toBeTypeOf('function');
        expect(toast.info).toBeTypeOf('function');
    });

    it('toast.success/erro/warning/info podem ser chamados sem erro', () => {
        expect(() => toast.success('ok')).not.toThrow();
        expect(() => toast.error('erro')).not.toThrow();
        expect(() => toast.warning('aviso')).not.toThrow();
        expect(() => toast.info('info')).not.toThrow();
    });

    it('setTimeout e agendado para auto-dismiss', () => {
        const setSpy = vi.spyOn(global, 'setTimeout');
        toast.success('auto');
        expect(setSpy).toHaveBeenCalled();
        // duracao 4000 para sucesso
        const callArgs = setSpy.mock.calls[0];
        expect(callArgs[1]).toBe(4000);
    });

    it('setTimeout usa 6000ms para erros', () => {
        const setSpy = vi.spyOn(global, 'setTimeout');
        toast.error('critico');
        const callArgs = setSpy.mock.calls[0];
        expect(callArgs[1]).toBe(6000);
    });
});
