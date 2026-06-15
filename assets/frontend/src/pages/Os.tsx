/**
 * Pagina Ordens de Servico.
 * Lista OS com busca, paginacao, ordenacao via DataTable generica.
 * Filtros rapidos por status via chips.
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { DataTable, StatusBadge } from '../components/ui/DataTable';
import type { Os, ColumnDef } from '../types';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    Aberto:           { label: 'Aberto',           color: 'secondary' },
    Orcamento:        { label: 'Orcamento',        color: 'info' },
    Aprovado:         { label: 'Aprovado',         color: 'success' },
    'Em Andamento':   { label: 'Em Andamento',     color: 'primary' },
    'Aguardando Pecas': { label: 'Aguard. Pecas',  color: 'warning' },
    Pronto:           { label: 'Pronto',           color: 'info' },
    Finalizado:       { label: 'Finalizado',       color: 'success' },
    Cancelado:        { label: 'Cancelado',        color: 'danger' },
};

function fmtCurrency(v: unknown): string {
    const n = Number(v || 0);
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n);
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try {
        return new Date(String(v)).toLocaleDateString('pt-BR');
    } catch { return '-'; }
}

const STATUS_CHIPS = [
    { id: '',            label: 'Todos' },
    { id: 'Aberto',      label: 'Aberto' },
    { id: 'Em Andamento',label: 'Andamento' },
    { id: 'Aguardando Pecas', label: 'Aguardando' },
    { id: 'Pronto',      label: 'Pronto' },
    { id: 'Finalizado',  label: 'Finalizado' },
];

const columns: ColumnDef<Os>[] = [
    { key: 'idOs',         label: 'OS',     width: '80px', sortable: true, className: 'fw-bold' },
    { key: 'cliente_nome', label: 'Cliente', sortable: false },
    { key: 'status',       label: 'Status', width: '140px', sortable: true, render: (r) => <StatusBadge value={r.status} map={STATUS_MAP} /> },
    { key: 'descricao',    label: 'Descricao', render: (r) => <span className="text-muted">{String(r.descricao || '-').slice(0, 80)}</span> },
    { key: 'dataInicial',  label: 'Abertura', width: '110px', sortable: true, render: (r) => fmtDate(r.dataInicial) },
    { key: 'dataFinal',    label: 'Conclusao', width: '110px', render: (r) => fmtDate(r.dataFinal) },
    { key: 'valorTotal',   label: 'Valor', width: '120px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valorTotal) },
];

export default function OsPage() {
    const [statusFilter, setStatusFilter] = useState('');
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h2 className="mb-0">
                    <CIcon icon="cilClipboard" className="me-2" />
                    Ordens de Servico
                </h2>
                <div className="d-flex gap-2 align-items-center flex-wrap">
                    {STATUS_CHIPS.map((c) => (
                        <button
                            key={c.id}
                            type="button"
                            className={`btn btn-sm ${statusFilter === c.id ? 'btn-primary' : 'btn-outline-secondary'}`}
                            onClick={() => setStatusFilter(c.id)}
                        >
                            {c.label}
                        </button>
                    ))}
                    <button type="button" className="btn btn-sm btn-success">
                        <CIcon icon="cilPlus" className="me-1" />Nova OS
                    </button>
                </div>
            </div>
            <DataTable<Os>
                controller="os"
                title=""
                columns={columns}
                fixedParams={statusFilter ? { status: statusFilter } : undefined}
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
