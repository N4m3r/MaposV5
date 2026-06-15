import { Routes, Route, Navigate } from 'react-router-dom';
import { useMemo } from 'react';
import { AppShell } from './components/layout/AppShell';
import Dashboard from './pages/Dashboard';
import Clientes from './pages/Clientes';
import Kanban from './pages/Kanban';
import OsPage from './pages/Os';
import VendasPage from './pages/Vendas';
import FinanceiroPage from './pages/Financeiro';
import ProdutosPage from './pages/Produtos';
import CobrancasPage from './pages/Cobrancas';
import GarantiasPage from './pages/Garantias';
import NfsePage from './pages/Nfse';
import ObrasPage from './pages/Obras';
import ArquivosPage from './pages/Arquivos';
import RelatoriosPage from './pages/Relatorios';
import UsuariosPage from './pages/Usuarios';
import ConfigPage from './pages/Config';
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
                <Route path="/dashboard"   element={<Dashboard />} />
                <Route path="/os"          element={<OsPage />} />
                <Route path="/kanban"      element={<Kanban />} />
                <Route path="/clientes"    element={<Clientes />} />
                <Route path="/produtos"    element={<ProdutosPage />} />
                <Route path="/vendas"      element={<VendasPage />} />
                <Route path="/financeiro"  element={<FinanceiroPage />} />
                <Route path="/cobrancas"   element={<CobrancasPage />} />
                <Route path="/garantias"   element={<GarantiasPage />} />
                <Route path="/nfse"        element={<NfsePage />} />
                <Route path="/obras"       element={<ObrasPage />} />
                <Route path="/arquivos"    element={<ArquivosPage />} />
                <Route path="/relatorios"  element={<RelatoriosPage />} />
                <Route path="/usuarios"    element={<UsuariosPage />} />
                <Route path="/config"      element={<ConfigPage />} />
                <Route path="*" element={<NotFound />} />
            </Routes>
        </AppShell>
    );
}

export default App;
