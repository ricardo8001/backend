from flask import Flask, request, jsonify
import psycopg2
from datetime import datetime, timedelta, timezone
from flask_cors import CORS
import os
from contextlib import contextmanager

app = Flask(__name__)
CORS(app)
DATABASE_URL = os.getenv("DATABASE_URL")

# Debug endpoint to check DATABASE_URL
@app.route("/debug", methods=["GET"])
def debug():
    return jsonify({
        "DATABASE_URL": DATABASE_URL if DATABASE_URL else "Not set",
        "success": True
    })

# Inicializa o banco e a tabela se não existirem
def init_db():
    with get_db_connection() as conn:
        cursor = conn.cursor()
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS access_keys (
                key TEXT PRIMARY KEY,
                expires_at TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        """)
        conn.commit()

@contextmanager
def get_db_connection():
    conn = psycopg2.connect(DATABASE_URL)
    try:
        yield conn
    finally:
        conn.close()

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
        with get_db_connection() as conn:
            cursor = conn.cursor()
            cursor.execute("SELECT expires_at FROM access_keys WHERE key = %s", (chave,))
            row = cursor.fetchone()
    except Exception as e:
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
        with get_db_connection() as conn:
            cursor = conn.cursor()
            cursor.execute("SELECT key, expires_at, created_at FROM access_keys")
            rows = cursor.fetchall()
        return jsonify([{"key": r[0], "expires_at": r[1], "created_at": r[2]} for r in rows])
    except Exception as e:
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
        with get_db_connection() as conn:
            cursor = conn.cursor()
            cursor.execute(
                "INSERT INTO access_keys (key, expires_at, created_at) VALUES (%s, %s, %s)",
                (chave, expires_at.isoformat(), created_at.isoformat())
            )
            conn.commit()
        return jsonify({"success": True, "message": "Chave adicionada com sucesso."})
    except psycopg2.IntegrityError:
        return jsonify({"success": False, "error": "Chave já existe."}), 409
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/remover", methods=["DELETE"])
def remover():
    data = request.get_json()
    chave = data.get("key")
    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    try:
        with get_db_connection() as conn:
            cursor = conn.cursor()
            cursor.execute("DELETE FROM access_keys WHERE key = %s", (chave,))
            conn.commit()
        return jsonify({"success": True, "message": "Chave removida com sucesso."})
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500

if __name__ == "__main__":
    init_db()
    app.run(debug=True, host="0.0.0.0", port=5000)
