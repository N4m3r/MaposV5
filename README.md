# IVMS-CRUD / Map-OS v5

Sistema de gestão empresarial completo baseado em **CodeIgniter 3**, com módulos avancados para **Ordens de Servico (OS)**, **Gestao de Obras**, **Atividades de Tecnicos**, **Portal do Cliente** e **Notificacoes**.

---

## 📋 Requisitos

| Requisito | Versao Minima |
|-----------|--------------|
| PHP | 8.3 |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Apache / Nginx | com mod_rewrite |
| Composer | 2.x |

### Extensoes PHP obrigatorias
- `ext-curl`
- `ext-gd`
- `ext-mysqli`
- `ext-json`
- `ext-mbstring`

---

## 🚀 Instalacao Rapida

### 1. Clone o repositorio

```bash
git clone https://github.com/seu-usuario/MaposV5.git
cd MaposV5
```

### 2. Instale as dependencias

```bash
composer install
```

### 3. Configure o ambiente

Copie o arquivo de exemplo e ajuste as variaveis:

```bash
cp .env.example .env
```

Edite o `.env` com suas credenciais de banco de dados:

```env
DB_HOSTNAME=localhost
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
DB_DATABASE=maposv5
```

### 4. Execute as migracoes

Acesse via navegador (logado como administrador):
```
https://seu-dominio.com/migrate/latest
```

Ou via CLI:
```bash
php index.php tools migrate
```

### 5. Instale os modulos adicionais (opcional)

```bash
# Sistema de atividades de tecnicos
php scripts/install_atividades.php

# Integracao de obras com etapas
php scripts/install_integracao_obras.php
php scripts/install_integracao_etapas.php

# Notificacoes
php scripts/install_notificacoes.php
```

### 6. Acesse o sistema

```
https://seu-dominio.com
```

Credenciais padrao:
- **Usuario:** admin@admin.com
- **Senha:** admin

> ⚠️ **Importante:** Altere a senha padrao imediatamente apos o primeiro acesso.

---

## 📁 Estrutura do Projeto

```
MaposV5/
├── application/          # Codigo principal (CodeIgniter 3)
│   ├── config/           # Configuracoes
│   ├── controllers/      # Controladores
│   ├── models/           # Modelos
│   ├── views/            # Templates
│   ├── database/
│   │   └── migrations/   # Migracoes do banco de dados
│   ├── libraries/        # Bibliotecas customizadas
│   └── vendor/           # Dependencias Composer
├── assets/               # CSS, JS, imagens, fontes
├── database/             # SQLs e seeds (legado)
├── docs/                 # Documentacao
├── docker/               # Configuracoes Docker
├── install/              # Instalador web
├── logs/                 # Logs da aplicacao
├── mobile/               # Recursos mobile/PWA
├── projeto/              # Arquivos de projeto e planejamento
├── scripts/              # Scripts utilitarios (instalacao)
├── tests/                # Scripts de teste e verificacao
├── tmp/                  # Arquivos temporarios e diagnosticos
├── tools/                # Ferramentas CLI (migracoes, cache)
├── updates/              # Scripts de atualizacao
├── .env.example          # Exemplo de variaveis de ambiente
├── .htaccess             # Regras Apache
├── composer.json         # Dependencias PHP
├── CHANGELOG.md          # Historico de alteracoes
└── index.php             # Entry point
```

---

## 🏗️ Modulos Principais

### Gestao de Obras
- Cadastro completo de obras com cliente, endereco e status
- Sistema de etapas com progresso automatico
- Equipe tecnica alocada por obra
- Diario de obra com fotos e geolocalizacao
- Materiais e custos
- Check-in / Check-out de tecnicos
- Portal do tecnico mobile-friendly

### Atividades de Tecnicos
- Registro detalhado de atividades em campo
- Categorias: Rede, CFTV, Seguranca, Infra, Internet, Geral
- Checklist por atividade
- Fotos com geolocalizacao
- Pausas e retomada
- Assinatura do cliente
- Reatendimentos

### Ordens de Servico (OS)
- Cadastro e acompanhamento de OS
- Vinculacao com obras
- Emissao de garantia
- Relatorios financeiros

### Portal do Cliente
- Acesso restrito por CPF/CNPJ
- Visualizacao de obras e etapas
- Acompanhamento de atividades
- Download de relatorios

### Notificacoes
- Eventos configuraveis (nova obra, conclusao, atraso, etc.)
- Canais: E-mail, WhatsApp, Sistema

---

## ⚙️ Configuracoes do Sistema de Obras

Acesse: **Obras > Configuracoes**

Gerencie diretamente no banco de dados:
- Tipos de obra
- Status de obra
- Especialidades (etapas)
- Funcoes da equipe
- Status de atividade
- Tipos de atividade
- Preferencias e notificacoes

---

## 🐳 Docker (Opcional)

```bash
cd docker
docker-compose up -d
```

Acesse: `http://localhost:8080`

---

## 🔒 Seguranca

- CSRF protegido em todos os formularios
- Permissoes baseadas em perfil (admin, tecnico, cliente)
- SQL injection mitigado via query builder
- Upload de arquivos validado
- Variaveis sensiveis via `.env`

---

## 🛠️ Comandos CLI Uteis

```bash
# Executar migracoes
php index.php tools migrate

# Criar nova migracao
php index.php tools migration "nome_da_migracao"

# Executar migracoes via CLI helper
php tools/cli.php migrate

# Limpar cache
php tools/clear_cache.php

# Verificar permissoes de arquivos
php scripts/verificar_permissoes.php
```

---

## 📝 Licenca

Este projeto e licenciado sob a licenca MIT.

---

## 🤝 Contribuicao

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/nova-feature`)
3. Commit suas mudancas (`git commit -m 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

---

## 📧 Suporte

Para duvidas, sugestoes ou relatorio de bugs, abra uma issue no GitHub ou entre em contato via e-mail.

---

<p align="center">Desenvolvido com ❤️ para gestao eficiente de obras e servicos.</p>
