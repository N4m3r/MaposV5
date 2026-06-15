/**
 * Config global da aplicacao.
 * Lê window.APP_CONFIG (injetado pela view PHP) ou usa defaults para dev.
 */
import type { AppConfig } from './types';

const DEFAULT_PERMISSIONS = [
    'vDashboard', 'vCliente', 'vOs', 'vVenda', 'vFinanceiro',
    'vProduto', 'vServico', 'vKanban', 'vObra', 'vNfse',
    'vGarantia', 'vArquivo', 'vUsuario', 'cPermissao',
];

const FALLBACK_CONFIG: AppConfig = {
    baseUrl: '/index.php/',
    userName: 'Desenvolvedor',
    userEmail: 'dev@mapos.local',
    permissions: DEFAULT_PERMISSIONS,
    theme: 'white',
};

export function getConfig(): AppConfig {
    if (typeof window !== 'undefined' && window.APP_CONFIG) {
        return {
            baseUrl: window.APP_CONFIG.baseUrl || FALLBACK_CONFIG.baseUrl,
            userName: window.APP_CONFIG.userName || FALLBACK_CONFIG.userName,
            userEmail: window.APP_CONFIG.userEmail || FALLBACK_CONFIG.userEmail,
            permissions: window.APP_CONFIG.permissions?.length
                ? window.APP_CONFIG.permissions
                : FALLBACK_CONFIG.permissions,
            theme: window.APP_CONFIG.theme || FALLBACK_CONFIG.theme,
        };
    }
    return FALLBACK_CONFIG;
}
