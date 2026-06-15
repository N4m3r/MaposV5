/**
 * Setup global para testes Vitest.
 *
 * - Importa matchers do jest-dom (toBeInTheDocument, etc)
 * - Polyfill matchMedia (algumas libs esperam)
 * - Configura window.MAPOS_CONFIG mockado
 */
import '@testing-library/jest-dom';

// jsdom nao tem matchMedia — algumas libs quebram
Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: (query: string) => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: () => {},
        removeListener: () => {},
        addEventListener: () => {},
        removeEventListener: () => {},
        dispatchEvent: () => false,
    }),
});

// Mock do config do app
declare global {
    interface Window {
        APP_CONFIG?: any;
        MAPOS_CONFIG?: any;
    }
}
window.APP_CONFIG = {
    baseUrl: 'http://localhost/',
    userName: 'Test User',
    userEmail: 'test@example.com',
    permissions: [],
    theme: 'white',
};
window.MAPOS_CONFIG = {
    csrfName: 'csrf_test_name',
    csrfHash: 'abc123',
    baseUrl: 'http://localhost/',
};
