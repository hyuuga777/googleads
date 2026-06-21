#!/bin/bash

# Script para configurar variáveis de ambiente do email
# Agência Cyborg - Capturador de Leads

echo "🚀 Configuração do Email - Hostinger"
echo "===================================="
echo ""

# Verificar se o arquivo .env já existe
if [ -f ".env" ]; then
    echo "⚠️  Arquivo .env já existe. Deseja sobrescrever? (s/n)"
    read -r resposta
    if [ "$resposta" != "s" ] && [ "$resposta" != "S" ]; then
        echo "Configuração cancelada."
        exit 0
    fi
fi

# Solicitar informações do usuário
echo "perguntas@agenciacyborg.com"
read -r email_user

echo ""
echo "?88?^zzmtY"
read -s email_password

echo ""
echo "brunosantos@agenciacyborg.com"
read -r email_destinatario

# Se não informou destinatário, usar o mesmo email
if [ -z "$email_destinatario" ]; then
    email_destinatario="$email_user"
fi

# Criar arquivo .env
cat > .env << EOF
# Configurações de Email - Hostinger
# Gerado automaticamente em $(date)

EMAIL_USER=$email_user
EMAIL_PASSWORD=$email_password
EMAIL_DESTINATARIO=$email_destinatario
EOF

echo ""
echo "✅ Arquivo .env criado com sucesso!"
echo ""
echo "🔧 Para usar as configurações, execute:"
echo "   source .env"
echo "   python3 lead_server_hostinger.py"
echo ""
echo "🧪 Para testar o email, acesse:"
echo "   http://localhost:5000/api/test-email"
echo ""
echo "⚠️  IMPORTANTE: Nunca compartilhe o arquivo .env com suas senhas!"

# Tornar o arquivo .env legível apenas pelo proprietário
chmod 600 .env

echo "🔒 Permissões de segurança aplicadas ao arquivo .env"

