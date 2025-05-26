from flask import Flask, request, jsonify
import sqlite3
from datetime import datetime, timedelta
from flask_cors import CORS
import logging
import re
import os

app = Flask(__name__)
CORS(app)

# Mapeamento dos bancos de dados
DB_PATHS = {
    "default": "keys.db",
    "enhanced": "keys_enhanced.db"
}

# Configura logging
logging.basicConfig(level=logging.DEBUG)

# Inicializa os bancos e cria a tabela se não existirem
def init_db():
    for db_path in DB_PATHS.values():
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS access_keys (
                key TEXT PRIMARY KEY,
                expires_at TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        """)
        conn.commit()
        conn.close()

# Retorna o caminho do banco com base no parâmetro ?db=
def get_db_path():
    tipo = request.args.get("db", "default")
    db_path = DB_PATHS.get(tipo)
    if not db_path:
        raise ValueError(f"Tipo de banco inválido: {tipo}")
    return db_path

@app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "API Flask ativa e funcionando."})

@app.route("/favicon.ico")
def favicon():
    return "", 204

@app.route("/validar", methods=["POST"])
def validar():
    data = request.get_json()
    chave = data.get("key")
    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    try:
        db_path = get_db_path()
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        cursor.execute("SELECT expires_at FROM access_keys WHERE key = ?", (chave,))
        row = cursor.fetchone()
        conn.close()
    except Exception as e:
        logging.error(f"Erro ao consultar banco: {str(e)}")
        return jsonify({"success": False, "error": str(e)}), 500

    if row:
        try:
            expira_str = re.sub(r'([+-]\d{2}:\d{2}|Z)$', '', row[0].strip())
            expira = datetime.fromisoformat(expira_str)
            current_utc = datetime.utcnow()
            if current_utc < expira:
                return jsonify({
                    "success": True,
                    "valid": True,
                    "validade": expira.isoformat() + "Z"
                })
            else:
                return jsonify({"success": True, "valid": False, "error": "Chave expirada."})
        except ValueError as ve:
            logging.error(f"Erro ao parsear data: {str(ve)}, valor: {expira_str}")
            return jsonify({"success": False, "error": "Formato de data inválido."}), 500

    return jsonify({"success": False, "error": "Chave inválida."}), 404

@app.route("/listar", methods=["GET"])
def listar():
    try:
        db_path = get_db_path()
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        cursor.execute("SELECT key, expires_at, created_at FROM access_keys")
        rows = cursor.fetchall()
        conn.close()
        return jsonify([{
            "key": r[0],
            "expires_at": re.sub(r'([+-]\d{2}:\d{2}|Z)$', '', r[1].strip()) + "Z",
            "created_at": re.sub(r'([+-]\d{2}:\d{2}|Z)$', '', r[2].strip()) + "Z"
        } for r in rows])
    except Exception as e:
        logging.error(f"Erro ao listar chaves: {str(e)}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/adicionar", methods=["POST"])
def adicionar():
    data = request.get_json()
    chave = data.get("key")
    dias_validade = data.get("dias", 30)

    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    created_at = datetime.utcnow()
    expires_at = created_at + timedelta(days=int(dias_validade))

    try:
        db_path = get_db_path()
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        cursor.execute("INSERT INTO access_keys (key, expires_at, created_at) VALUES (?, ?, ?)",
                       (chave, expires_at.isoformat(), created_at.isoformat()))
        conn.commit()
        conn.close()
        return jsonify({"success": True, "message": "Chave adicionada com sucesso."})
    except sqlite3.IntegrityError:
        return jsonify({"success": False, "error": "Chave já existe."}), 409
    except Exception as e:
        logging.error(f"Erro ao adicionar chave: {str(e)}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/remover", methods=["DELETE"])
def remover():
    data = request.get_json()
    chave = data.get("key")
    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    try:
        db_path = get_db_path()
        conn = sqlite3.connect(db_path)
        cursor = conn.cursor()
        cursor.execute("DELETE FROM access_keys WHERE key = ?", (chave,))
        conn.commit()
        conn.close()
        return jsonify({"success": True, "message": "Chave removida com sucesso."})
    except Exception as e:
        logging.error(f"Erro ao remover chave: {str(e)}")
        return jsonify({"success": False, "error": str(e)}), 500

if __name__ == "__main__":
    init_db()
    app.run(debug=True, host="0.0.0.0", port=5000)
