/**
 * CrudTable generica: DataTable + FormModal + useCrudForm em um so componente.
 *
 * Encapsula: listagem, busca, paginacao, criar, editar, excluir.
 *
 * Uso:
 *   <CrudTable
 *     controller="clientes"
 *     title="Clientes"
 *     icon="cilPeople"
 *     columns={[...]}
 *     fields={[
 *       { key: 'nomeCliente', label: 'Nome', type: 'text', required: true },
 *       { key: 'email',       label: 'E-mail', type: 'email' },
 *     ]}
 *     defaultValue={{ nomeCliente: '', email: '' }}
 *   />
 */
import { useState } from 'react';
import CIcon from '@coreui/icons-react';
import { DataTable } from './DataTable';
import { FormModal, type FieldDef } from './FormModal';
import { useCrudForm } from '../../hooks/useCrudForm';
import type { ColumnDef, Row } from '../../types';

interface CrudTableProps<R extends Row> {
    controller: string;
    title: string;
    icon?: string;
    columns: ColumnDef<R>[];
    fields: FieldDef[];
    defaultValue: Omit<R, 'id'>;
    /** Nome da coluna id (default 'id') */
    idKey?: string;
    /** Validacao antes de salvar */
    validate?: (v: R) => string | null;
    /** Mensagem custom no botao criar */
    newLabel?: string;
    /** Params fixos */
    fixedParams?: Record<string, string | number>;
    /** Callback depois de salvar/excluir (ex: toast) */
    onSuccess?: (action: 'create' | 'update' | 'delete') => void;
    /** Extrai id custom */
    getRowId?: (row: R) => number | string;
    /** Page size inicial */
    initialPageSize?: number;
    /** Tag do controller pro FormModal (ex: 'Cliente') */
    entityName?: string;
}

export function CrudTable<R extends Row>({
    controller,
    title,
    icon,
    columns,
    fields,
    defaultValue,
    idKey,
    validate,
    newLabel = 'Novo',
    fixedParams,
    onSuccess,
    getRowId,
    initialPageSize,
    entityName,
}: CrudTableProps<R>) {
    // O reload eh disparado via key remount do DataTable
    const [reloadKey, setReloadKey] = useState('');
    const { form, setForm, editing, open, loading, error, openCreate, openEdit, close, submit, remove } = useCrudForm<R>({
        controller,
        defaultValue,
        onSuccess: (action) => {
            setReloadKey(String(Date.now()));
            onSuccess?.(action);
        },
    });

    const formValidate = validate ? (v: Record<string, unknown>) => validate(v as R) : undefined;

    const label = entityName || title.replace(/s$/, '');

    return (
        <>
            <DataTable<R>
                key={reloadKey}
                controller={controller}
                title={title}
                icon={icon}
                columns={columns}
                idKey={idKey}
                fixedParams={fixedParams}
                getRowId={getRowId}
                initialPageSize={initialPageSize}
                headerButton={
                    <button type="button" className="btn btn-sm btn-success" onClick={openCreate}>
                        <CIcon icon="cilPlus" className="me-1" />{newLabel}
                    </button>
                }
                renderActions={(r) => {
                    const id = (getRowId ? getRowId(r) : (r[idKey || 'id'] as number | string));
                    return (
                        <>
                            <button
                                className="btn btn-sm btn-link p-0 me-2"
                                title={`Editar ${label}`}
                                aria-label="Editar"
                                onClick={() => openEdit(id)}
                            >
                                <CIcon icon="cilPencil" />
                            </button>
                            <button
                                className="btn btn-sm btn-link p-0 text-danger"
                                title={`Excluir ${label}`}
                                aria-label="Excluir"
                                onClick={() => remove(id)}
                            >
                                <CIcon icon="cilTrash" />
                            </button>
                        </>
                    );
                }}
            />
            <FormModal
                visible={open}
                title={editing ? `Editar ${label}` : `Novo ${label}`}
                fields={fields}
                value={form}
                onChange={setForm}
                onClose={close}
                onSubmit={submit}
                loading={loading}
                error={error}
                validate={formValidate}
            />
        </>
    );
}
