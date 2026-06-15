/**
 * Pagina Usuarios com CRUD (apenas admins).
 * Senha nao eh exibida nem editavel pelo frontend (campo hidden).
 */
import CIcon from '@coreui/icons-react';
import { CrudTable } from '../components/ui/CrudTable';
import { StatusBadge } from '../components/ui/DataTable';
import type { FieldDef } from '../components/ui/FormModal';
import type { Row, ColumnDef } from '../types';

const columns: ColumnDef<Row>[] = [
    { key: 'idUsuarios', label: '#',       width: '80px',  sortable: true, className: 'fw-bold' },
    { key: 'nome',       label: 'Nome',    sortable: true },
    { key: 'usuario',    label: 'Login',   width: '160px' },
    { key: 'email',      label: 'E-mail',  render: (r) => String(r.email || '-') },
    { key: 'permissao',  label: 'Permissao', width: '150px' },
    { key: 'situacao',   label: 'Status',  width: '110px', render: (r) => (
        <StatusBadge value={Number(r.situacao || 0)} map={{
            1: { label: 'Ativo',   color: 'success' },
            0: { label: 'Inativo', color: 'secondary' },
        }} />
    )},
];

const fields: FieldDef[] = [
    { key: 'nome',     label: 'Nome',         type: 'text',     required: true },
    { key: 'usuario',  label: 'Login',        type: 'text',     required: true },
    { key: 'email',    label: 'E-mail',       type: 'email' },
    { key: 'permissao', label: 'Permissao',   type: 'text',     help: 'Codigo da permissao (ex: 1, 2, 3...)' },
    { key: 'situacao', label: 'Ativo',        type: 'checkbox' },
    { key: 'senha',    label: 'Senha (deixe vazio pra manter)', type: 'password', help: 'Min. 6 caracteres (so altera se preenchido)' },
];

export default function UsuariosPage() {
    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h2 className="mb-0">
                    <CIcon icon="cilUser" className="me-2" />
                    Usuarios
                </h2>
            </div>
            <CrudTable<Row>
                controller="usuarios"
                title="Usuarios"
                icon="cilUser"
                columns={columns}
                fields={fields}
                defaultValue={{ nome: '', usuario: '', email: '', permissao: '1', situacao: 1 }}
                idKey="idUsuarios"
                entityName="Usuario"
            />
        </>
    );
}
