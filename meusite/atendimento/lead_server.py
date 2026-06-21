from flask import Flask, request, jsonify
from flask_cors import CORS
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

app = Flask(__name__)
CORS(app)

@app.route("/api/submit-lead", methods=["POST"])
def submit_lead():
    data = request.get_json()
    
    # Configurações do email
    sender_email = "perguntas@agenciacyborg.com.br"  # Substitua pelo seu email
    receiver_email = "perguntas@agenciacyborg.com.br"
    password = "?88?^zzmtY"  # Substitua pela sua senha de app do Gmail
    
    # Criar a mensagem
    message = MIMEMultipart("alternative")
    message["Subject"] = "Novo Lead Capturado!"
    message["From"] = sender_email
    message["To"] = receiver_email
    
    # Corpo do email em HTML
    html = f"""
    <html>
      <body>
        <h2>Novo Lead Recebido!</h2>
        <p><strong>Nome:</strong> {data.get("nome")}</p>
        <p><strong>Telefone:</strong> {data.get("telefone")}</p>
        <p><strong>Email:</strong> {data.get("email")}</p>
        <p><strong>Como podemos ajudar:</strong></p>
        <p>{data.get("ajuda")}</p>
      </body>
    </html>
    """
    
    # Adicionar corpo HTML à mensagem
    part = MIMEText(html, "html")
    message.attach(part)
    
    try:
        # Conectar ao servidor SMTP do Gmail
        server = smtplib.SMTP_SSL("smtp.hostinger.com", 465)
        server.login(sender_email, password)
        
        # Enviar o email
        server.sendmail(sender_email, receiver_email, message.as_string())
        server.quit()
        
        return jsonify({"success": True, "message": "Email enviado com sucesso!"})
    
    except Exception as e:
        print(f"Erro ao enviar email: {e}")
        return jsonify({"success": False, "message": "Erro ao enviar email."}), 500

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5001, debug=True)


