/**
 * Pagina Garantias com CRUD completo.
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import { StatusBadge } from '../components/ui/DataTable';
import type { FieldDef } from '../components/ui/FormModal';
import type { Row, ColumnDef } from '../types';

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    ativa:     { label: 'Ativa',     color: 'success' },
    expirada:  { label: 'Expirada',  color: 'secondary' },
    usada:     { label: 'Usada',     color: 'info' },
    cancelada: { label: 'Cancelada', color: 'danger' },
};

const columns: ColumnDef<Row>[] = [
    { key: 'idGarantias', label: '#',         width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'descricao',   label: 'Descricao', sortable: true },
    { key: 'data_inicio', label: 'Inicio',    width: '120px', sortable: true, render: (r) => fmtDate(r.data_inicio) },
    { key: 'data_fim',    label: 'Validade',  width: '120px', sortable: true, render: (r) => fmtDate(r.data_fim) },
    { key: 'status',      label: 'Status',    width: '120px', render: (r) => <StatusBadge value={String(r.status || 'ativa').toLowerCase()} map={STATUS_MAP} /> },
];

const fields: FieldDef[] = [
    { key: 'descricao',   label: 'Descricao', type: 'text',     required: true },
    { key: 'data_inicio', label: 'Data Inicio', type: 'date',  required: true },
    { key: 'data_fim',    label: 'Data Fim',    type: 'date',  required: true },
    { key: 'os_id',       label: 'OS Relacionada', type: 'number', help: 'ID da OS (opcional)' },
    { key: 'cliente_id',  label: 'Cliente',    type: 'number', help: 'ID do cliente' },
    { key: 'status',      label: 'Status',    type: 'select',  required: true, options: [
        { value: 'ativa',     label: 'Ativa' },
        { value: 'expirada',  label: 'Expirada' },
        { value: 'usada',     label: 'Usada' },
        { value: 'cancelada', label: 'Cancelada' },
    ] },
    { key: 'observacao',  label: 'Observacao', type: 'textarea', rows: 2 },
];

const FILTROS = [
    { id: '',         label: 'Todas' },
    { id: 'ativa',    label: 'Ativas' },
    { id: 'expirada', label: 'Expiradas' },
];

export default function GarantiasPage() {
    const [status, setStatus] = useState('');
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 className="mb-0">
                    <CIcon icon="cilShieldAlt" className="me-2" />
                    Garantias
                </h2>
                <div className="d-flex gap-2 align-items-center flex-wrap">
                    {FILTROS.map((f) => (
                        <button
                            key={f.id}
                            type="button"
                            className={`btn btn-sm ${status === f.id ? 'btn-primary' : 'btn-outline-secondary'}`}
                            onClick={() => setStatus(f.id)}
                        >
                            {f.label}
                        </button>
                    ))}
                </div>
            </div>
            <CrudTable<Row>
                controller="garantias"
                title="Garantias"
                icon="cilShieldAlt"
                columns={columns}
                fields={fields}
                defaultValue={{ descricao: '', data_inicio: new Date().toISOString().slice(0, 10), data_fim: '', status: 'ativa' }}
                idKey="idGarantias"
                entityName="Garantia"
                fixedParams={status ? { status } : undefined}
            />
        </>
    );
}
