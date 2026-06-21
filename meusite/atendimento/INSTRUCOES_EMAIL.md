# Instruções para Configuração do Envio de Email

## Configuração do Gmail

Para que o sistema funcione corretamente e envie os leads por email, você precisa configurar as credenciais do Gmail no arquivo `lead_server.py`.

### Passos:

1. **Ativar a verificação em duas etapas no Gmail:**
   - Acesse sua conta do Google
   - Vá em "Segurança"
   - Ative a "Verificação em duas etapas"

2. **Gerar uma senha de app:**
   - Na seção "Segurança" da sua conta Google
   - Clique em "Senhas de app"
   - Selecione "Email" e "Outro (nome personalizado)"
   - Digite "Capturador de Leads"
   - Copie a senha gerada (16 caracteres)

3. **Configurar o arquivo lead_server.py:**
   - Abra o arquivo `lead_server.py`
   - Substitua `"seu_email@gmail.com"` pelo seu email do Gmail
   - Substitua `"sua_senha_de_app"` pela senha de app gerada no passo 2

### Exemplo de configuração:

```python
sender_email = "seuemail@gmail.com"
password = "abcd efgh ijkl mnop"  # Senha de app de 16 caracteres
```

## Como executar o servidor

1. **Instalar dependências:**
```bash
pip install flask flask-cors
```

2. **Executar o servidor:**
```bash
python lead_server.py
```

O servidor ficará rodando na porta 5001.

## Testando o sistema

1. Abra o arquivo `perguntas.html` no navegador
2. Preencha o formulário
3. Verifique se o email chegou em brunosantos@agenciacyborg.com.br

## Alternativa sem servidor Python

Se preferir não usar o servidor Python, você pode modificar o JavaScript no arquivo `perguntas.html` para usar um serviço como Formspree ou EmailJS:

### Usando Formspree:

1. Cadastre-se em https://formspree.io
2. Crie um novo formulário
3. Substitua a URL no JavaScript:

```javascript
fetch('https://formspree.io/f/SEU_ID_AQUI', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(formData)
})
```

### Usando EmailJS:

1. Cadastre-se em https://emailjs.com
2. Configure um template de email
3. Substitua o JavaScript conforme a documentação do EmailJS

## Estrutura dos arquivos

- `perguntas.html` - Página principal com o formulário
- `lead_server.py` - Servidor backend para envio de emails
- `css/` - Estilos (não modificar)
- `js/` - Scripts (não modificar)
- `img/` - Imagens (não modificar)
- `fonts/` - Fontes (não modificar)

