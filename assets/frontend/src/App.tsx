import { lazy, Suspense } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useMemo } from 'react';
import { AppShell } from './components/layout/AppShell';
import { ToastContainer } from './components/ui/Toast';
import { getConfig } from './config';
import { CSpinner } from '@coreui/react';

// Lazy-loaded: cada pagina fica em seu proprio chunk.
// Bundle inicial so inclui Dashboard + NotFound (o minimo absoluto).
const Dashboard     = lazy(() => import('./pages/Dashboard'));
const Clientes      = lazy(() => import('./pages/Clientes'));
const Kanban        = lazy(() => import('./pages/Kanban'));
const OsPage        = lazy(() => import('./pages/Os'));
const OsDetail      = lazy(() => import('./pages/OsDetail'));
const VendasPage    = lazy(() => import('./pages/Vendas'));
const VendasDetail  = lazy(() => import('./pages/VendasDetail'));
const FinanceiroPage = lazy(() => import('./pages/Financeiro'));
const ProdutosPage  = lazy(() => import('./pages/Produtos'));
const CobrancasPage = lazy(() => import('./pages/Cobrancas'));
const GarantiasPage = lazy(() => import('./pages/Garantias'));
const NfsePage      = lazy(() => import('./pages/Nfse'));
const ObrasPage     = lazy(() => import('./pages/Obras'));
const ArquivosPage  = lazy(() => import('./pages/Arquivos'));
const RelatoriosPage = lazy(() => import('./pages/Relatorios'));
const UsuariosPage  = lazy(() => import('./pages/Usuarios'));
const ConfigPage    = lazy(() => import('./pages/Config'));
const NotFound      = lazy(() => import('./pages/NotFound'));

/** Loading fallback durante o download do chunk */
function PageLoader() {
    return (
        <div
            style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                minHeight: '60vh',
                flexDirection: 'column',
                gap: '1rem',
            }}
            role="status"
            aria-label="Carregando"
        >
            <CSpinner color="primary" variant="grow" />
            <span className="text-muted">Carregando...</span>
        </div>
    );
}

function App() {
    const config = useMemo(() => getConfig(), []);

    return (
        <>
            <AppShell
                userName={config.userName}
                theme={config.theme}
                permissions={config.permissions}
            >
                <Suspense fallback={<PageLoader />}>
                    <Routes>
                        <Route path="/" element={<Navigate to="/dashboard" replace />} />
                        <Route path="/dashboard"   element={<Dashboard />} />
                        <Route path="/os"          element={<OsPage />} />
                        <Route path="/os/:id"      element={<OsDetail />} />
                        <Route path="/kanban"      element={<Kanban />} />
                        <Route path="/clientes"    element={<Clientes />} />
                        <Route path="/produtos"    element={<ProdutosPage />} />
                        <Route path="/vendas"      element={<VendasPage />} />
                        <Route path="/vendas/:id"  element={<VendasDetail />} />
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
                </Suspense>
            </AppShell>
            <ToastContainer />
        </>
    );
}

export default App;
