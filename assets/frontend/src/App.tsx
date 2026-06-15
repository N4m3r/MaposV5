import { Routes, Route, Navigate } from 'react-router-dom';
import { useMemo } from 'react';
import { AppShell } from './components/layout/AppShell';
import Dashboard from './pages/Dashboard';
import Clientes from './pages/Clientes';
import Kanban from './pages/Kanban';
import NotFound from './pages/NotFound';
import { getConfig } from './config';

function App() {
    const config = useMemo(() => getConfig(), []);

    return (
        <AppShell
            userName={config.userName}
            theme={config.theme}
            permissions={config.permissions}
        >
            <Routes>
                <Route path="/" element={<Navigate to="/dashboard" replace />} />
                <Route path="/dashboard" element={<Dashboard />} />
                <Route path="/clientes" element={<Clientes />} />
                <Route path="/kanban" element={<Kanban />} />
                <Route path="*" element={<NotFound />} />
            </Routes>
        </AppShell>
    );
}

export default App;
