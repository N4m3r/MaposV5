import { useState, useEffect, createContext, useContext, type ReactNode } from 'react';
import { CSidebar, CSidebarBrand, CSidebarNav, CHeader } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import '@coreui/coreui/dist/css/coreui.min.css';
import { AppSidebarNav } from './AppSidebarNav';
import { NotificationBell } from './NotificationBell';
import { ThemeSwitcher } from './ThemeSwitcher';
import { useLocation } from 'react-router-dom';

interface AppShellProps {
    children: ReactNode;
    userName: string;
    theme: string;
    permissions: string[];
}

export const ThemeContext = createContext<{
    theme: string;
    setTheme: (t: string) => void;
}>({ theme: 'white', setTheme: () => {} });

export function useTheme() {
    return useContext(ThemeContext);
}

export function AppShell({ children, userName, theme: initialTheme, permissions }: AppShellProps) {
    const [sidebarVisible, setSidebarVisible] = useState(true);
    const [theme, setTheme] = useState(initialTheme);
    const location = useLocation();

    // Sincroniza theme com body[data-theme] (consolidado em mapos.css Bloco 1)
    useEffect(() => {
        document.body.setAttribute('data-theme', theme);
    }, [theme]);

    return (
        <ThemeContext.Provider value={{ theme, setTheme }}>
            <div className="app-shell">
                <CSidebar
                    visible={sidebarVisible}
                    onVisibleChange={(v: boolean) => setSidebarVisible(v)}
                    className="app-sidebar"
                >
                    <CSidebarBrand className="app-sidebar-brand">
                        <CIcon icon="cil-speedometer" size="lg" />
                        {sidebarVisible && <span>Mapos OS</span>}
                    </CSidebarBrand>

                    <CSidebarNav>
                        <AppSidebarNav
                            currentPath={location.pathname}
                            permissions={permissions}
                            compact={!sidebarVisible}
                        />
                    </CSidebarNav>
                </CSidebar>

                <div className={`app-main ${!sidebarVisible ? 'sidebar-collapsed' : ''}`}>
                    <CHeader className="app-topbar">
                        <button
                            type="button"
                            onClick={() => setSidebarVisible(!sidebarVisible)}
                            className="app-icon-btn"
                            aria-label="Toggle sidebar"
                        >
                            <CIcon icon="cil-menu" size="lg" />
                        </button>

                        <div className="app-topbar-title">
                            <strong>Mapos OS</strong>
                            <span className="text-muted d-none d-md-inline"> / {location.pathname.replace('/', '')}</span>
                        </div>

                        <div className="app-topbar-actions ms-auto d-flex align-items-center gap-3">
                            <input
                                type="search"
                                className="form-control app-topbar-search d-none d-lg-block"
                                placeholder="Buscar (Ctrl+K)"
                                aria-label="Busca global"
                            />

                            <ThemeSwitcher />
                            <NotificationBell />
                            <div className="app-topbar-user">
                                <CIcon icon="cil-user" className="me-2" />
                                <span className="d-none d-md-inline">{userName}</span>
                            </div>
                        </div>
                    </CHeader>

                    <main className="app-content">
                        {children}
                    </main>
                </div>
            </div>
        </ThemeContext.Provider>
    );
}
