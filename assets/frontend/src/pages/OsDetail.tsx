/**
 * Detalhes de uma OS (drill-down).
 *
 * Acessado por /os/:id
 *
 * Mostra abas:
 *   - Resumo (dados da OS, cliente, valores, datas)
 *   - Servicos
 *   - Produtos
 *   - Anotacoes
 *   - Historico
 *   - Anexos
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
import { FileUpload } from '../components/ui/FileUpload';
import { toast } from '../components/ui/Toast';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    Aberto:             { label: 'Aberto',           color: 'secondary' },
    Orcamento:          { label: 'Orçamento',        color: 'info' },
    Aprovado:           { label: 'Aprovado',         color: 'success' },
    'Em Andamento':     { label: 'Em Andamento',     color: 'primary' },
    'Aguardando Pecas': { label: 'Aguard. Peças',    color: 'warning' },
    Pronto:             { label: 'Pronto',           color: 'info' },
    Finalizado:         { label: 'Finalizado',       color: 'success' },
    Cancelado:          { label: 'Cancelado',        color: 'danger' },
};

interface OsDetail {
    os: any;
    cliente: any;
    servicos: any[];
    produtos: any[];
    anotacoes: any[];
    historico: any[];
    anexos: any[];
}

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleString('pt-BR'); } catch { return '-'; }
}

export default function OsDetailPage() {
    const { id } = useParams<{ id: string }>();
    const [data, setData] = useState<OsDetail | null>(null);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState(1);

    const load = useCallback(async () => {
        if (!id) return;
        setLoading(true);
        try {
            const { data: res } = await api.get<{ success: boolean } & OsDetail>(`os/api_detail/${id}`);
            if (res.success) {
                setData(res);
            } else {
                toast.error('OS não encontrada');
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
                    <p>Carregando OS #{id}...</p>
                </CCardBody>
            </CCard>
        );
    }

    if (!data) {
        return (
            <CCard>
                <CCardBody className="text-center p-5">
                    <h3>OS não encontrada</h3>
                    <Link to="/os" className="btn btn-primary">
                        <CIcon icon="cilArrowLeft" className="me-2" />
                        Voltar para a lista
                    </Link>
                </CCardBody>
            </CCard>
        );
    }

    const { os, cliente, servicos, produtos, anotacoes, historico, anexos } = data;
    const totalProdutos = produtos.reduce((s, p) => s + Number(p.subtotal || p.valor || 0), 0);
    const totalServicos = servicos.reduce((s, p) => s + Number(p.subtotal || p.valor || 0), 0);

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 className="mb-1">
                        <CIcon icon="cilClipboard" className="me-2" />
                        OS #{os.idOs}
                    </h2>
                    <div className="d-flex align-items-center gap-2">
                        <StatusBadge value={os.status} map={STATUS_MAP} />
                        <span className="text-muted">Aberta em {fmtDate(os.dataInicial)}</span>
                    </div>
                </div>
                <div className="d-flex gap-2">
                    <a href={`/index.php/os/visualizar/${os.idOs}`} className="btn btn-sm btn-outline-secondary">
                        <CIcon icon="cilExternalLink" className="me-1" />Sistema legado
                    </a>
                    <Link to="/os" className="btn btn-sm btn-outline-primary">
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
                        <CIcon icon="cilWrench" className="me-1" />Servicos ({servicos.length})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 3} onClick={() => setActiveTab(3)} role="button">
                        <CIcon icon="cilBox" className="me-1" />Produtos ({produtos.length})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 4} onClick={() => setActiveTab(4)} role="button">
                        <CIcon icon="cilNotes" className="me-1" />Anotacoes ({anotacoes.length})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 5} onClick={() => setActiveTab(5)} role="button">
                        <CIcon icon="cilHistory" className="me-1" />Historico
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 6} onClick={() => setActiveTab(6)} role="button">
                        <CIcon icon="cilPaperclip" className="me-1" />Anexos ({anexos.length})
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
                                    <h5 className="mb-3"><CIcon icon="cilUser" className="me-2" />Cliente</h5>
                                    {cliente ? (
                                        <dl className="row mb-0">
                                            <dt className="col-sm-4">Nome</dt>
                                            <dd className="col-sm-8">{cliente.nomeCliente || '-'}</dd>
                                            <dt className="col-sm-4">Email</dt>
                                            <dd className="col-sm-8">{cliente.email || '-'}</dd>
                                            <dt className="col-sm-4">Telefone</dt>
                                            <dd className="col-sm-8">{cliente.telefone || cliente.celular || '-'}</dd>
                                            <dt className="col-sm-4">Documento</dt>
                                            <dd className="col-sm-8">{cliente.documento || '-'}</dd>
                                        </dl>
                                    ) : (
                                        <p className="text-muted">Cliente não vinculado</p>
                                    )}
                                </CCardBody>
                            </CCard>
                        </div>
                        <div className="col-md-6">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilInfoCircle" className="me-2" />OS</h5>
                                    <dl className="row mb-0">
                                        <dt className="col-sm-4">Status</dt>
                                        <dd className="col-sm-8"><StatusBadge value={os.status} map={STATUS_MAP} /></dd>
                                        <dt className="col-sm-4">Abertura</dt>
                                        <dd className="col-sm-8">{fmtDate(os.dataInicial)}</dd>
                                        <dt className="col-sm-4">Conclusao</dt>
                                        <dd className="col-sm-8">{fmtDate(os.dataFinal)}</dd>
                                        <dt className="col-sm-4">Garantia</dt>
                                        <dd className="col-sm-8">{os.garantia || '-'}</dd>
                                        <dt className="col-sm-4">Descricao</dt>
                                        <dd className="col-sm-8">{os.descricao || '-'}</dd>
                                    </dl>
                                </CCardBody>
                            </CCard>
                        </div>
                        <div className="col-12">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilCalculator" className="me-2" />Valores</h5>
                                    <div className="row text-center">
                                        <div className="col-md-3">
                                            <small className="text-muted">Servicos</small>
                                            <div className="h4">{fmtCurrency(totalServicos)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Produtos</small>
                                            <div className="h4">{fmtCurrency(totalProdutos)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Desconto</small>
                                            <div className="h4">{fmtCurrency(os.desconto || 0)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Total</small>
                                            <div className="h3 text-primary">{fmtCurrency(os.valorTotal || 0)}</div>
                                        </div>
                                    </div>
                                </CCardBody>
                            </CCard>
                        </div>
                    </div>
                </CTabPane>

                {/* SERVICOS */}
                <CTabPane visible={activeTab === 2} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {servicos.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhum servico vinculado.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Servico</th>
                                            <th className="text-end">Qtd</th>
                                            <th className="text-end">Preco</th>
                                            <th className="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {servicos.map((s, i) => (
                                            <tr key={i}>
                                                <td>{s.servico_nome || s.nome || `Servico #${s.servico_id}`}</td>
                                                <td className="text-end">{s.quantidade || 1}</td>
                                                <td className="text-end">{fmtCurrency(s.preco || s.valor || 0)}</td>
                                                <td className="text-end fw-bold">{fmtCurrency(s.subtotal || s.valor || 0)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colSpan={3} className="text-end fw-bold">Total</td>
                                            <td className="text-end fw-bold text-primary">{fmtCurrency(totalServicos)}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* PRODUTOS */}
                <CTabPane visible={activeTab === 3} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {produtos.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhum produto vinculado.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th className="text-end">Qtd</th>
                                            <th className="text-end">Preco</th>
                                            <th className="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {produtos.map((p, i) => (
                                            <tr key={i}>
                                                <td>{p.produto_nome || p.nome || `Produto #${p.produto_id}`}</td>
                                                <td className="text-end">{p.quantidade || 1}</td>
                                                <td className="text-end">{fmtCurrency(p.preco || p.valor || 0)}</td>
                                                <td className="text-end fw-bold">{fmtCurrency(p.subtotal || p.valor || 0)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colSpan={3} className="text-end fw-bold">Total</td>
                                            <td className="text-end fw-bold text-primary">{fmtCurrency(totalProdutos)}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* ANOTACOES */}
                <CTabPane visible={activeTab === 4} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {anotacoes.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhuma anotacao.</p>
                            ) : (
                                <ul className="list-unstyled mb-0">
                                    {anotacoes.map((a, i) => (
                                        <li key={i} className="border-bottom py-2">
                                            <div className="d-flex justify-content-between">
                                                <strong>{a.usuario_nome || a.nome || 'Anotacao'}</strong>
                                                <small className="text-muted">{fmtDate(a.data_cadastro || a.data)}</small>
                                            </div>
                                            <p className="mb-0 mt-1">{a.texto || a.anotacao || a.descricao}</p>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* HISTORICO */}
                <CTabPane visible={activeTab === 5} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {historico.length === 0 ? (
                                <p className="text-muted text-center my-4">Sem historico de alteracoes.</p>
                            ) : (
                                <ul className="timeline list-unstyled mb-0">
                                    {historico.map((h, i) => (
                                        <li key={i} className="d-flex gap-3 py-2 border-bottom">
                                            <CIcon icon="cilHistory" className="text-muted" />
                                            <div className="flex-grow-1">
                                                <div className="d-flex justify-content-between">
                                                    <strong>{h.usuario_nome || 'Sistema'}</strong>
                                                    <small className="text-muted">{fmtDate(h.data)}</small>
                                                </div>
                                                <div className="text-muted">
                                                    {h.status_anterior && <span className="badge bg-secondary me-1">{h.status_anterior}</span>}
                                                    {h.status_anterior && '→'}
                                                    {h.status_novo && <span className="badge bg-primary ms-1">{h.status_novo}</span>}
                                                    {h.observacao && <p className="mt-1 mb-0">{h.observacao}</p>}
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* ANEXOS */}
                <CTabPane visible={activeTab === 6} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            <FileUpload
                                value={anexos}
                                onChange={() => load()}
                                folder="os"
                                entity="os"
                                entityId={os.idOs}
                                accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                                maxSize={20}
                                maxFiles={10}
                            />
                        </CCardBody>
                    </CCard>
                </CTabPane>
            </CTabContent>
        </>
    );
}
