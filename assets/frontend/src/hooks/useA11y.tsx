/**
 * Utilitários de acessibilidade.
 *
 * - useFocusTrap: prende o foco dentro de um container (modais)
 * - useAnnounce: anuncia mensagens via aria-live region
 * - useEscKey: handler para tecla ESC
 * - prefersReducedMotion: detecta preferencia do usuario
 */
import { useEffect, useRef, useCallback, useState, type ReactElement } from 'react';

/** Detecta se o usuario prefere animacoes reduzidas */
export function prefersReducedMotion(): boolean {
    if (typeof window === 'undefined') return false;
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

/** Foca o primeiro elemento focavel de um container */
function getFocusable(container: HTMLElement): HTMLElement[] {
    const selector = [
        'a[href]',
        'button:not([disabled])',
        'input:not([disabled])',
        'select:not([disabled])',
        'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])',
    ].join(',');
    return Array.from(container.querySelectorAll<HTMLElement>(selector));
}

/**
 * Prende o foco dentro de um container.
 * Restaura o foco ao elemento original ao desmontar.
 */
export function useFocusTrap<T extends HTMLElement>(active = true) {
    const ref = useRef<T>(null);

    useEffect(() => {
        if (!active || !ref.current) return;
        const container = ref.current;
        const previouslyFocused = document.activeElement as HTMLElement;

        const focusable = getFocusable(container);
        if (focusable.length > 0) {
            focusable[0].focus();
        }

        function handleKeyDown(e: KeyboardEvent) {
            if (e.key !== 'Tab' || !ref.current) return;
            const items = getFocusable(ref.current);
            if (items.length === 0) {
                e.preventDefault();
                return;
            }
            const first = items[0];
            const last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        }

        container.addEventListener('keydown', handleKeyDown);
        return () => {
            container.removeEventListener('keydown', handleKeyDown);
            if (previouslyFocused?.focus) previouslyFocused.focus();
        };
    }, [active]);

    return ref;
}

/**
 * Hook para reagir a tecla ESC.
 */
export function useEscKey(handler: () => void, active = true) {
    const handlerRef = useRef(handler);
    handlerRef.current = handler;

    useEffect(() => {
        if (!active) return;
        function onKey(e: KeyboardEvent) {
            if (e.key === 'Escape') handlerRef.current();
        }
        document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [active]);
}

/**
 * Region aria-live para anunciar mensagens dinamicas.
 *
 * Uso:
 *   const { announce, liveRegion } = useAnnounce();
 *   announce('Item excluido com sucesso');
 *   {liveRegion}
 */
export function useAnnounce() {
    const [msg, setMsg] = useState('');
    const timerRef = useRef<number | null>(null);

    const announce = useCallback((message: string) => {
        setMsg(''); // limpa antes (forca o screen reader a re-anunciar)
        requestAnimationFrame(() => {
            setMsg(message);
            if (timerRef.current) window.clearTimeout(timerRef.current);
            timerRef.current = window.setTimeout(() => setMsg(''), 3000);
        });
    }, []);

    useEffect(() => {
        return () => {
            if (timerRef.current) window.clearTimeout(timerRef.current);
        };
    }, []);

    const liveRegion: ReactElement = (
        <div
            role="status"
            aria-live="polite"
            aria-atomic="true"
            style={{
                position: 'absolute',
                width: 1,
                height: 1,
                padding: 0,
                margin: -1,
                overflow: 'hidden',
                clip: 'rect(0, 0, 0, 0)',
                whiteSpace: 'nowrap',
                border: 0,
            }}
        >
            {msg}
        </div>
    );

    return { announce, liveRegion };
}

/**
 * Hook que retorna se a tela eh pequena (mobile).
 */
export function useMediaQuery(query: string): boolean {
    const [matches, setMatches] = useState(() => {
        if (typeof window === 'undefined') return false;
        return window.matchMedia(query).matches;
    });
    useEffect(() => {
        if (typeof window === 'undefined') return;
        const mql = window.matchMedia(query);
        const handler = (e: MediaQueryListEvent) => setMatches(e.matches);
        mql.addEventListener('change', handler);
        return () => mql.removeEventListener('change', handler);
    }, [query]);
    return matches;
}
