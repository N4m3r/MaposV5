/**
 * API de upload de arquivos.
 *
 * Endpoint:
 *   POST /api/upload  (multipart/form-data)
 *   - files: File[]  (1+ arquivos)
 *   - folder?: string  (subpasta em /assets/uploads/...)
 *   - entity?: string  (ex: 'os', 'cliente') - usado no nome do arquivo
 *   - entity_id?: number|string  (id do registro)
 *   res: { success: bool, files: UploadResult[] }
 */
import { api } from './client';

export interface UploadResult {
    name: string;
    originalName: string;
    size: number;
    type: string;
    url: string;
    thumb?: string;
}

export interface UploadResponse {
    success: boolean;
    message?: string;
    files?: UploadResult[];
    errors?: string[];
}

export interface UploadOptions {
    folder?: string;
    entity?: string;
    entityId?: number | string;
    onProgress?: (percent: number) => void;
}

export async function upload(
    files: File | File[],
    options: UploadOptions = {},
): Promise<UploadResult[]> {
    const list = Array.isArray(files) ? files : [files];
    if (list.length === 0) return [];

    const form = new FormData();
    list.forEach((f) => form.append('files[]', f, f.name));
    if (options.folder) form.append('folder', options.folder);
    if (options.entity) form.append('entity', options.entity);
    if (options.entityId !== undefined) form.append('entity_id', String(options.entityId));

    const { data } = await api.post<UploadResponse>('api/upload', form, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (evt) => {
            if (options.onProgress && evt.total) {
                const pct = Math.round((evt.loaded * 100) / evt.total);
                options.onProgress(pct);
            }
        },
    });

    if (!data.success) {
        throw new Error(data.message || data.errors?.join('; ') || 'Falha no upload');
    }
    return data.files || [];
}

export async function remove_upload(fileUrl: string): Promise<boolean> {
    const { data } = await api.post<{ success: boolean }>('api/upload_delete', { url: fileUrl });
    return !!data.success;
}

/**
 * Formata bytes para string legivel.
 */
export function formatBytes(bytes: number, decimals = 1): string {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(decimals))} ${sizes[i]}`;
}

/**
 * Tipo de arquivo a partir do mime-type.
 */
export function fileKind(type: string): 'image' | 'pdf' | 'doc' | 'xls' | 'archive' | 'other' {
    if (type.startsWith('image/')) return 'image';
    if (type === 'application/pdf') return 'pdf';
    // Planilhas: .xls (ms-excel) ou .xlsx (openxmlformats-officedocument.spreadsheetml)
    if (type === 'application/vnd.ms-excel' ||
        type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') return 'xls';
    // Documentos: .doc, .docx, .odt
    if (type === 'application/msword' ||
        type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
        type === 'application/vnd.oasis.opendocument.text') return 'doc';
    // Compactados
    if (type.includes('zip') || type.includes('rar') || type.includes('7z')) return 'archive';
    return 'other';
}
