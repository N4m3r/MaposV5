/**
 * Sistema de internacionalização (i18n).
 *
 * Hook useTranslation() com fallback pt-BR, interpolacao {variavel},
 * e deteccao automatica do locale do navegador.
 *
 * Uso:
 *   const { t, locale, setLocale, formatDate, formatCurrency } = useTranslation();
 *   <h1>{t('login.title')}</h1>
 *   <p>{t('crud.created', { entity: 'Cliente' })}</p>
 */
import { useState, useCallback, useMemo } from 'react';
import ptBR from './pt-BR.json';
import enUS from './en-US.json';

export type Locale = 'pt-BR' | 'en-US';

const dictionaries: Record<Locale, any> = {
    'pt-BR': ptBR,
    'en-US': enUS,
};

const STORAGE_KEY = 'mapos.locale';

function detectLocale(): Locale {
    if (typeof window === 'undefined') return 'pt-BR';
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved && saved in dictionaries) return saved as Locale;
    } catch {}
    const nav = window.navigator?.language || 'pt-BR';
    if (nav.startsWith('en')) return 'en-US';
    return 'pt-BR';
}

/** Resolve uma chave aninhada tipo 'crud.created' em um dicionario */
function resolve(dict: any, key: string): string | string[] | undefined {
    const parts = key.split('.');
    let cur: any = dict;
    for (const p of parts) {
        if (cur == null) return undefined;
        cur = cur[p];
    }
    return cur;
}

/** Interpola {var} no template */
function interpolate(template: string, vars?: Record<string, string | number>): string {
    if (!vars) return template;
    return template.replace(/\{(\w+)\}/g, (_, k) => {
        return vars[k] !== undefined ? String(vars[k]) : `{${k}}`;
    });
}

export function useTranslation() {
    const [locale, setLocaleState] = useState<Locale>(detectLocale);

    const setLocale = useCallback((newLocale: Locale) => {
        setLocaleState(newLocale);
        try {
            localStorage.setItem(STORAGE_KEY, newLocale);
        } catch {}
    }, []);

    const t = useCallback(
        (key: string, vars?: Record<string, string | number>): string => {
            let value = resolve(dictionaries[locale], key);
            // Fallback para pt-BR
            if (value === undefined && locale !== 'pt-BR') {
                value = resolve(dictionaries['pt-BR'], key);
            }
            if (typeof value !== 'string') {
                if (Array.isArray(value)) return value.join(', ');
                return key; // chave nao encontrada
            }
            return interpolate(value, vars);
        },
        [locale],
    );

    const formatDate = useCallback(
        (date: Date | string | number, style: 'short' | 'long' = 'short'): string => {
            const d = new Date(date);
            if (isNaN(d.getTime())) return '-';
            if (style === 'long') {
                return d.toLocaleDateString(locale, {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                });
            }
            return d.toLocaleDateString(locale);
        },
        [locale],
    );

    const formatCurrency = useCallback(
        (value: number): string => {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: locale === 'pt-BR' ? 'BRL' : 'USD',
            }).format(value);
        },
        [locale],
    );

    const formatNumber = useCallback(
        (value: number, decimals = 0): string => {
            return new Intl.NumberFormat(locale, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            }).format(value);
        },
        [locale],
    );

    return useMemo(
        () => ({ t, locale, setLocale, formatDate, formatCurrency, formatNumber }),
        [t, locale, setLocale, formatDate, formatCurrency, formatNumber],
    );
}
