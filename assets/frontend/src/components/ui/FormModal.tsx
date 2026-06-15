/**
 * FormModal generico.
 *
 * Modal com campos configuraveis (input/select/textarea/date/number).
 * Gerencia: open/close, value/change, save, loading, error.
 *
 * Cada FieldDef declara: key, label, type, required, options, placeholder, etc.
 *
 * Uso:
 *   <FormModal
 *     visible={open}
 *     title={editing ? 'Editar OS' : 'Nova OS'}
 *     fields={OS_FIELDS}
 *     value={form}
 *     onChange={setForm}
 *     onClose={() => setOpen(false)}
 *     onSubmit={async (v) => { await save(v); setOpen(false); }}
 *     loading={saving}
 *     error={error}
 *   />
 */
import { useEffect, useState, type ReactNode } from 'react';
import { CModal, CModalHeader, CModalTitle, CModalBody, CModalFooter, CButton, CFormInput, CFormSelect, CFormTextarea, CFormLabel, CFormCheck } from '@coreui/react';
import { useEscKey, useFocusTrap } from '../../hooks/useA11y';

export type FieldType = 'text' | 'password' | 'number' | 'email' | 'date' | 'datetime-local' | 'select' | 'textarea' | 'checkbox';

export interface FieldDef {
    key: string;
    label: string;
    type: FieldType;
    required?: boolean;
    placeholder?: string;
    options?: Array<{ value: string | number; label: string }>;
    rows?: number;
    step?: string;
    min?: number;
    max?: number;
    pattern?: string;
    help?: string;
    col?: number; // grid (1-12) — futuro
}

interface FormModalProps {
    visible: boolean;
    title: string;
    fields: FieldDef[];
    value: Record<string, unknown>;
    onChange: (v: Record<string, unknown>) => void;
    onClose: () => void;
    onSubmit: () => void | Promise<unknown>;
    loading?: boolean;
    error?: string | null;
    size?: 'sm' | 'lg' | 'xl';
    /** Footer custom (botoes extras) */
    extraFooter?: ReactNode;
    /** Validacao: retorna mensagem de erro ou null */
    validate?: (v: Record<string, unknown>) => string | null;
}

export function FormModal({
    visible,
    title,
    fields,
    value,
    onChange,
    onClose,
    onSubmit,
    loading = false,
    error = null,
    size,
    extraFooter,
    validate,
}: FormModalProps) {
    const [validationErr, setValidationErr] = useState<string | null>(null);
    const trapRef = useFocusTrap<HTMLDivElement>(visible);

    // Limpa erro de validacao sempre que mudar valor
    useEffect(() => { setValidationErr(null); }, [value]);

    // ESC fecha o modal
    useEscKey(() => {
        if (visible && !loading) onClose();
    }, visible);

    function setField(key: string, v: unknown) {
        onChange({ ...value, [key]: v });
    }

    async function handleSubmit() {
        if (validate) {
            const err = validate(value);
            if (err) { setValidationErr(err); return; }
        }
        await onSubmit();
    }

    return (
        <CModal visible={visible} onClose={onClose} size={size} backdrop="static" aria-labelledby="form-modal-title">
            <CModalHeader closeButton>
                <CModalTitle id="form-modal-title">{title}</CModalTitle>
            </CModalHeader>
            <CModalBody ref={trapRef}>
                {(error || validationErr) && (
                    <div className="alert alert-danger" role="alert">
                        {error || validationErr}
                    </div>
                )}
                <div className="row g-3">
                    {fields.map((f) => {
                        const v = value[f.key];
                        const common = {
                            id: `f-${f.key}`,
                            name: f.key,
                            required: f.required,
                            disabled: loading,
                            placeholder: f.placeholder,
                            'aria-required': f.required ? true : undefined,
                            'aria-invalid': validationErr && f.required && !v ? true : undefined,
                            'aria-describedby': f.help ? `f-${f.key}-help` : undefined,
                        };
                        let control: ReactNode = null;
                        switch (f.type) {
                            case 'select':
                                control = (
                                    <CFormSelect
                                        {...common}
                                        value={String(v ?? '')}
                                        onChange={(e) => setField(f.key, e.target.value)}
                                    >
                                        <option value="">— selecione —</option>
                                        {(f.options || []).map((o) => (
                                            <option key={String(o.value)} value={String(o.value)}>{o.label}</option>
                                        ))}
                                    </CFormSelect>
                                );
                                break;
                            case 'textarea':
                                control = (
                                    <CFormTextarea
                                        {...common}
                                        rows={f.rows || 3}
                                        value={String(v ?? '')}
                                        onChange={(e) => setField(f.key, e.target.value)}
                                    />
                                );
                                break;
                            case 'checkbox':
                                control = (
                                    <CFormCheck
                                        id={common.id}
                                        checked={!!v}
                                        disabled={loading}
                                        label={f.label}
                                        onChange={(e) => setField(f.key, e.target.checked)}
                                    />
                                );
                                break;
                            case 'number':
                                control = (
                                    <CFormInput
                                        {...common}
                                        type="number"
                                        step={f.step}
                                        min={f.min}
                                        max={f.max}
                                        value={v === null || v === undefined ? '' : String(v)}
                                        onChange={(e) => setField(f.key, e.target.value === '' ? null : Number(e.target.value))}
                                    />
                                );
                                break;
                            default:
                                control = (
                                    <CFormInput
                                        {...common}
                                        type={f.type}
                                        pattern={f.pattern}
                                        value={v === null || v === undefined ? '' : String(v)}
                                        onChange={(e) => setField(f.key, e.target.value)}
                                    />
                                );
                        }
                        if (f.type === 'checkbox') {
                            return <div className="col-12" key={f.key}>{control}</div>;
                        }
                        return (
                            <div className="col-md-6" key={f.key}>
                                <CFormLabel htmlFor={common.id}>
                                    {f.label}{f.required ? ' *' : ''}
                                </CFormLabel>
                                {control}
                                {f.help && <small id={`f-${f.key}-help`} className="text-muted d-block mt-1">{f.help}</small>}
                            </div>
                        );
                    })}
                </div>
            </CModalBody>
            <CModalFooter>
                {extraFooter}
                <CButton color="secondary" onClick={onClose} disabled={loading}>Cancelar</CButton>
                <CButton color="primary" onClick={handleSubmit} disabled={loading}>
                    {loading ? 'Salvando...' : 'Salvar'}
                </CButton>
            </CModalFooter>
        </CModal>
    );
}
