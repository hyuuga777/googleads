FROM python:3.11-slim

WORKDIR /app

# Instala dependências do sistema
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Copia requisitos e instala dependências Python
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copia o restante do código
COPY . .

# Expõe a porta 5000 (onde a API roda)
EXPOSE 5000

# Variáveis de ambiente
ENV PORT=5000
ENV PYTHONUNBUFFERED=1

# Executa o servidor Flask
CMD ["python", "meusite/backend/api_server.py"]
