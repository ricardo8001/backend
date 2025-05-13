from flask import Flask, request, jsonify
import sqlite3
from datetime import datetime, timedelta, timezone
from flask_cors import CORS
import os
import logging

app = Flask(__name__)
CORS(app)
DB_PATH = "/app/keys.db"  # Persistent path in Render

# Configure logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

# Debug endpoint to check database status
@app.route("/debug", methods=["GET"])
def debug():
    db_exists = os.path.exists(DB_PATH)
    try:
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name='access_keys'")
        table_exists = cursor.fetchone() is not None
        conn.close()
        return jsonify({
            "DB_PATH": DB_PATH,
            "db_exists": db_exists,
            "table_exists": table_exists,
            "success": True
        })
    except Exception as e:
        return jsonify({
            "DB_PATH": DB_PATH,
            "db_exists": db_exists,
            "error": str(e),
            "success": False
        })

# Inicializa o banco e a tabela se não existirem
def init_db():
    try:
        logger.debug(f"Inicializando banco de dados em {DB_PATH}")
        conn = sqlite3.connect(DB_PATH)
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
        logger.debug("Banco de dados inicializado com sucesso")
    except Exception as e:
        logger.error(f"Erro ao inicializar o banco de dados: {e}")
        raise

# Call init_db on startup
init_db()

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
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("SELECT expires_at FROM access_keys WHERE key = ?", (chave,))
        row = cursor.fetchone()
        conn.close()
    except Exception as e:
        logger.error(f"Erro ao acessar o banco de dados no endpoint /validar: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

    if row:
        try:
            expira = datetime.fromisoformat(row[0])
            # Compare only dates, ignoring time
            expira_date = expira.date()
            current_date = datetime.now(timezone.utc).date()
            if current_date <= expira_date:
                return jsonify({
                    "success": True,
                    "valid": True,
                    "validade": expira.isoformat()
                })
            else:
                return jsonify({"success": True, "valid": False, "error": "Chave expirada."})
        except ValueError:
            return jsonify({"success": False, "error": "Formato de data inválido."}), 500

    return jsonify({"success": False, "error": "Chave inválida."}), 404

@app.route("/listar", methods=["GET"])
def listar():
    try:
        logger.debug("Acessando banco de dados para listar chaves")
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("SELECT key, expires_at, created_at FROM access_keys")
        rows = cursor.fetchall()
        conn.close()
        logger.debug(f"Chaves encontradas: {len(rows)}")
        return jsonify([{"key": r[0], "expires_at": r[1], "created_at": r[2]} for r in rows])
    except Exception as e:
        logger.error(f"Erro ao listar chaves: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/adicionar", methods=["POST"])
def adicionar():
    data = request.get_json()
    chave = data.get("key")
    dias_validade = data.get("dias", 30)  # padrão: 30 dias

    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    created_at = datetime.now(timezone.utc)
    expires_at = created_at + timedelta(days=int(dias_validade))

    try:
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("INSERT INTO access_keys (key, expires_at, created_at) VALUES (?, ?, ?)",
                       (chave, expires_at.isoformat(), created_at.isoformat()))
        conn.commit()
        conn.close()
        logger.debug(f"Chave {chave} adicionada com sucesso")
        return jsonify({"success": True, "message": "Chave adicionada com sucesso."})
    except sqlite3.IntegrityError:
        logger.warning(f"Tentativa de adicionar chave duplicada: {chave}")
        return jsonify({"success": False, "error": "Chave já existe."}), 409
    except Exception as e:
        logger.error(f"Erro ao adicionar chave: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/remover", methods=["DELETE"])
def remover():
    data = request.get_json()
    chave = data.get("key")
    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    try:
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("DELETE FROM access_keys WHERE key = ?", (chave,))
        conn.commit()
        conn.close()
        logger.debug(f"Chave {chave} removida com sucesso")
        return jsonify({"success": True, "message": "Chave removida com sucesso."})
    except Exception as e:
        logger.error(f"Erro ao remover chave: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
