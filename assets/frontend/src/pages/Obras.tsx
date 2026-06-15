/**
 * Pagina Obras com CRUD.
 */
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import { StatusBadge } from '../components/ui/DataTable';
import type { FieldDef } from '../components/ui/FormModal';
import type { Row, ColumnDef } from '../types';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    planejamento: { label: 'Planejamento', color: 'info' },
    andamento:    { label: 'Em Andamento', color: 'primary' },
    pausada:      { label: 'Pausada',      color: 'warning' },
    concluida:    { label: 'Concluida',    color: 'success' },
    cancelada:    { label: 'Cancelada',    color: 'danger' },
};

const STATUS_OPTIONS = Object.entries(STATUS_MAP).map(([value, v]) => ({ value, label: v.label }));

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

const columns: ColumnDef<Row>[] = [
    { key: 'idObras',     label: '#',         width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'descricao',   label: 'Descricao', sortable: true },
    { key: 'endereco',    label: 'Endereco',  render: (r) => String(r.endereco || '-') },
    { key: 'status',      label: 'Status',    width: '150px', render: (r) => <StatusBadge value={String(r.status || 'planejamento').toLowerCase()} map={STATUS_MAP} /> },
    { key: 'data_inicio', label: 'Inicio',    width: '120px', sortable: true, render: (r) => fmtDate(r.data_inicio) },
    { key: 'data_fim',    label: 'Previsao',  width: '120px', render: (r) => fmtDate(r.data_fim) },
    { key: 'valor',       label: 'Valor',     width: '130px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valor) },
];

const fields: FieldDef[] = [
    { key: 'descricao',   label: 'Descricao',     type: 'text',     required: true },
    { key: 'endereco',    label: 'Endereco',      type: 'text' },
    { key: 'cliente_id',  label: 'Cliente',       type: 'number',   help: 'ID do cliente' },
    { key: 'status',      label: 'Status',        type: 'select',   required: true, options: STATUS_OPTIONS },
    { key: 'data_inicio', label: 'Data Inicio',   type: 'date' },
    { key: 'data_fim',    label: 'Previsao Termino', type: 'date' },
    { key: 'valor',       label: 'Valor Total (R$)', type: 'number', step: '0.01', min: 0 },
    { key: 'observacao',  label: 'Observacao',    type: 'textarea', rows: 3 },
];

export default function ObrasPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilBuilding" className="me-2" />
                    Obras
                </h2>
            </div>
            <CrudTable<Row>
                controller="obras"
                title="Obras"
                icon="cilBuilding"
                columns={columns}
                fields={fields}
                defaultValue={{ descricao: '', status: 'planejamento', valor: 0, cliente_id: 0 }}
                idKey="idObras"
                entityName="Obra"
            />
        </>
    );
}
