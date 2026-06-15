/**
 * Testes dos helpers de upload.
 */
import { describe, it, expect } from 'vitest';
import { formatBytes, fileKind } from '../src/api/upload';

describe('formatBytes', () => {
    it('formata 0 bytes', () => {
        expect(formatBytes(0)).toBe('0 B');
    });

    it('formata bytes < 1KB', () => {
        expect(formatBytes(500)).toBe('500 B');
    });

    it('formata kilobytes', () => {
        expect(formatBytes(1024)).toBe('1 KB');
        expect(formatBytes(1536)).toBe('1.5 KB');
    });

    it('formata megabytes', () => {
        expect(formatBytes(1024 * 1024)).toBe('1 MB');
        expect(formatBytes(5.5 * 1024 * 1024)).toBe('5.5 MB');
    });

    it('formata gigabytes', () => {
        expect(formatBytes(2 * 1024 * 1024 * 1024)).toBe('2 GB');
    });
});

describe('fileKind', () => {
    it('detecta imagens', () => {
        expect(fileKind('image/jpeg')).toBe('image');
        expect(fileKind('image/png')).toBe('image');
        expect(fileKind('image/webp')).toBe('image');
    });

    it('detecta PDF', () => {
        expect(fileKind('application/pdf')).toBe('pdf');
    });

    it('detecta documentos Word', () => {
        expect(fileKind('application/msword')).toBe('doc');
        expect(fileKind('application/vnd.openxmlformats-officedocument.wordprocessingml.document')).toBe('doc');
    });

    it('detecta planilhas', () => {
        expect(fileKind('application/vnd.ms-excel')).toBe('xls');
        expect(fileKind('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')).toBe('xls');
    });

    it('detecta arquivos compactados', () => {
        expect(fileKind('application/zip')).toBe('archive');
        expect(fileKind('application/x-rar-compressed')).toBe('archive');
    });

    it('cai em other para tipos nao classificados', () => {
        expect(fileKind('text/plain')).toBe('other');
        expect(fileKind('application/octet-stream')).toBe('other');
    });
});
