/**
 * Pagina Garantias.
 * Lista garantias (servicos/produtos com prazo) com filtro por situacao.
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { DataTable, StatusBadge } from '../components/ui/DataTable';
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
                    <button type="button" className="btn btn-sm btn-success">
                        <CIcon icon="cilPlus" className="me-1" />Nova Garantia
                    </button>
                </div>
            </div>
            <DataTable<Row>
                controller="garantias"
                title=""
                columns={columns}
                fixedParams={status ? { status } : undefined}
                initialPageSize={50}
            />
        </>
    );
}
