# 🚀 Capturador de Leads - Agência Cyborg
## Configuração para Hostinger com Segurança

### 📋 Arquivos Incluídos

- `perguntas_simples.html` - Formulário de captura de leads
- `lead_server_hostinger.py` - Servidor backend para Hostinger
- `configurar_email.sh` - Script de configuração automática
- `.env.exemplo` - Exemplo de configuração de variáveis
- `INSTRUCOES_HOSTINGER.md` - Este arquivo de instruções

### 🔧 Configuração Rápida

#### Opção 1: Configuração Automática (Recomendada)
```bash
# 1. Execute o script de configuração
./configurar_email.sh

# 2. Inicie o servidor
python3 lead_server_hostinger.py
```

#### Opção 2: Configuração Manual
```bash
# 1. Copie o arquivo de exemplo
cp .env.exemplo .env

# 2. Edite o arquivo .env com suas informações
nano .env

# 3. Configure as variáveis no terminal
source .env

# 4. Inicie o servidor
python3 lead_server_hostinger.py
```

### 📧 Configurações de Email da Hostinger

#### Informações Necessárias:
- **Servidor SMTP:** smtp.hostinger.com
- **Porta:** 587 (TLS)
- **Seu email:** seu-email@seudominio.com.br
- **Senha:** sua senha do email da Hostinger

#### Como obter as informações:
1. Acesse o painel da Hostinger
2. Vá em "Email" → "Gerenciar"
3. Use seu email completo como usuário
4. Use a senha do seu email

### 🔒 Segurança das Credenciais

#### ✅ O que fizemos para proteger suas senhas:
- **Variáveis de ambiente:** Senhas não ficam no código
- **Arquivo .env:** Credenciais em arquivo separado
- **Permissões restritivas:** Apenas você pode ler o arquivo
- **Exemplo seguro:** Arquivo .env.exemplo sem dados reais

#### ⚠️ Importantes medidas de segurança:
- Nunca compartilhe o arquivo `.env`
- Adicione `.env` no `.gitignore` se usar Git
- Use senhas de aplicativo quando disponível
- Mantenha o servidor em ambiente seguro

### 🧪 Testando a Configuração

#### 1. Verificar se o servidor está funcionando:
```bash
curl http://localhost:5000/health
```

#### 2. Testar envio de email:
```bash
curl http://localhost:5000/api/test-email
```

#### 3. Ou acesse no navegador:
- Status: http://localhost:5000/health
- Teste de email: http://localhost:5000/api/test-email

### 🌐 Usando o Formulário

1. Abra `perguntas_simples.html` no navegador
2. Preencha as 4 perguntas
3. Clique em "Enviar"
4. Verifique se o email chegou em brunosantos@agenciacyborg.com.br

### 🚨 Solução de Problemas

#### Erro: "Variáveis de ambiente não configuradas"
```bash
# Verifique se o arquivo .env existe
ls -la .env

# Configure as variáveis
source .env

# Ou execute o script de configuração
./configurar_email.sh
```

#### Erro: "Falha na autenticação SMTP"
- Verifique se o email e senha estão corretos
- Confirme se o email está ativo na Hostinger
- Tente usar uma senha de aplicativo se disponível

#### Erro: "Conexão recusada"
- Verifique sua conexão com a internet
- Confirme se a porta 587 não está bloqueada
- Teste com outro cliente de email primeiro

### 📱 Integração com WhatsApp

O botão do WhatsApp está configurado para:
- **Número:** +55 35 997286067
- **Mensagem automática:** "Olá! Vim através do site e gostaria de falar com um especialista."

### 🔄 Atualizações e Manutenção

#### Para alterar o email destinatário:
1. Edite o arquivo `.env`
2. Altere a linha `EMAIL_DESTINATARIO=`
3. Reinicie o servidor

#### Para alterar as configurações de email:
1. Execute novamente `./configurar_email.sh`
2. Ou edite manualmente o arquivo `.env`

### 📞 Suporte

Se precisar de ajuda:
1. Verifique os logs do servidor no terminal
2. Teste as configurações com os endpoints de teste
3. Confirme as configurações de email na Hostinger

### 🎯 Próximos Passos

1. ✅ Configure as variáveis de ambiente
2. ✅ Teste o envio de email
3. ✅ Teste o formulário completo
4. 🚀 Coloque em produção!

---

**Agência Cyborg** - Capturador de Leads Profissional
*Versão com Hostinger e Segurança Avançada*

