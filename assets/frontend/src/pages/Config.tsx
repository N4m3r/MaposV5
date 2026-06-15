/**
 * Pagina Configuracoes (painel admin).
 * Hub de acesso a configuracoes, tema, sistema, etc.
 */
import CIcon from '@coreui/icons-react';
import { CCard, CCardBody, CCardHeader } from '@coreui/react';
import { useTheme } from '../components/layout/AppShell';

const LINKS = [
    { to: '/index.php/mapos',                        icon: 'cilSpeedometer', label: 'Painel administrativo',     desc: 'Visao geral do sistema.' },
    { to: '/index.php/permissoes',                   icon: 'cilLockLocked',  label: 'Permissoes',                desc: 'Grupos e permissoes de usuarios.' },
    { to: '/index.php/usuarios',                     icon: 'cilUser',        label: 'Usuarios',                  desc: 'Cadastro de usuarios.' },
    { to: '/index.php/notificacoesConfig',           icon: 'cilBell',        label: 'Notificacoes',              desc: 'Configurar alertas do sistema.' },
    { to: '/index.php/impostos',                     icon: 'cilCalculator',  label: 'Impostos',                  desc: 'Tributos e aliquotas.' },
    { to: '/index.php/backup',                       icon: 'cilDataTransferDown', label: 'Backup',               desc: 'Backup e restauracao do banco.' },
    { to: '/index.php/agente_ia',                    icon: 'cilRobot',       label: 'Agente IA',                 desc: 'Assistente IA do sistema.' },
    { to: '/index.php/api_docs',                     icon: 'cilCode',        label: 'Documentacao da API',       desc: 'Swagger / OpenAPI.' },
    { to: '/index.php/diagnostico',                  icon: 'cilHeart',       label: 'Diagnostico',                desc: 'Saude do sistema.' },
];

export default function ConfigPage() {
    const { theme, setTheme } = useTheme();

    const THEMES = ['white', 'puredark', 'whitegreen', 'whiteblack', 'darkviolet', 'darkorange'];

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilSettings" className="me-2" />
                    Configuracoes
                </h2>
            </div>

            <CCard className="mb-4">
                <CCardHeader><strong>Aparencia</strong></CCardHeader>
                <CCardBody>
                    <label className="form-label">Tema ativo: <code>{theme}</code></label>
                    <div className="d-flex gap-2 flex-wrap mt-2">
                        {THEMES.map((t) => (
                            <button
                                key={t}
                                type="button"
                                className={`btn btn-sm ${t === theme ? 'btn-primary' : 'btn-outline-secondary'}`}
                                onClick={() => setTheme(t)}
                            >
                                {t}
                            </button>
                        ))}
                    </div>
                </CCardBody>
            </CCard>

            <div className="row g-3">
                {LINKS.map((l) => (
                    <div className="col-md-6 col-lg-4" key={l.to}>
                        <a href={l.to} className="text-decoration-none">
                            <CCard className="h-100 app-relatorio-card" role="button" tabIndex={0}>
                                <CCardBody>
                                    <div className="d-flex align-items-start gap-3">
                                        <div className="app-relatorio-icon">
                                            <CIcon icon={l.icon} size="xl" />
                                        </div>
                                        <div>
                                            <h5 className="mb-1">{l.label}</h5>
                                            <small className="text-muted">{l.desc}</small>
                                        </div>
                                    </div>
                                </CCardBody>
                            </CCard>
                        </a>
                    </div>
                ))}
            </div>
        </>
    );
}
