/**
 * Pagina Vendas.
 * Lista vendas com busca, paginacao, ordenacao.
 */
import CIcon from '@coreui/icons-react';
import { DataTable } from '../components/ui/DataTable';
import type { Row, ColumnDef } from '../types';

function fmtCurrency(v: unknown): string {
    const n = Number(v || 0);
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n);
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const columns: ColumnDef<Row>[] = [
    { key: 'idVendas',     label: '#',        width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'dataVenda',    label: 'Data',     width: '120px', sortable: true, render: (r) => fmtDate(r.dataVenda) },
    { key: 'cliente_nome', label: 'Cliente',  sortable: false, render: (r) => String(r.cliente_nome || r.nomeCliente || '-') },
    { key: 'status',       label: 'Status',   width: '120px', render: (r) => String(r.status || '-') },
    { key: 'tipo',         label: 'Tipo',     width: '120px', render: (r) => String(r.tipo || '-') },
    { key: 'valorTotal',   label: 'Valor',    width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valorTotal) },
];

export default function VendasPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilCart" className="me-2" />
                    Vendas
                </h2>
                <button type="button" className="btn btn-sm btn-success">
                    <CIcon icon="cilPlus" className="me-1" />Nova Venda
                </button>
            </div>
            <DataTable<Row>
                controller="vendas"
                title=""
                columns={columns}
                initialPageSize={50}
                renderActions={(r) => (
                    <>
                        <button className="btn btn-sm btn-link p-0 me-2" title="Visualizar" aria-label="Visualizar">
                            <CIcon icon="cilEye" />
                        </button>
                        <button className="btn btn-sm btn-link p-0" title="Editar" aria-label="Editar">
                            <CIcon icon="cilPencil" />
                        </button>
                    </>
                )}
            />
        </>
    );
}
