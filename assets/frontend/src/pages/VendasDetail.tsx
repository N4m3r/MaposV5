/**
 * Detalhes de uma Venda (drill-down).
 *
 * Acessado por /vendas/:id
 *
 * Mostra abas:
 *   - Resumo (dados da venda, cliente, valores, datas)
 *   - Produtos (itens da venda)
 *   - Cobranças (cobranças vinculadas)
 *   - Financeiro (lançamento)
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
    Orcamento:     { label: 'Orçamento',    color: 'info' },
    Aprovado:      { label: 'Aprovado',     color: 'success' },
    'Em Andamento': { label: 'Em Andamento', color: 'primary' },
    Finalizado:    { label: 'Finalizado',   color: 'success' },
    Faturado:      { label: 'Faturado',     color: 'success' },
    Cancelado:     { label: 'Cancelado',    color: 'danger' },
    Canceled:      { label: 'Cancelado',    color: 'danger' },
    Pago:          { label: 'Pago',         color: 'success' },
    Pendente:      { label: 'Pendente',     color: 'warning' },
    Vencido:       { label: 'Vencido',      color: 'danger' },
};

const TIPO_LABEL: Record<string, string> = {
    OS: 'Ordem de Serviço',
    Venda: 'Venda Direta',
    Orcamento: 'Orçamento',
};

interface VendaDetail {
    venda: any;
    produtos: any[];
    cobrancas: any[];
    lancamento: any;
    anexos: any[];
}

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleString('pt-BR'); } catch { return '-'; }
}

function fmtDateShort(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

export default function VendasDetailPage() {
    const { id } = useParams<{ id: string }>();
    const [data, setData] = useState<VendaDetail | null>(null);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState(1);

    const load = useCallback(async () => {
        if (!id) return;
        setLoading(true);
        try {
            const { data: res } = await api.get<{ success: boolean } & VendaDetail>(`vendas/api_detail/${id}`);
            if (res.success) {
                setData(res);
            } else {
                toast.error('Venda não encontrada');
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
                    <p>Carregando Venda #{id}...</p>
                </CCardBody>
            </CCard>
        );
    }

    if (!data) {
        return (
            <CCard>
                <CCardBody className="text-center p-5">
                    <h3>Venda não encontrada</h3>
                    <Link to="/vendas" className="btn btn-primary">
                        <CIcon icon="cilArrowLeft" className="me-2" />
                        Voltar para a lista
                    </Link>
                </CCardBody>
            </CCard>
        );
    }

    const { venda, produtos, cobrancas, lancamento, anexos } = data;
    const totalProdutos = produtos.reduce(
        (s, p) => s + Number(p.subTotal || p.subtotal || 0),
        0,
    );
    const totalCobrancas = cobrancas.reduce(
        (s, c) => s + Number(c.valor || 0),
        0,
    );

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 className="mb-1">
                        <CIcon icon="cilCart" className="me-2" />
                        Venda #{venda.idVendas}
                    </h2>
                    <div className="d-flex align-items-center gap-2 flex-wrap">
                        <StatusBadge value={venda.status} map={STATUS_MAP} />
                        {venda.tipo && (
                            <span className="badge bg-light text-dark border">
                                {TIPO_LABEL[venda.tipo] || venda.tipo}
                            </span>
                        )}
                        {venda.faturado === '1' || venda.faturado === 1 ? (
                            <span className="badge bg-success">Faturada</span>
                        ) : (
                            <span className="badge bg-secondary">Não faturada</span>
                        )}
                        <span className="text-muted">Aberta em {fmtDate(venda.dataVenda)}</span>
                    </div>
                </div>
                <div className="d-flex gap-2">
                    <a href={`/index.php/vendas/visualizar/${venda.idVendas}`} className="btn btn-sm btn-outline-secondary">
                        <CIcon icon="cilExternalLink" className="me-1" />Sistema legado
                    </a>
                    <Link to="/vendas" className="btn btn-sm btn-outline-primary">
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
                        <CIcon icon="cilBox" className="me-1" />Produtos ({produtos.length})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 3} onClick={() => setActiveTab(3)} role="button">
                        <CIcon icon="cilMoney" className="me-1" />Cobranças ({cobrancas.length})
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 4} onClick={() => setActiveTab(4)} role="button">
                        <CIcon icon="cilCalculator" className="me-1" />Financeiro
                    </CNavLink>
                </CNavItem>
                <CNavItem>
                    <CNavLink active={activeTab === 5} onClick={() => setActiveTab(5)} role="button">
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
                                    <dl className="row mb-0">
                                        <dt className="col-sm-4">Nome</dt>
                                        <dd className="col-sm-8">{venda.nomeCliente || '-'}</dd>
                                        <dt className="col-sm-4">Email</dt>
                                        <dd className="col-sm-8">{venda.email || venda.emailCliente || '-'}</dd>
                                        <dt className="col-sm-4">Telefone</dt>
                                        <dd className="col-sm-8">{venda.telefone || venda.celular || '-'}</dd>
                                        <dt className="col-sm-4">Documento</dt>
                                        <dd className="col-sm-8">{venda.documento || '-'}</dd>
                                    </dl>
                                </CCardBody>
                            </CCard>
                        </div>
                        <div className="col-md-6">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilInfoCircle" className="me-2" />Venda</h5>
                                    <dl className="row mb-0">
                                        <dt className="col-sm-4">Tipo</dt>
                                        <dd className="col-sm-8">{TIPO_LABEL[venda.tipo] || venda.tipo || '-'}</dd>
                                        <dt className="col-sm-4">Status</dt>
                                        <dd className="col-sm-8"><StatusBadge value={venda.status} map={STATUS_MAP} /></dd>
                                        <dt className="col-sm-4">Data</dt>
                                        <dd className="col-sm-8">{fmtDate(venda.dataVenda)}</dd>
                                        <dt className="col-sm-4">Vendedor</dt>
                                        <dd className="col-sm-8">{venda.nome || '-'}</dd>
                                        <dt className="col-sm-4">Garantia</dt>
                                        <dd className="col-sm-8">{venda.garantia || '-'}</dd>
                                    </dl>
                                </CCardBody>
                            </CCard>
                        </div>
                        {(venda.observacoes || venda.observacoes_cliente) && (
                            <div className="col-12">
                                <CCard>
                                    <CCardBody>
                                        <h5 className="mb-3"><CIcon icon="cilNotes" className="me-2" />Observações</h5>
                                        {venda.observacoes && (
                                            <div className="mb-2">
                                                <strong>Internas:</strong>
                                                <p className="mb-0 mt-1">{venda.observacoes}</p>
                                            </div>
                                        )}
                                        {venda.observacoes_cliente && (
                                            <div>
                                                <strong>Do cliente:</strong>
                                                <p className="mb-0 mt-1">{venda.observacoes_cliente}</p>
                                            </div>
                                        )}
                                    </CCardBody>
                                </CCard>
                            </div>
                        )}
                        <div className="col-12">
                            <CCard>
                                <CCardBody>
                                    <h5 className="mb-3"><CIcon icon="cilCalculator" className="me-2" />Valores</h5>
                                    <div className="row text-center">
                                        <div className="col-md-3">
                                            <small className="text-muted">Produtos</small>
                                            <div className="h4">{fmtCurrency(totalProdutos)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Desconto</small>
                                            <div className="h4">{fmtCurrency(venda.desconto || 0)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Cobranças</small>
                                            <div className="h4">{fmtCurrency(totalCobrancas)}</div>
                                        </div>
                                        <div className="col-md-3">
                                            <small className="text-muted">Total</small>
                                            <div className="h3 text-primary">{fmtCurrency(venda.valorTotal || 0)}</div>
                                        </div>
                                    </div>
                                </CCardBody>
                            </CCard>
                        </div>
                    </div>
                </CTabPane>

                {/* PRODUTOS */}
                <CTabPane visible={activeTab === 2} role="tabpanel">
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
                                            <th className="text-end">Preço</th>
                                            <th className="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {produtos.map((p, i) => (
                                            <tr key={i}>
                                                <td>{p.descricao || p.nome || `Produto #${p.produtos_id || p.idItens}`}</td>
                                                <td className="text-end">{p.quantidade || 1}</td>
                                                <td className="text-end">{fmtCurrency(p.preco || 0)}</td>
                                                <td className="text-end fw-bold">{fmtCurrency(p.subTotal || p.subtotal || 0)}</td>
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

                {/* COBRANCAS */}
                <CTabPane visible={activeTab === 3} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {cobrancas.length === 0 ? (
                                <p className="text-muted text-center my-4">Nenhuma cobrança vinculada.</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
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
                                                <td><StatusBadge value={c.status} map={STATUS_MAP} /></td>
                                                <td>{c.payment_gateway || '-'}</td>
                                                <td className="text-end">{fmtCurrency(c.valor)}</td>
                                                <td>{fmtDateShort(c.expire_at || c.data_vencimento)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colSpan={3} className="text-end fw-bold">Total</td>
                                            <td className="text-end fw-bold text-primary">{fmtCurrency(totalCobrancas)}</td>
                                            <td />
                                        </tr>
                                    </tfoot>
                                </table>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* FINANCEIRO */}
                <CTabPane visible={activeTab === 4} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            {!lancamento ? (
                                <p className="text-muted text-center my-4">Nenhum lançamento financeiro vinculado.</p>
                            ) : (
                                <dl className="row mb-0">
                                    <dt className="col-sm-3">ID</dt>
                                    <dd className="col-sm-9">#{lancamento.idLancamentos}</dd>
                                    <dt className="col-sm-3">Descrição</dt>
                                    <dd className="col-sm-9">{lancamento.descricao || '-'}</dd>
                                    <dt className="col-sm-3">Tipo</dt>
                                    <dd className="col-sm-9">
                                        <span className={`badge bg-${lancamento.tipo === 'receita' ? 'success' : 'danger'}`}>
                                            {lancamento.tipo}
                                        </span>
                                    </dd>
                                    <dt className="col-sm-3">Valor</dt>
                                    <dd className="col-sm-9">{fmtCurrency(lancamento.valor)}</dd>
                                    <dt className="col-sm-3">Desconto</dt>
                                    <dd className="col-sm-9">{fmtCurrency(lancamento.desconto || 0)}</dd>
                                    <dt className="col-sm-3">Valor c/ desconto</dt>
                                    <dd className="col-sm-9">{fmtCurrency(lancamento.valor_desconto || lancamento.valor)}</dd>
                                    <dt className="col-sm-3">Vencimento</dt>
                                    <dd className="col-sm-9">{fmtDate(lancamento.data_vencimento)}</dd>
                                    <dt className="col-sm-3">Pagamento</dt>
                                    <dd className="col-sm-9">{fmtDate(lancamento.data_pagamento) || 'Em aberto'}</dd>
                                    <dt className="col-sm-3">Forma de pagamento</dt>
                                    <dd className="col-sm-9">{lancamento.forma_pgto || '-'}</dd>
                                    <dt className="col-sm-3">Baixado</dt>
                                    <dd className="col-sm-9">
                                        {lancamento.baixado === '1' || lancamento.baixado === 1 || lancamento.baixado === true ? (
                                            <span className="badge bg-success">Sim</span>
                                        ) : (
                                            <span className="badge bg-warning">Não</span>
                                        )}
                                    </dd>
                                </dl>
                            )}
                        </CCardBody>
                    </CCard>
                </CTabPane>

                {/* ANEXOS */}
                <CTabPane visible={activeTab === 5} role="tabpanel">
                    <CCard>
                        <CCardBody>
                            <FileUpload
                                value={anexos}
                                onChange={() => load()}
                                folder="vendas"
                                entity="vendas"
                                entityId={venda.idVendas}
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
