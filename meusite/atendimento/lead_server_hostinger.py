#!/usr/bin/env python3
"""
Servidor Flask para Capturador de Leads - Agência Cyborg
Configurado para usar email da Hostinger com segurança
"""

import os
import smtplib
from datetime import datetime
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # Permitir requisições de qualquer origem

# Configurações de email da Hostinger (usando variáveis de ambiente)
EMAIL_HOST = 'smtp.hostinger.com'
EMAIL_PORT = 587
EMAIL_USER = os.getenv('perguntas@agenciacyborg.com.br')  # Seu email da Hostinger
EMAIL_PASSWORD = os.getenv('?88?^zzmtY')  # Senha do seu email
EMAIL_DESTINATARIO = os.getenv('EMAIL_DESTINATARIO', 'brunosantos@agenciacyborg.com.br')

def enviar_email(dados_lead):
    """
    Envia email com os dados do lead capturado
    """
    try:
        # Verificar se as variáveis de ambiente estão configuradas
        if not EMAIL_USER or not EMAIL_PASSWORD:
            print("ERRO: Variáveis de ambiente EMAIL_USER e EMAIL_PASSWORD não configuradas!")
            return False
        
        # Criar mensagem
        msg = MIMEMultipart()
        msg['From'] = EMAIL_USER
        msg['To'] = EMAIL_DESTINATARIO
        msg['Subject'] = f"🚀 Novo Lead Capturado - {dados_lead['nome']}"
        
        # Corpo do email em HTML
        corpo_html = f"""
        <html>
        <head>
            <style>
                body {{ font-family: Arial, sans-serif; line-height: 1.6; color: #333; }}
                .container {{ max-width: 600px; margin: 0 auto; padding: 20px; }}
                .header {{ background: linear-gradient(135deg, #00d4ff, #00a8cc); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }}
                .content {{ background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }}
                .field {{ margin-bottom: 15px; padding: 10px; background: white; border-left: 4px solid #00d4ff; }}
                .field-label {{ font-weight: bold; color: #00a8cc; }}
                .field-value {{ margin-top: 5px; }}
                .footer {{ text-align: center; margin-top: 20px; color: #666; font-size: 12px; }}
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎯 Novo Lead Capturado!</h1>
                    <p>Agência Cyborg - Capturador de Leads</p>
                </div>
                
                <div class="content">
                    <div class="field">
                        <div class="field-label">👤 Nome:</div>
                        <div class="field-value">{dados_lead['nome']}</div>
                    </div>
                    
                    <div class="field">
                        <div class="field-label">📱 Telefone:</div>
                        <div class="field-value">{dados_lead['telefone']}</div>
                    </div>
                    
                    <div class="field">
                        <div class="field-label">📧 Email:</div>
                        <div class="field-value">{dados_lead['email']}</div>
                    </div>
                    
                    <div class="field">
                        <div class="field-label">💬 Como podemos ajudar:</div>
                        <div class="field-value">{dados_lead['ajuda']}</div>
                    </div>
                    
                    <div class="field">
                        <div class="field-label">🕐 Data/Hora:</div>
                        <div class="field-value">{datetime.now().strftime('%d/%m/%Y às %H:%M:%S')}</div>
                    </div>
                </div>
                
                <div class="footer">
                    <p>Este email foi gerado automaticamente pelo sistema de captura de leads.</p>
                    <p>Agência Cyborg © {datetime.now().year}</p>
                </div>
            </div>
        </body>
        </html>
        """
        
        msg.attach(MIMEText(corpo_html, 'html'))
        
        # Conectar ao servidor SMTP da Hostinger
        print(f"Conectando ao servidor SMTP da Hostinger ({EMAIL_HOST}:{EMAIL_PORT})...")
        server = smtplib.SMTP(EMAIL_HOST, EMAIL_PORT)
        server.starttls()  # Habilitar criptografia TLS
        server.login(EMAIL_USER, EMAIL_PASSWORD)
        
        # Enviar email
        texto = msg.as_string()
        server.sendmail(EMAIL_USER, EMAIL_DESTINATARIO, texto)
        server.quit()
        
        print(f"✅ Email enviado com sucesso para {EMAIL_DESTINATARIO}")
        return True
        
    except Exception as e:
        print(f"❌ Erro ao enviar email: {str(e)}")
        return False

@app.route('/health', methods=['GET'])
def health_check():
    """
    Endpoint para verificar se o servidor está funcionando
    """
    return jsonify({
        'status': 'ok',
        'message': 'Servidor do Capturador de Leads está funcionando!',
        'timestamp': datetime.now().isoformat(),
        'email_configured': bool(EMAIL_USER and EMAIL_PASSWORD)
    })

@app.route('/api/submit-lead', methods=['POST'])
def submit_lead():
    """
    Endpoint para receber dados do formulário de leads
    """
    try:
        # Obter dados do formulário
        dados = request.get_json()
        
        # Validar dados obrigatórios
        campos_obrigatorios = ['nome', 'telefone', 'email', 'ajuda']
        for campo in campos_obrigatorios:
            if not dados.get(campo):
                return jsonify({
                    'success': False,
                    'message': f'Campo obrigatório não preenchido: {campo}'
                }), 400
        
        # Validar formato do email
        if '@' not in dados['email'] or '.' not in dados['email']:
            return jsonify({
                'success': False,
                'message': 'Formato de email inválido'
            }), 400
        
        # Log dos dados recebidos
        print(f"📝 Novo lead recebido: {dados['nome']} ({dados['email']})")
        
        # Enviar email
        email_enviado = enviar_email(dados)
        
        if email_enviado:
            return jsonify({
                'success': True,
                'message': 'Lead capturado e email enviado com sucesso!',
                'timestamp': datetime.now().isoformat()
            })
        else:
            return jsonify({
                'success': False,
                'message': 'Lead capturado, mas houve erro no envio do email. Verifique as configurações.'
            }), 500
            
    except Exception as e:
        print(f"❌ Erro no processamento do lead: {str(e)}")
        return jsonify({
            'success': False,
            'message': 'Erro interno do servidor'
        }), 500

@app.route('/api/test-email', methods=['GET'])
def test_email():
    """
    Endpoint para testar configurações de email
    """
    try:
        # Dados de teste
        dados_teste = {
            'nome': 'Teste do Sistema',
            'telefone': '(11) 99999-9999',
            'email': 'teste@exemplo.com',
            'ajuda': 'Este é um email de teste do sistema de captura de leads.'
        }
        
        # Tentar enviar email de teste
        resultado = enviar_email(dados_teste)
        
        if resultado:
            return jsonify({
                'success': True,
                'message': 'Email de teste enviado com sucesso!',
                'config': {
                    'host': EMAIL_HOST,
                    'port': EMAIL_PORT,
                    'user': EMAIL_USER,
                    'destinatario': EMAIL_DESTINATARIO
                }
            })
        else:
            return jsonify({
                'success': False,
                'message': 'Falha no envio do email de teste. Verifique as configurações.',
                'config': {
                    'host': EMAIL_HOST,
                    'port': EMAIL_PORT,
                    'user_configured': bool(EMAIL_USER),
                    'password_configured': bool(EMAIL_PASSWORD)
                }
            }), 500
            
    except Exception as e:
        return jsonify({
            'success': False,
            'message': f'Erro no teste de email: {str(e)}'
        }), 500

if __name__ == '__main__':
    print("🚀 Iniciando servidor do Capturador de Leads...")
    print(f"📧 Servidor de email: {EMAIL_HOST}:{EMAIL_PORT}")
    print(f"👤 Usuário configurado: {EMAIL_USER if EMAIL_USER else 'NÃO CONFIGURADO'}")
    print(f"🔒 Senha configurada: {'SIM' if EMAIL_PASSWORD else 'NÃO'}")
    print(f"📬 Destinatário: {EMAIL_DESTINATARIO}")
    print("\n" + "="*50)
    print("IMPORTANTE: Configure as variáveis de ambiente:")
    print("export EMAIL_USER='seu-email@seudominio.com.br'")
    print("export EMAIL_PASSWORD='sua-senha-do-email'")
    print("="*50 + "\n")
    
    # Iniciar servidor
    app.run(host='0.0.0.0', port=5000, debug=True)

