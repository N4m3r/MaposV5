/**
 * Testes do sistema de i18n.
 */
import { describe, it, expect, beforeEach } from 'vitest';
import { renderHook, act } from '@testing-library/react';
import { useTranslation } from '../src/i18n/useTranslation';

describe('useTranslation', () => {
    beforeEach(() => {
        try { localStorage.clear(); } catch {}
    });

    it('retorna t, locale, setLocale', () => {
        const { result } = renderHook(() => useTranslation());
        expect(typeof result.current.t).toBe('function');
        expect(result.current.locale).toMatch(/^pt-BR$|^en-US$/);
        expect(typeof result.current.setLocale).toBe('function');
    });

    it('t() resolve chave simples', () => {
        const { result } = renderHook(() => useTranslation());
        expect(result.current.t('actions.save')).toBe('Salvar');
    });

    it('t() resolve chave aninhada', () => {
        const { result } = renderHook(() => useTranslation());
        expect(result.current.t('nav.dashboard')).toBe('Painel');
    });

    it('t() interpola variaveis', () => {
        const { result } = renderHook(() => useTranslation());
        const msg = result.current.t('crud.created', { entity: 'Cliente' });
        expect(msg).toBe('Cliente criado com sucesso');
    });

    it('t() cai no fallback pt-BR se chave nao existe no en-US', () => {
        const { result } = renderHook(() => useTranslation());
        act(() => result.current.setLocale('en-US'));
        // chave que existe nos dois
        expect(result.current.t('actions.save')).toBe('Save');
    });

    it('t() retorna a propria chave se nem fallback encontra', () => {
        const { result } = renderHook(() => useTranslation());
        expect(result.current.t('chave.inexistente.subitem')).toBe('chave.inexistente.subitem');
    });

    it('setLocale troca o idioma', () => {
        const { result } = renderHook(() => useTranslation());
        act(() => result.current.setLocale('en-US'));
        expect(result.current.locale).toBe('en-US');
        expect(result.current.t('actions.save')).toBe('Save');
    });

    it('formatCurrency retorna R$ em pt-BR', () => {
        const { result } = renderHook(() => useTranslation());
        const formatted = result.current.formatCurrency(1234.56);
        expect(formatted).toContain('1.234,56');
        expect(formatted).toMatch(/R\$/);
    });

    it('formatCurrency retorna $ em en-US', () => {
        const { result } = renderHook(() => useTranslation());
        act(() => result.current.setLocale('en-US'));
        const formatted = result.current.formatCurrency(1234.56);
        expect(formatted).toContain('1,234.56');
        expect(formatted).toMatch(/[\$]/);
    });

    it('formatDate formata em pt-BR', () => {
        const { result } = renderHook(() => useTranslation());
        const formatted = result.current.formatDate('2026-06-15T12:00:00');
        expect(formatted).toMatch(/15\/06\/2026/);
    });

    it('formatDate com estilo long', () => {
        const { result } = renderHook(() => useTranslation());
        const formatted = result.current.formatDate('2026-06-15T12:00:00', 'long');
        expect(formatted).toContain('2026');
        expect(formatted.toLowerCase()).toContain('junho');
    });

    it('formatDate retorna - para data invalida', () => {
        const { result } = renderHook(() => useTranslation());
        expect(result.current.formatDate('invalido')).toBe('-');
    });

    it('formatNumber formata com decimais', () => {
        const { result } = renderHook(() => useTranslation());
        expect(result.current.formatNumber(1234.5, 2)).toMatch(/1\.234,50|1234\.50/);
    });
});
