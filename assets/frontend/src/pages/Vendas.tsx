/**
 * Pagina Vendas com CRUD completo (lista + cria + edita + exclui).
 */
import { Link } from 'react-router-dom';
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import type { FieldDef } from '../components/ui/FormModal';
import { StatusBadge } from '../components/ui/DataTable';
import type { Row, ColumnDef } from '../types';

const STATUS_OPTIONS = [
    { value: 'Orcamento',  label: 'Orcamento' },
    { value: 'Aprovado',   label: 'Aprovado' },
    { value: 'Em Andamento', label: 'Em Andamento' },
    { value: 'Finalizado', label: 'Finalizado' },
    { value: 'Cancelado',  label: 'Cancelado' },
];

const TIPO_OPTIONS = [
    { value: 'OS',         label: 'Ordem de Servico' },
    { value: 'Venda',      label: 'Venda Direta' },
    { value: 'Orcamento',  label: 'Orcamento' },
];

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    Orcamento:    { label: 'Orcamento',    color: 'info' },
    Aprovado:     { label: 'Aprovado',     color: 'success' },
    'Em Andamento': { label: 'Em Andamento', color: 'primary' },
    Finalizado:   { label: 'Finalizado',   color: 'success' },
    Cancelado:    { label: 'Cancelado',    color: 'danger' },
};

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const columns: ColumnDef<Row>[] = [
    { key: 'idVendas',     label: '#',         width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'dataVenda',    label: 'Data',      width: '120px', sortable: true, render: (r) => fmtDate(r.dataVenda) },
    { key: 'cliente_nome', label: 'Cliente',   render: (r) => String(r.cliente_nome || r.nomeCliente || '-') },
    { key: 'tipo',         label: 'Tipo',      width: '150px' },
    { key: 'status',       label: 'Status',    width: '130px', render: (r) => <StatusBadge value={String(r.status || '')} map={STATUS_MAP} /> },
    { key: 'valorTotal',   label: 'Valor',     width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valorTotal) },
];

const fields: FieldDef[] = [
    { key: 'dataVenda',  label: 'Data',       type: 'date',         required: true },
    { key: 'cliente_id', label: 'Cliente',    type: 'number',       required: true, help: 'ID do cliente' },
    { key: 'tipo',       label: 'Tipo',       type: 'select',       required: true, options: TIPO_OPTIONS },
    { key: 'status',     label: 'Status',     type: 'select',       required: true, options: STATUS_OPTIONS },
    { key: 'valorTotal', label: 'Valor (R$)', type: 'number',       step: '0.01', min: 0 },
    { key: 'descricao',  label: 'Observacoes', type: 'textarea',    rows: 3 },
];

export default function VendasPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilCart" className="me-2" />
                    Vendas
                </h2>
            </div>
            <CrudTable<Row>
                controller="vendas"
                title="Vendas"
                icon="cilCart"
                columns={columns}
                fields={fields}
                defaultValue={{ dataVenda: new Date().toISOString().slice(0, 10), tipo: 'Venda', status: 'Aprovado', valorTotal: 0, cliente_id: 0 }}
                idKey="idVendas"
                entityName="Venda"
                renderRowActions={(r) => (
                    <Link
                        to={`/vendas/${r.idVendas}`}
                        className="btn btn-sm btn-link p-0 me-2"
                        title="Visualizar detalhes"
                        aria-label="Visualizar detalhes"
                    >
                        <CIcon icon="cilEye" />
                    </Link>
                )}
            />
        </>
    );
}
