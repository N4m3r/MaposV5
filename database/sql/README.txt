===============================================================================
SCRIPTS SQL - MAPOS V5
===============================================================================

Arquivos SQL para instalação e atualização do banco de dados.

ARQUIVOS:
---------

1. banco.sql
   - Estrutura inicial completa do banco
   - Use para instalação limpa

2. atualizacao_completa_v5.sql
   - Atualização para versão 5
   - Cria tabelas: email_queue, webhooks, nfse, certificado, dre, impostos

3. atualizar.sql
   - Atualizações incrementais

4. atualizacao_mapos_corrigida_v2.sql
   - Correções específicas

5. atualizacao_tecnico.sql
   - Atualização relacionada a técnicos

COMO USAR:
----------

# Nova instalação:
mysql -u usuario -p banco_mapos < banco.sql

# Atualização para V5:
mysql -u usuario -p banco_mapos < atualizacao_completa_v5.sql

# Ou via PHP:
php application/database/migrations/run_migrations.php

===============================================================================
