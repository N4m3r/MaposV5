# Changelog

Todas as mudancas notaveis deste projeto serao documentadas neste arquivo.

O formato e baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/).

## [5.0.0] - 2026-04-25

### Adicionado
- Sistema completo de **Gestao de Obras** com etapas, equipe e atividades
- **Portal do Tecnico** para acompanhamento mobile de obras
- **Sistema de Atividades** para registro detalhado de servicos em campo
- **Notificacoes** multi-canal (e-mail, WhatsApp, sistema)
- **Geolocalizacao** com check-in/check-out
- **Migracoes** de banco de dados via web e CLI
- **Configuracoes de Obras** em pagina unica com CRUD completo
- Painel de **Configuracoes** para tipos, status, especialidades, funcoes e preferencias
- Suporte a **reatendimentos** e reabertura de atividades

### Modificado
- Atualizado para **PHP 8.3**
- Melhorias de seguranca em CSRF e permissoes
- Otimizacoes de performance em consultas SQL
- Interface responsiva para dispositivos moveis

### Corrigido
- Erros de migracao em tabelas inexistentes
- Duplicatas em cadastros com UNIQUE KEY
- Renderizacao de flashdata em JavaScript
- Compatibilidade com navegadores antigos (jQuery 1.12.4)

## [4.x] - 2025

### Base
- Sistema Map-OS original com Ordens de Servico
- Cadastro de clientes, produtos e usuarios
- Financeiro e relatorios
- Emissao de OS e garantias
