# 🚀 Upload SFTP - MAPOS V5

## 📋 Configuração

| Configuração | Valor |
|--------------|-------|
| **Protocolo** | SFTP (SSH File Transfer Protocol) |
| **Servidor** | `ftp.jj-ferreiras.com.br` |
| **Porta** | `22` |
| **Login** | `jj-ferreiras` |
| **Senha** | `93982740tT` |
| **Pasta Remota** | `/home/jj-ferreiras/www/mapos3` |

---

## 🎯 Método 1: FileZilla (Recomendado)

### Download
Baixe o FileZilla: https://filezilla-project.org/download.php

### Configuração Rápida
1. Abra o FileZilla
2. Clique em **Gerenciador de Sites** (Ctrl+S)
3. Clique em **Novo Site** e nomeie: `Mapos Produção`
4. Configure:
   - **Protocolo:** SFTP - SSH File Transfer Protocol
   - **Host:** `ftp.jj-ferreiras.com.br`
   - **Porta:** `22`
   - **Tipo de Logon:** Normal
   - **Usuário:** `jj-ferreiras`
   - **Senha:** `93982740tT`
5. Clique em **Conectar**

### Upload
1. Na **janela da esquerda** (Local): Navegue até a pasta do mapos
2. Na **janela da direita** (Remoto): Navegue para `/home/jj-ferreiras/www/mapos3`
3. **Selecione todos os arquivos** na pasta local
4. Clique com **botão direito** → **Upload**
5. Aguarde a conclusão

---

## ⚡ Método 2: WinSCP (Mais Rápido)

### Download
Baixe o WinSCP: https://winscp.net/eng/download.php

### Usar Script Automatizado
1. Instale o WinSCP
2. Execute: `upload_winscp_script.bat`
3. Aguarde o upload automático

### Configuração Manual
1. Abra WinSCP
2. Selecione **SFTP** como protocolo
3. Preencha:
   - Servidor: `ftp.jj-ferreiras.com.br`
   - Porta: `22`
   - Usuário: `jj-ferreiras`
   - Senha: `93982740tT`
4. Clique em **Login**
5. Arraste arquivos da esquerda para a direita

---

## 💻 Método 3: PowerShell Script

Execute o script automatizado:
```powershell
# No PowerShell como Administrador
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
.\upload_rapido_sftp.ps1
```

---

## 📁 Arquivos a Enviar

### ✅ Enviar TUDO (exceto):
- `.git/` (pasta do git)
- `.claude/` (arquivos do Claude)
- `docs/` (documentação)
- `projeto/` (arquivos de planejamento)
- `*.tar.gz` (arquivos compactados antigos)
- `*.log` (logs)

### 📋 Estrutura no Servidor
```
/home/jj-ferreiras/www/
└── mapos3/
    ├── index.php
    ├── application/
    ├── assets/
    ├── system/
    └── ...
```

---

## ⚙️ Configurar Permissões (após upload)

Conecte via SSH e execute:
```bash
ssh jj-ferreiras@ftp.jj-ferreiras.com.br
cd /home/jj-ferreiras/www/mapos3
chmod -R 777 application/logs
chmod -R 777 application/cache
chmod -R 777 assets/uploads
chmod -R 777 updates
```

---

## 🔍 Verificar Upload

Após concluir, acesse:
- **URL:** https://jj-ferreiras.com.br/mapos3

Ou execute o verificador:
- https://jj-ferreiras.com.br/mapos3/scripts/VERIFICAR_E_CORRIGIR.php

---

## ❌ Solução de Problemas

### "Connection refused"
- Verifique se a porta é 22 (SFTP) não 21 (FTP)

### "Permission denied"
- Verifique login e senha
- Certifique-se de que a pasta `mapos3` existe no servidor

### "Transfer stalled"
- Use modo binário nas configurações
- Tente reenviar apenas os arquivos que falharam

---

## ✅ Checklist Pós-Upload

- [ ] Acesse https://jj-ferreiras.com.br/mapos3
- [ ] Faça login no sistema
- [ ] Verifique se aparece o menu "Fila de Emails"
- [ ] Execute: scripts/VERIFICAR_E_CORRIGIR.php
- [ ] Atualize o banco se necessário

---

**Dica:** Para uploads rápidos, use o WinSCP com o script `upload_winscp_script.bat`! 🚀
