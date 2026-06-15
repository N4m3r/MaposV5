/**
 * Testes dos utilitários de acessibilidade.
 */
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { renderHook, act } from '@testing-library/react';
import { useMediaQuery, useAnnounce } from '../src/hooks/useA11y';

describe('useMediaQuery', () => {
    beforeEach(() => {
        // jsdom nao tem matchMedia por padrao — simula
        Object.defineProperty(window, 'matchMedia', {
            writable: true,
            value: (query: string) => ({
                matches: query === '(min-width: 768px)',
                media: query,
                onchange: null,
                addListener: () => {},
                removeListener: () => {},
                addEventListener: vi.fn(),
                removeEventListener: vi.fn(),
                dispatchEvent: () => false,
            }),
        });
    });

    it('retorna true se a query casa no estado inicial', () => {
        const { result } = renderHook(() => useMediaQuery('(min-width: 768px)'));
        expect(result.current).toBe(true);
    });

    it('retorna false se a query nao casa no estado inicial', () => {
        const { result } = renderHook(() => useMediaQuery('(min-width: 2000px)'));
        expect(result.current).toBe(false);
    });
});

describe('useAnnounce', () => {
    it('retorna funcao announce e elemento liveRegion', () => {
        const { result } = renderHook(() => useAnnounce());
        expect(typeof result.current.announce).toBe('function');
        expect(result.current.liveRegion).toBeDefined();
        expect(result.current.liveRegion.props.role).toBe('status');
        expect(result.current.liveRegion.props['aria-live']).toBe('polite');
    });

    it('announce eh uma funcao que nao lanca erro', () => {
        const { result } = renderHook(() => useAnnounce());
        expect(() => act(() => {
            result.current.announce('Item excluido');
        })).not.toThrow();
    });

    it('anunciar duas vezes nao lanca erro', () => {
        const { result } = renderHook(() => useAnnounce());
        expect(() => {
            act(() => result.current.announce('primeira'));
            act(() => result.current.announce('segunda'));
        }).not.toThrow();
    });
});
