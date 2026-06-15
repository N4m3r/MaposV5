/**
 * Pagina Ordens de Servico com CRUD basico + filtro por status.
 *
 * O cadastro completo de OS (wizard com produtos, servicos, etc) continua
 * no link "Nova OS" -> sistema legado, ate migracao completa do wizard.
 * Aqui: edicao simples de status/descricao/valor + exclusao.
 */
import { useState } from 'react';
import { Link } from 'react-router-dom';
import CIcon from '@coreui/icons-react';
import { DataTable } from '../components/ui/DataTable';
import { StatusBadge } from '../components/ui/DataTable';
import { FormModal, type FieldDef } from '../components/ui/FormModal';
import { useCrudForm } from '../hooks/useCrudForm';
import { remove as crudRemove } from '../api/crud';
import type { Os, ColumnDef } from '../types';

const STATUS_MAP: Record<string, { label: string; color: string }> = {
    Aberto:             { label: 'Aberto',           color: 'secondary' },
    Orcamento:          { label: 'Orcamento',        color: 'info' },
    Aprovado:           { label: 'Aprovado',         color: 'success' },
    'Em Andamento':     { label: 'Em Andamento',     color: 'primary' },
    'Aguardando Pecas': { label: 'Aguard. Pecas',    color: 'warning' },
    Pronto:             { label: 'Pronto',           color: 'info' },
    Finalizado:         { label: 'Finalizado',       color: 'success' },
    Cancelado:          { label: 'Cancelado',        color: 'danger' },
};

function fmtCurrency(v: unknown): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(v || 0));
}

function fmtDate(v: unknown): string {
    if (!v) return '-';
    try { return new Date(String(v)).toLocaleDateString('pt-BR'); } catch { return '-'; }
}

const STATUS_CHIPS = [
    { id: '',                 label: 'Todos' },
    { id: 'Aberto',           label: 'Aberto' },
    { id: 'Em Andamento',     label: 'Andamento' },
    { id: 'Aguardando Pecas', label: 'Aguardando' },
    { id: 'Pronto',           label: 'Pronto' },
    { id: 'Finalizado',       label: 'Finalizado' },
];

const STATUS_OPTIONS = Object.keys(STATUS_MAP).map((k) => ({ value: k, label: STATUS_MAP[k].label }));

const columns: ColumnDef<Os>[] = [
    { key: 'idOs',         label: 'OS',     width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'cliente_nome', label: 'Cliente', sortable: false },
    { key: 'status',       label: 'Status', width: '150px', sortable: true, render: (r) => <StatusBadge value={r.status} map={STATUS_MAP} /> },
    { key: 'descricao',    label: 'Descricao', render: (r) => <span className="text-muted">{String(r.descricao || '-').slice(0, 80)}</span> },
    { key: 'dataInicial',  label: 'Abertura', width: '110px', sortable: true, render: (r) => fmtDate(r.dataInicial) },
    { key: 'dataFinal',    label: 'Conclusao', width: '110px', render: (r) => fmtDate(r.dataFinal) },
    { key: 'valorTotal',   label: 'Valor',   width: '120px', sortable: true, className: 'text-end', render: (r) => fmtCurrency(r.valorTotal) },
];

const fields: FieldDef[] = [
    { key: 'cliente_id',   label: 'Cliente',    type: 'number',   help: 'ID do cliente' },
    { key: 'status',       label: 'Status',     type: 'select',   required: true, options: STATUS_OPTIONS },
    { key: 'descricao',    label: 'Descricao',  type: 'textarea', rows: 3 },
    { key: 'valorTotal',   label: 'Valor (R$)', type: 'number',   step: '0.01', min: 0 },
    { key: 'dataInicial',  label: 'Abertura',   type: 'date' },
    { key: 'dataFinal',    label: 'Conclusao',  type: 'date' },
];

export default function OsPage() {
    const [statusFilter, setStatusFilter] = useState('');
    const { form, setForm, editing, open, loading, error, openEdit, close, submit } = useCrudForm<Os>({
        controller: 'os',
        defaultValue: { status: 'Aberto', descricao: '', valorTotal: 0, cliente_id: 0 },
    });

    async function handleDelete(id: number) {
        if (!window.confirm('Excluir esta OS? (apenas OS sem produtos/servicos)')) return;
        try {
            await crudRemove('os', id);
            window.location.reload();
        } catch {
            window.alert('Nao foi possivel excluir a OS. Verifique se ha produtos/servicos vinculados.');
        }
    }

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
                    <a href="/index.php/os/adicionar" className="btn btn-sm btn-success">
                        <CIcon icon="cilPlus" className="me-1" />Nova OS (wizard)
                    </a>
                </div>
            </div>
            <DataTable<Os>
                controller="os"
                title="Ordens de Servico"
                icon="cilClipboard"
                columns={columns}
                idKey="idOs"
                fixedParams={statusFilter ? { status: statusFilter } : undefined}
                initialPageSize={50}
                renderActions={(r) => (
                    <>
                        <Link
                            to={`/os/${r.idOs}`}
                            className="btn btn-sm btn-link p-0 me-2"
                            title="Visualizar detalhes"
                            aria-label="Visualizar detalhes"
                        >
                            <CIcon icon="cilEye" />
                        </Link>
                        <button
                            className="btn btn-sm btn-link p-0 me-2"
                            title="Edicao rapida"
                            aria-label="Edicao rapida"
                            onClick={() => openEdit(r.idOs as number)}
                        >
                            <CIcon icon="cilPencil" />
                        </button>
                        <button
                            className="btn btn-sm btn-link p-0 text-danger"
                            title="Excluir"
                            aria-label="Excluir"
                            onClick={() => handleDelete(r.idOs as number)}
                        >
                            <CIcon icon="cilTrash" />
                        </button>
                    </>
                )}
            />
            <FormModal
                visible={open}
                title={editing ? `Editar OS #${form.idOs || form.id}` : 'Nova OS'}
                fields={fields}
                value={form}
                onChange={setForm}
                onClose={close}
                onSubmit={submit}
                loading={loading}
                error={error}
                size="lg"
            />
        </>
    );
}
