import { CNavItem, CNavLink, CNavGroup } from '@coreui/react';
import { Link, useLocation } from 'react-router-dom';
import { CIcon } from '@coreui/icons-react';
import type { NavItem } from './navItems';

interface AppSidebarNavProps {
    currentPath: string;
    permissions: string[];
    compact: boolean;
}

/**
 * Sidebar dinamica que filtra itens por permissao do usuario.
 * - Itens sao definidos em navItems.ts (separado pra facil manutencao)
 * - Permissao vem de window.APP_CONFIG.permissions (injetado pela view PHP)
 */
export function AppSidebarNav({ currentPath, permissions, compact }: AppSidebarNavProps) {
    const visibleItems = NAV_ITEMS.filter((item) => {
        if (item.adminOnly && !permissions.includes('cPermissao')) return false;
        if (!item.permission) return true;
        return permissions.includes(item.permission);
    });

    return (
        <>
            {visibleItems.map((item) => (
                <CNavItem key={item.to}>
                    <CNavLink
                        to={item.to}
                        as={Link}
                        active={currentPath === item.to}
                        className="app-sidebar-link"
                    >
                        <CIcon icon={item.icon} className="me-2" />
                        {!compact && <span>{item.label}</span>}
                        {!compact && item.badge && (
                            <span className="badge bg-primary ms-auto">{item.badge}</span>
                        )}
                    </CNavLink>
                </CNavItem>
            ))}
        </>
    );
}

/**
 * Lista de itens do menu.
 * - permission: chave que deve estar em APP_CONFIG.permissions
 * - icon: icone CoreUI (https://coreui.io/icons/)
 * - badge: contador opcional (futuro, vindo de API)
 */
const NAV_ITEMS: NavItem[] = [
    { to: '/dashboard', label: 'Dashboard',    icon: 'cilSpeedometer', permission: 'vDashboard' },
    { to: '/os',        label: 'Ordens',       icon: 'cilClipboard',    permission: 'vOs' },
    { to: '/kanban',    label: 'Kanban',       icon: 'cilApps',         permission: 'vKanban' },
    { to: '/clientes',  label: 'Clientes',     icon: 'cilPeople',       permission: 'vCliente' },
    { to: '/produtos',  label: 'Produtos',     icon: 'cilBox',          permission: 'vProduto' },
    { to: '/vendas',    label: 'Vendas',       icon: 'cilCart',         permission: 'vVenda' },
    { to: '/financeiro',label: 'Financeiro',   icon: 'cilWallet',       permission: 'vFinanceiro' },
    { to: '/cobrancas', label: 'Cobrancas',    icon: 'cilCreditCard',   permission: 'vCobranca' },
    { to: '/garantias', label: 'Garantias',    icon: 'cilShieldAlt',    permission: 'vGarantia' },
    { to: '/nfse',      label: 'NFS-e',        icon: 'cilReceipt',      permission: 'vNfse' },
    { to: '/obras',     label: 'Obras',        icon: 'cilBuilding',     permission: 'vObra' },
    { to: '/arquivos',  label: 'Arquivos',     icon: 'cilFolderOpen',   permission: 'vArquivo' },
    { to: '/relatorios',label: 'Relatorios',   icon: 'cilChartPie',     permission: 'vRelatorio' },
    { to: '/usuarios',  label: 'Usuarios',     icon: 'cilUser',         permission: 'vUsuario', adminOnly: true },
    { to: '/config',    label: 'Configuracoes',icon: 'cilSettings',     permission: 'cPermissao' },
];
