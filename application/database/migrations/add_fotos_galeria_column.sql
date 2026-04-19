-- Migration: Add fotos_galeria_json column to tec_os_execucao
-- Run this SQL if you get error "Unknown column 'fotos_galeria_json'"

ALTER TABLE `tec_os_execucao`
ADD COLUMN IF NOT EXISTS `fotos_galeria_json` JSON NULL AFTER `fotos_durante`;
