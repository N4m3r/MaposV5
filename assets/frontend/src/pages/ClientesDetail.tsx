/**
 * Detalhes de um Cliente (drill-down).
 *
 * Acessado por /clientes/:id
 *
 * Mostra abas:
 *   - Resumo (dados do cliente, endereco, totais)
 *   - Ordens de Servico (historico)
 *   - Vendas (historico)
 *   - Cobrancas
 *   - Financeiro (lancamentos)
 */
import { useEffect, useState, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import {
    CCard,
    CCardBody,
    CNav,
    CNavItem,
    CNavLink,
    CTabContent,
    CTabPane,
} from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { api } from '../api/client';
import { StatusBadge } from '../components/ui/DataTable';
import { toast } from '../components/ui/Toast';

const OS_STATUS_MAP: Record<string, { label: string; color: string }> = {
    Aberto:             { label: 'Aberto',        color: 'secondary' },
    Orcamento:          { label: 'Orçamento',     color: 'info' },
    Aprovado:           { label: 'Aprovado',      color: 'success' },
    'Em Andamento':     { label: 'Em Andamento',  color: 'primary' },
    'Aguardando Pecas': { label: 'Aguard. Peças', color: 'warning' },
    Pronto:             { label: 'Pronto',        color: 'info' },
    Finalizado:         { label: 'Finalizado',    color: 'success' },
    Cancelado:          { label: 'Cancelado',     color: 'danger' },
};

const VENDA_STATUS_MAP: Record<string, { label: string; color: string }> = {
    Orcamento:     { label: 'Orçamento',    color: 'info' },
    Aprovado:      { label: 'Aprovado',     color: 'success' },
    'Em Andamento': { label: 'Em Andamento', color: 'primary' },
    Finalizado:    { label: 'Finalizado',   color: 'success' },
    Faturado:      { label: 'Faturado',     color: 'success' },
    Cancelado:     { label: 'Cancelado',    color: 'danger' },
    Canceled:      { label: 'Cancelado',    color: 'danger' },
};

const COB_STATUS_MAP: Record<string, { label: string; color: string }> = {
    pending:      { label: 'Pendente',     color: 'warning' },
    paid:         { label: 'Pago',         color: 'success' },
    received:     { label: 'Recebido',     color: 'success' },
    confirmed:    { label: 'Confirmado',   color: 'success' },
    Pago:         { label: 'Pago',         color: 'success' },
    Pendente:     { label: 'Pendente',     color: 'warning' },
    Vencido:      { label: 'Vencido',      color: 'danger' },
    Cancelado:    { label: 'Cancelado',    color: 'danger' },
    canceled:     { label: 'Cancelado',    color: 'danger' },
    expired:      { label: 'Expirado',     color: 'danger' },
};

const TIPO_LABEL: Record<string, string> = {
    OS: 'Ordem de Serviço',
    Venda: 'Venda Direta',
    Orcamento: 'Orçamento',
};

interface ClienteDetail {
    cliente: any;
    ordens: any[];
    vendas: any[];
    cobrancas: any[];
    lancamentos: any[];
    stats: {
        totalOs: number;
        totalVendas: number;
        totalCobrancas: number;
        valorOs: number;
        valorVendas: number;
        valorCobrancasPago: number;
        valorCobrancasPendente: number;
    };
}

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

function fmtDoc(v: unknown): string {
    if (!v) return '-';
    const s = String(v).replace(/\D/g, '');
    if (s.length === 11) {
        return s.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }
    if (s.length === 14) {
        return s.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    }
    return String(v);
}

function fmtPhone(v: unknown): string {
    if (!v) return '-';
    const s = String(v).replace(/\D/g, '');
    if (s.length === 11) return s.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    if (s.length === 10) return s.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    return String(v);
}

export default function ClientesDetailPage() {
    const { id } = useParams<{ id: string }>();
    const [data, setData] = useState<ClienteDetail | null>(null);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState(1);

    const load = useCallback(async () => {
        if (!id) return;
        setLoading(true);
        try {
            const { data: res } = await api.get<{ success: boolean } & ClienteDetail>(`clientes/api_detail/${id}`);
            if (res.success) {
                setData(res);
            } else {
                toast.error('Cliente não encontrado');
            }
        } catch (e) {
            toast.error(e instanceof Error ? e.message : 'Erro ao carregar');
        } finally {
            setLoading(false);
        }
    }, [id]);

    useEffect(() => { load(); }, [load]);

    if (loading) {
        return (
            <CCard>
                <CCardBody className="text-center p-5">
                    <CIcon icon="cil3dRotate" size="3xl" />
                    <p>Carregando Cliente #{id}...</p>
                </CCardBody>
            </CCard>
        );
    }

    if (!data) {
        return (
            <CCard>
                <CCardBody className="text-center p-5">
                    <h3>Cliente não encontrado</h3>
                    <Link to="/clientes" className="btn btn-primary">
                        <CIcon icon="cilArrowLeft" className="me-2" />
                        Voltar para a lista
                    </Link>
                </CCardBody>
            </CCard>
        );
    }

    const { cliente, ordens, vendas, cobrancas, lancamentos, stats } = data;
    const temEndereco = cliente?.rua || cliente?.cidade;

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 className="mb-1">
                        <CIcon icon="cilUser" className="me-2" />
                        {cliente.nomeCliente || `Cliente #${cliente.idClientes}`}
                    </h2>
                    <div className="d-flex align-items-center gap-2 flex-wrap">
                        <span className="text-muted">{fmtDoc(cliente.documento)}</span>
                        {cliente.fornecedor == 1 || cliente.fornecedor === '1' ? (
                            <span className="badge bg-info">Cliente + Fornecedor</span>
                        ) : (
                            <span className="badge bg-light text-dark border">Cliente</span>
                        )}
                        {cliente.ativo == 0 || cliente.ativo === '0' ? (
                            <span className="badge bg-secondary">Inativo</span>
                        ) : (
                            <span className="badge bg-success">Ativo</span>
                        )}
                        <span className="text-muted">
                            Cadastrado em {fmtDate(cliente.dataCadastro)}
                        </span>
                    </div>
                </div>
                <div className="d-flex gap-2">
                    <a href={`/index.php/clientes/visualizar/${cliente.idClientes}`} className="btn btn-sm btn-outline-secondary">
                        <CIcon icon="cilExternalLink" className="me-1" />Sistema legado
                    </a>
                    <Link to="/clientes" className="btn btn-sm btn-outline-primary">
                        <CIcon icon="cilArrowLeft" className="me-1" />Voltar
                    </Link>
                </div>
            </div>

            <CNav variant="tabs" role="tablist" className="mb-3">
                <CNavItem>
                    <CNavLink active={activeTab === 1} onClick={() => setActiveTab(1)} role="button">
                        <CIcon icon="cilInfo" className="me-1" />Resumo
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 2} onClick={() => setActiveTab(2)} role="button">
                        <CIcon icon="cilClipboard" className="me-1" />OS ({stats.totalOs})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 3} onClick={() => setActiveTab(3)} role="button">
                        <CIcon icon="cilCart" className="me-1" />Vendas ({stats.totalVendas})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 4} onClick={() => setActiveTab(4)} role="button">
                        <CIcon icon="cilMoney" className="me-1" />Cobranças ({stats.totalCobrancas})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 5} onClick={() => setActiveTab(5)} role="button">
                        <CIcon icon="cilCalculator" className="me-1" />Financeiro ({lancamentos.length})
                    </CNavLink>
                </CNavItem>
            </CNav>

            <CTabContent>
                {/* RESUMO */}
                <CTabPane visible={activeTab === 1} role="tabpanel">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilContact" className="me-2" />Contato</h5>
                                    <dl className="row mb-0">
                                        <dt className="col-sm-4">Nome</dt>
                                        <dd className="col-sm-8">{cliente.nomeCliente || '-'}</dd>
                                        <dt className="col-sm-4">Contato</dt>
                                        <dd className="col-sm-8">{cliente.contato || '-'}</dd>
                                        <dt className="col-sm-4">E-mail</dt>
                                        <dd className="col-sm-8">{cliente.email || '-'}</dd>
                                        <dt className="col-sm-4">Telefone</dt>
                                        <dd className="col-sm-8">{fmtPhone(cliente.telefone)}</dd>
                                        <dt className="col-sm-4">Celular</dt>
                                        <dd className="col-sm-8">{fmtPhone(cliente.celular)}</dd>
                                        <dt className="col-sm-4">Documento</dt>
                                        <dd className="col-sm-8">{fmtDoc(cliente.documento)}</dd>
                                    </dl>
                                </CCardBody>
                            </CCard>
                        </div>
                        <div className="col-md-6">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilLocationPin" className="me-2" />Endereço</h5>
                                    {temEndereco ? (
                                        <dl className="row mb-0">
                                            <dt className="col-sm-4">CEP</dt>
                                            <dd className="col-sm-8">{cliente.cep || '-'}</dd>
                                            <dt className="col-sm-4">Rua</dt>
                                            <dd className="col-sm-8">
                                                {cliente.rua || '-'}
                                                {cliente.numero ? `, ${cliente.numero}` : ''}
                                            </dd>
                                            <dt className="col-sm-4">Complemento</dt>
                                            <dd className="col-sm-8">{cliente.complemento || '-'}</dd>
                                            <dt className="col-sm-4">Bairro</dt>
                                            <dd className="col-sm-8">{cliente.bairro || '-'}</dd>
                                            <dt className="col-sm-4">Cidade/UF</dt>
                                            <dd className="col-sm-8">
                                                {cliente.cidade || '-'}
                                                {cliente.estado ? `/${cliente.estado}` : ''}
                                            </dd>
                                        </dl>
                                    ) : (
                                        <p className="text-muted">Sem endereço cadastrado.</p>
                                    )}
                                </CCardBody>
                            </CCard>
                        </div>
                        <div className="col-12">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilChart" className="me-2" />Resumo Financeiro</h5>
                                    <div className="row text-center">
                                        <div className="col-md-3">
                                            <small className="text-muted">Em OS</small>
                                            <div className="h4">{fmtCurrency(stats.valorOs)}</div>
                                            <small className="text-muted">{stats.totalOs} OS</small>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Em Vendas</small>
                                            <div className="h4">{fmtCurrency(stats.valorVendas)}</div>
                                            <small className="text-muted">{stats.totalVendas} vendas</small>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Cobranças Pagas</small>
                                            <div className="h4 text-success">{fmtCurrency(stats.valorCobrancasPago)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Cobranças em Aberto</small>
                                            <div className="h4 text-warning">{fmtCurrency(stats.valorCobrancasPendente)}</div>
                                        </div>
                                    </div>
                                </CCardBody>
                            </CCard>
                        </div>
                    </div>
                </CTabPane>

                {/* OS */}
                <CTabPane visible={activeTab === 2} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {ordens.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhuma OS vinculada a este cliente.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>OS</th>
                                            <th>Status</th>
                                            <th>Descrição</th>
                                            <th>Abertura</th>
                                            <th className="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {ordens.map((o, i) => (
                                            <tr key={i}>
                                                <td>
                                                    <Link to={`/os/${o.idOs}`}>#{o.idOs}</Link>
                                                </td>
                                                <td><StatusBadge value={o.status} map={OS_STATUS_MAP} /></td>
                                                <td className="text-muted">{String(o.descricao || '-').slice(0, 80)}</td>
                                                <td>{fmtDate(o.dataInicial)}</td>
                                                <td className="text-end">{fmtCurrency(o.valorTotal)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* VENDAS */}
                <CTabPane visible={activeTab === 3} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {vendas.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhuma venda vinculada a este cliente.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Data</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th className="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {vendas.map((v, i) => (
                                            <tr key={i}>
                                                <td>
                                                    <Link to={`/vendas/${v.idVendas}`}>#{v.idVendas}</Link>
                                                </td>
                                                <td>{fmtDate(v.dataVenda)}</td>
                                                <td>
                                                    <span className="badge bg-light text-dark border">
                                                        {TIPO_LABEL[v.tipo] || v.tipo || '-'}
                                                    </span>
                                                </td>
                                                <td><StatusBadge value={v.status} map={VENDA_STATUS_MAP} /></td>
                                                <td className="text-end">{fmtCurrency(v.valorTotal)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* COBRANCAS */}
                <CTabPane visible={activeTab === 4} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {cobrancas.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhuma cobrança vinculada.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Venda</th>
                                            <th>Status</th>
                                            <th>Gateway</th>
                                            <th className="text-end">Valor</th>
                                            <th>Vencimento</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {cobrancas.map((c, i) => (
                                            <tr key={i}>
                                                <td>#{c.idCobranca}</td>
                                                <td>{c.idVendas ? <Link to={`/vendas/${c.idVendas}`}>#{c.idVendas}</Link> : '-'}</td>
                                                <td><StatusBadge value={c.status} map={COB_STATUS_MAP} /></td>
                                                <td>{c.payment_gateway || '-'}</td>
                                                <td className="text-end">{fmtCurrency(c.valor)}</td>
                                                <td>{fmtDate(c.expire_at || c.data_vencimento)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* FINANCEIRO */}
                <CTabPane visible={activeTab === 5} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {lancamentos.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhum lançamento financeiro.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Descrição</th>
                                            <th>Tipo</th>
                                            <th className="text-end">Valor</th>
                                            <th>Vencimento</th>
                                            <th>Pagamento</th>
                                            <th>Baixado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {lancamentos.map((l, i) => (
                                            <tr key={i}>
                                                <td>#{l.idLancamentos}</td>
                                                <td>{l.descricao || '-'}</td>
                                                <td>
                                                    <span className={`badge bg-${l.tipo === 'receita' ? 'success' : 'danger'}`}>
                                                        {l.tipo}
                                                    </span>
                                                </td>
                                                <td className="text-end">{fmtCurrency(l.valor)}</td>
                                                <td>{fmtDate(l.data_vencimento)}</td>
                                                <td>{fmtDate(l.data_pagamento)}</td>
                                                <td>
                                                    {l.baixado === '1' || l.baixado === 1 || l.baixado === true ? (
                                                        <span className="badge bg-success">Sim</span>
                                                    ) : (
                                                        <span className="badge bg-warning">Não</span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}
