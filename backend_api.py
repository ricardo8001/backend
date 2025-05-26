from flask import Flask, request, jsonify
import sqlite3
from datetime import datetime, timedelta
from flask_cors import CORS
import logging
import re

app = Flask(__name__)
CORS(app)

DB_PATHS = ["keys.db", "keys_enhanced.db"]

logging.basicConfig(level=logging.DEBUG)

def init_db():
    for db_path in DB_PATHS:
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

    for db_path in DB_PATHS:
        try:
            conn = sqlite3.connect(db_path)
            cursor = conn.cursor()
            cursor.execute("SELECT expires_at FROM access_keys WHERE key = ?", (chave,))
            row = cursor.fetchone()
            conn.close()
            if row:
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
        except Exception as e:
            logging.error(f"Erro ao consultar {db_path}: {str(e)}")

    return jsonify({"success": False, "error": "Chave inválida."}), 404

@app.route("/listar", methods=["GET"])
def listar():
    todas = []
    for db_path in DB_PATHS:
        try:
            conn = sqlite3.connect(db_path)
            cursor = conn.cursor()
            cursor.execute("SELECT key, expires_at, created_at FROM access_keys")
            rows = cursor.fetchall()
            conn.close()
            for r in rows:
                todas.append({
                    "key": r[0],
                    "expires_at": re.sub(r'([+-]\d{2}:\d{2}|Z)$', '', r[1].strip()) + "Z",
                    "created_at": re.sub(r'([+-]\d{2}:\d{2}|Z)$', '', r[2].strip()) + "Z",
                    "source": db_path  # opcional: indica de qual banco veio
                })
        except Exception as e:
            logging.error(f"Erro ao listar chaves de {db_path}: {str(e)}")

    return jsonify(todas)

@app.route("/adicionar", methods=["POST"])
def adicionar():
    data = request.get_json()
    chave = data.get("key")
    dias_validade = data.get("dias", 30)

    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    created_at = datetime.utcnow()
    expires_at = created_at + timedelta(days=int(dias_validade))

    adicionadas = []
    ja_existem = []

    for db_path in DB_PATHS:
        try:
            conn = sqlite3.connect(db_path)
            cursor = conn.cursor()
            cursor.execute("INSERT INTO access_keys (key, expires_at, created_at) VALUES (?, ?, ?)",
                           (chave, expires_at.isoformat(), created_at.isoformat()))
            conn.commit()
            conn.close()
            adicionadas.append(db_path)
        except sqlite3.IntegrityError:
            ja_existem.append(db_path)
        except Exception as e:
            logging.error(f"Erro ao adicionar chave em {db_path}: {str(e)}")

    if adicionadas:
        return jsonify({
            "success": True,
            "message": "Chave adicionada com sucesso em: " + ", ".join(adicionadas),
            "ignorada_em": ja_existem
        })
    else:
        return jsonify({"success": False, "error": "Chave já existe em todos os bancos."}), 409

@app.route("/remover", methods=["DELETE"])
def remover():
    data = request.get_json()
    chave = data.get("key")
    if not chave:
        return jsonify({"success": False, "error": "Chave não fornecida."}), 400

    removidos = []
    for db_path in DB_PATHS:
        try:
            conn = sqlite3.connect(db_path)
            cursor = conn.cursor()
            cursor.execute("DELETE FROM access_keys WHERE key = ?", (chave,))
            if cursor.rowcount > 0:
                removidos.append(db_path)
            conn.commit()
            conn.close()
        except Exception as e:
            logging.error(f"Erro ao remover chave de {db_path}: {str(e)}")

    if removidos:
        return jsonify({"success": True, "message": f"Chave removida de: {', '.join(removidos)}"})
    else:
        return jsonify({"success": False, "error": "Chave não encontrada em nenhum banco."}), 404

if __name__ == "__main__":
    init_db()
    app.run(debug=True, host="0.0.0.0", port=5000)
