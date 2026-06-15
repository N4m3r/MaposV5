/**
 * Pagina Clientes com CRUD completo.
 */
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import { StatusBadge } from '../components/ui/DataTable';
import type { FieldDef } from '../components/ui/FormModal';
import type { Cliente, ColumnDef } from '../types';

const columns: ColumnDef<Cliente>[] = [
    { key: 'id',           label: '#',         width: '60px',  sortable: true, className: 'fw-bold' },
    { key: 'nomeCliente',  label: 'Nome',      sortable: true },
    { key: 'documento',    label: 'CPF/CNPJ',  width: '160px' },
    { key: 'email',        label: 'E-mail',    render: (r) => String(r.email || '-') },
    { key: 'telefone',     label: 'Telefone',  width: '140px' },
    { key: 'celular',      label: 'Celular',   width: '140px' },
    { key: 'ativo',        label: 'Status',    width: '110px', render: (r) => <StatusBadge value={r.ativo ? 1 : 0} map={{ 1: { label: 'Ativo', color: 'success' }, 0: { label: 'Inativo', color: 'secondary' } }} /> },
];

const fields: FieldDef[] = [
    { key: 'nomeCliente', label: 'Nome',        type: 'text',     required: true },
    { key: 'documento',   label: 'CPF/CNPJ',    type: 'text' },
    { key: 'email',       label: 'E-mail',      type: 'email' },
    { key: 'telefone',    label: 'Telefone',    type: 'text' },
    { key: 'celular',     label: 'Celular',     type: 'text' },
    { key: 'ativo',       label: 'Ativo',       type: 'checkbox' },
];

export default function ClientesPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilPeople" className="me-2" />
                    Clientes
                </h2>
            </div>
            <CrudTable<Cliente>
                controller="clientes"
                title="Clientes"
                icon="cilPeople"
                columns={columns}
                fields={fields}
                defaultValue={{ nomeCliente: '', documento: '', email: '', telefone: '', celular: '', ativo: 1 }}
                entityName="Cliente"
            />
        </>
    );
}
