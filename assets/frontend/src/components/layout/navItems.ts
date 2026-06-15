/**
 * Tipos do menu de navegacao.
 * Mantido em arquivo separado pra facil edicao sem tocar no componente.
 */
export interface NavItem {
    to: string;
    label: string;
    /** Icone CoreUI (prefixo cil ou nome do icon set) */
    icon: string;
    /** Chave em APP_CONFIG.permissions. Se omitido, visivel pra todos. */
    permission?: string;
    /** Se true, visivel apenas pra admin (cPermissao) */
    adminOnly?: boolean;
    /** Badge numerico (futuro: vir de API) */
    badge?: number;
}
