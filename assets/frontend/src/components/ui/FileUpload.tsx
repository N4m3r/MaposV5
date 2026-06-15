/**
 * Componente FileUpload
 *
 * Dropzone + botao de upload. Mostra previews (imagens) e progresso.
 *
 * Props:
 *   - value: UploadResult[]              arquivos ja enviados
 *   - onChange: (files) => void          chamado quando subir/remover
 *   - endpoint: { folder, entity, id }   onde salvar
 *   - accept?: string                    ex: 'image/*,.pdf'
 *   - maxSize?: number                   em MB (default 10)
 *   - maxFiles?: number                  qtd maxima (default 5)
 *   - multiple?: boolean                 default true
 *   - disabled?: boolean
 */
import { useState, useRef, useCallback, DragEvent, ChangeEvent } from 'react';
import { CButton, CProgress, CSpinner } from '@coreui/react';
import CIcon from '@coreui/icons-react';
import { upload, remove_upload, formatBytes, fileKind, type UploadResult } from '../../api/upload';
import { toast } from '../ui/Toast';

export interface FileUploadProps {
    value?: UploadResult[];
    onChange?: (files: UploadResult[]) => void;
    folder?: string;
    entity?: string;
    entityId?: number | string;
    accept?: string;
    maxSize?: number;
    maxFiles?: number;
    multiple?: boolean;
    disabled?: boolean;
}

const KIND_ICONS: Record<string, string> = {
    image: 'cilImage',
    pdf: 'cilFile',
    doc: 'cilDescription',
    xls: 'cilSpreadsheet',
    archive: 'cilZip',
    other: 'cilPaperclip',
};

export function FileUpload({
    value = [],
    onChange,
    folder = 'uploads',
    entity = 'misc',
    entityId,
    accept = '*/*',
    maxSize = 10,
    maxFiles = 5,
    multiple = true,
    disabled = false,
}: FileUploadProps) {
    const [files, setFiles] = useState<UploadResult[]>(value);
    const [dragOver, setDragOver] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [progress, setProgress] = useState(0);
    const inputRef = useRef<HTMLInputElement>(null);

    const remaining = maxFiles - files.length;

    const handleFiles = useCallback(async (incoming: FileList | null) => {
        if (!incoming || incoming.length === 0) return;
        const list = Array.from(incoming);

        // Validacoes
        const maxBytes = maxSize * 1024 * 1024;
        const tooBig = list.find((f) => f.size > maxBytes);
        if (tooBig) {
            toast.error(`${tooBig.name} excede ${maxSize}MB`);
            return;
        }
        if (!multiple && list.length > 1) {
            toast.warning('Selecione apenas 1 arquivo');
            return;
        }
        if (files.length + list.length > maxFiles) {
            toast.warning(`Limite de ${maxFiles} arquivos`);
            return;
        }

        setUploading(true);
        setProgress(0);
        try {
            const uploaded = await upload(list, {
                folder,
                entity,
                entityId,
                onProgress: setProgress,
            });
            const next = multiple ? [...files, ...uploaded] : uploaded;
            setFiles(next);
            onChange?.(next);
            toast.success(`${uploaded.length} arquivo(s) enviado(s)`);
        } catch (e) {
            toast.error(e instanceof Error ? e.message : 'Falha no upload');
        } finally {
            setUploading(false);
            setProgress(0);
        }
    }, [files, folder, entity, entityId, maxSize, maxFiles, multiple, onChange]);

    function onInputChange(e: ChangeEvent<HTMLInputElement>) {
        handleFiles(e.target.files);
        // Limpa o input para permitir re-upload do mesmo arquivo
        e.target.value = '';
    }

    function onDrop(e: DragEvent<HTMLDivElement>) {
        e.preventDefault();
        setDragOver(false);
        if (disabled) return;
        handleFiles(e.dataTransfer.files);
    }

    function onDragOver(e: DragEvent<HTMLDivElement>) {
        e.preventDefault();
        if (!disabled) setDragOver(true);
    }

    function onDragLeave() {
        setDragOver(false);
    }

    async function handleRemove(idx: number) {
        const f = files[idx];
        if (!window.confirm(`Remover ${f.originalName || f.name}?`)) return;
        try {
            await remove_upload(f.url);
            const next = files.filter((_, i) => i !== idx);
            setFiles(next);
            onChange?.(next);
        } catch (e) {
            toast.error(e instanceof Error ? e.message : 'Falha ao remover');
        }
    }

    return (
        <div className={`file-upload ${dragOver ? 'is-drag-over' : ''} ${disabled ? 'is-disabled' : ''}`}>
            <div
                className="file-upload-dropzone"
                onDrop={onDrop}
                onDragOver={onDragOver}
                onDragLeave={onDragLeave}
                onClick={() => !disabled && inputRef.current?.click()}
                role="button"
                tabIndex={0}
            >
                <input
                    ref={inputRef}
                    type="file"
                    accept={accept}
                    multiple={multiple}
                    onChange={onInputChange}
                    style={{ display: 'none' }}
                    disabled={disabled || uploading}
                />
                <CIcon icon="cilCloudUpload" size="3xl" className="file-upload-icon" />
                <p className="file-upload-title">
                    {uploading
                        ? 'Enviando...'
                        : dragOver
                            ? 'Solte aqui'
                            : 'Arraste arquivos ou clique para selecionar'}
                </p>
                <p className="file-upload-hint">
                    {multiple ? `Até ${maxFiles} arquivos` : '1 arquivo'} ·
                    {' '}Máx {maxSize}MB cada
                </p>
                {uploading && (
                    <CProgress className="file-upload-progress" value={progress} color="primary">
                        {progress}%
                    </CProgress>
                )}
            </div>

            {files.length > 0 && (
                <ul className="file-upload-list">
                    {files.map((f, i) => {
                        const kind = fileKind(f.type);
                        return (
                            <li key={i} className="file-upload-item">
                                {kind === 'image' ? (
                                    <a href={f.url} target="_blank" rel="noopener noreferrer" className="file-upload-thumb">
                                        <img src={f.url} alt={f.originalName} />
                                    </a>
                                ) : (
                                    <div className="file-upload-thumb file-upload-thumb-icon">
                                        <CIcon icon={KIND_ICONS[kind]} size="3xl" />
                                    </div>
                                )}
                                <div className="file-upload-meta">
                                    <a href={f.url} target="_blank" rel="noopener noreferrer" className="file-upload-name">
                                        {f.originalName || f.name}
                                    </a>
                                    <small className="file-upload-size">{formatBytes(f.size)}</small>
                                </div>
                                <CButton
                                    color="danger"
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => handleRemove(i)}
                                    disabled={uploading || disabled}
                                    aria-label="Remover"
                                >
                                    <CIcon icon="cilTrash" />
                                </CButton>
                            </li>
                        );
                    })}
                </ul>
            )}

            {uploading && (
                <div className="file-upload-overlay">
                    <CSpinner color="primary" />
                </div>
            )}
        </div>
    );
}

export default FileUpload;
