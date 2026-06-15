/**
 * Pagina Relatorios.
 * Hubs para os relatorios disponiveis (links para o sistema legado).
 * Filtros rapidos por periodo.
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { CCard, CCardBody, CCardHeader } from '@coreui/react';
import { CFormInput, CFormSelect } from '@coreui/react';

const RELATORIOS = [
    { to: '/index.php/relatorioatendimentos',                icon: 'cilClipboard',  label: 'Atendimentos',          desc: 'Relatorio de OS por tecnico, status, periodo.' },
    { to: '/index.php/dre',                                  icon: 'cilChart',      label: 'DRE',                   desc: 'Demonstrativo de Resultados.' },
    { to: '/index.php/dashboard/relatorio_financeiro',        icon: 'cilWallet',     label: 'Financeiro',            desc: 'Receitas, despesas, lucro.' },
    { to: '/index.php/dashboard/relatorio_clientes',         icon: 'cilPeople',     label: 'Clientes',              desc: 'Top clientes, ticket medio, novos vs recorrentes.' },
    { to: '/index.php/dashboard/relatorio_produtos',          icon: 'cilBox',        label: 'Produtos',              desc: 'Top produtos, estoque critico.' },
    { to: '/index.php/dashboard/relatorio_atendimentos',     icon: 'cilUser',       label: 'Performance tecnicos',  desc: 'OS finalizadas por tecnico.' },
    { to: '/index.php/atividades/relatorio',                 icon: 'cilListRich',   label: 'Atividades',            desc: 'Historico de atividades do sistema.' },
    { to: '/index.php/auditoria/logs',                        icon: 'cilShieldAlt',  label: 'Auditoria',             desc: 'Logs de acesso e alteracoes.' },
];

const PERIODOS = [
    { value: 'hoje',   label: 'Hoje' },
    { value: 'semana', label: 'Esta semana' },
    { value: 'mes',    label: 'Este mes' },
    { value: 'ano',    label: 'Este ano' },
];

export default function RelatoriosPage() {
    const [periodo, setPeriodo] = useState('mes');
    const [dataIni, setDataIni] = useState('');
    const [dataFim, setDataFim] = useState('');

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 className="mb-0">
                    <CIcon icon="cilChartPie" className="me-2" />
                    Relatorios
                </h2>
            </div>

            <CCard className="mb-4">
                <CCardHeader><strong>Filtros</strong></CCardHeader>
                <CCardBody>
                    <div className="row g-3 align-items-end">
                        <div className="col-md-3">
                            <label className="form-label">Periodo</label>
                            <CFormSelect value={periodo} onChange={(e) => setPeriodo(e.target.value)}>
                                {PERIODOS.map((p) => <option key={p.value} value={p.value}>{p.label}</option>)}
                            </CFormSelect>
                        </div>
                        <div className="col-md-3">
                            <label className="form-label">Data inicial</label>
                            <CFormInput type="date" value={dataIni} onChange={(e) => setDataIni(e.target.value)} />
                        </div>
                        <div className="col-md-3">
                            <label className="form-label">Data final</label>
                            <CFormInput type="date" value={dataFim} onChange={(e) => setDataFim(e.target.value)} />
                        </div>
                    </div>
                </CCardBody>
            </CCard>

            <div className="row g-3">
                {RELATORIOS.map((r) => {
                    const qs = new URLSearchParams();
                    if (dataIni) qs.set('data_inicio', dataIni);
                    if (dataFim) qs.set('data_fim', dataFim);
                    if (!dataIni && !dataFim) qs.set('periodo', periodo);
                    const url = `${r.to}?${qs.toString()}`;
                    return (
                        <div className="col-md-6 col-lg-4" key={r.to}>
                            <a href={url} className="text-decoration-none">
                                <CCard className="h-100 app-relatorio-card" role="button" tabIndex={0}>
                                    <CCardBody>
                                        <div className="d-flex align-items-start gap-3">
                                            <div className="app-relatorio-icon">
                                                <CIcon icon={r.icon} size="xl" />
                                            </div>
                                            <div>
                                                <h5 className="mb-1">{r.label}</h5>
                                                <small className="text-muted">{r.desc}</small>
                                            </div>
                                        </div>
                                    </CCardBody>
                                </CCard>
                            </a>
                        </div>
                    );
                })}
            </div>
        </>
    );
}
