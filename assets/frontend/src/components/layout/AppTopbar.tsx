/**
 * Topbar alternativa (caso queira usar ao inves do CHeader do CoreUI).
 * Mantida como referencia para customizacoes futuras.
 */
import { CIcon } from '@coreui/icons-react';
import { ThemeSwitcher } from './ThemeSwitcher';
import { NotificationBell } from './NotificationBell';

interface AppTopbarProps {
    userName: string;
    onToggleSidebar: () => void;
    sidebarVisible: boolean;
}

export function AppTopbar({ userName, onToggleSidebar, sidebarVisible }: AppTopbarProps) {
    return (
        <header className="app-topbar">
            <button
                className="btn btn-link app-icon-btn"
                onClick={onToggleSidebar}
                aria-label="Toggle sidebar"
            >
                <CIcon icon={sidebarVisible ? 'cilMenu' : 'cilMenu'} size="lg" />
            </button>

            <div className="app-topbar-brand">
                <strong>Mapos OS</strong>
            </div>

            <input
                type="search"
                className="form-control app-topbar-search d-none d-lg-block"
                placeholder="Buscar (Ctrl+K)"
                aria-label="Busca global"
            />

            <div className="app-topbar-actions ms-auto d-flex align-items-center gap-2">
                <ThemeSwitcher />
                <NotificationBell />
                <div className="app-topbar-user">
                    <CIcon icon="cilUser" className="me-2" />
                    <span className="d-none d-md-inline">{userName}</span>
                </div>
            </div>
        </header>
    );
}
