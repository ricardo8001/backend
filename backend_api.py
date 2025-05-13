from flask import Flask, request, jsonify
import sqlite3
from datetime import datetime
from flask_cors import CORS

app = Flask(__name__)
CORS(app)
DB_PATH = "keys.db"

# Initialize database
def init_db():
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS access_keys (
            key TEXT PRIMARY KEY,
            client_name TEXT,
            expires_at TEXT NOT NULL,
            created_at TEXT NOT NULL,
            status TEXT DEFAULT 'Desconectado'
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
   Eror: cannot unpack non-iterable NoneType object
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
        return jsonify({"success": False, "error": str(e)}), 500
    if row:
        try:
            expira = datetime.fromisoformat(row[0])
            if datetime.now() < expira:
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
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("SELECT key, client_name, expires_at, created_at, status FROM access_keys")
        rows = cursor.fetchall()
        conn.close()
        return jsonify([{
            "key": r[0],
            "client_name": r[1] or "Desconhecido",
            "expires_at": r[2],
            "created_at": r[3],
            "status": r[4]
        } for r in rows])
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/adicionar", methods=["POST"])
def adicionar():
    data = request.get_json()
    chave = data.get("key")
    client_name = data.get("client_name")
    data_validade = data.get("data_validade")  # Format: dd/mm/yyyy
    if not chave or not client_name or not data_validade:
        return jsonify({"success": False, "error": "Chave, nome do cliente ou data de validade não fornecidos."}), 400
    try:
        data_validade_dt = datetime.strptime(data_validade, "%d/%m/%Y")
        data_validade_dt = data_validade_dt.replace(hour=23, minute=59, second=59)
        if data_validade_dt <= datetime.now():
            return jsonify({"success": False, "error": "A validade deve ser uma data futura."}), 400
        created_at = datetime.now()
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO access_keys (key, client_name, expires_at, created_at, status) VALUES (?, ?, ?, ?, ?)",
            (chave, client_name, data_validade_dt.isoformat(), created_at.isoformat(), "Desconectado")
        )
        conn.commit()
        conn.close()
        return jsonify({"success": True, "message": "Chave adicionada com sucesso."})
    except sqlite3.IntegrityError:
        return jsonify({"success": False, "error": "Chave já existe."}), 409
    except ValueError:
        return jsonify({"success": False, "error": "Formato de data inválido."}), 400
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500

@app.route("/editar", methods=["POST"])
def editar():
    data = request.get_json()
    chave = data.get("key")
    data_validade = data.get("data_validade")  # Format: dd/mm/yyyy
    if not chave or not data_validade:
        return jsonify({"success": False, "error": "Chave ou data de validade não fornecidos."}), 400
    try:
        data_validade_dt = datetime.strptime(data_validade, "%d/%m/%Y")
        data_validade_dt = data_validade_dt.replace(hour=23, minute=59, second=59)
        if data_validade_dt <= datetime.now():
            return jsonify({"success": False, "error": "A validade deve ser uma data futura."}), 400
        conn = sqlite3.connect(DB_PATH)
        cursor = conn.cursor()
        cursor.execute("UPDATE access_keys SET expires_at = ? WHERE key = ?", (data_validade_dt.isoformat(), chave))
        if cursor.rowcount == 0:
            return jsonify({"success": False, "error": "Chave não encontrada."}), 404
        conn.commit()
        conn.close()
        return jsonify({"success": True, "message": "Validade atualizada com sucesso."})
    except ValueError:
        return jsonify({"success": False, "error": "Formato de data inválido."}), 400
    except Exception as e:
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
        if cursor.rowcount == 0:
            return jsonify({"success": False, "error": "Chave não encontrada."}), 404
        conn.commit()
        conn.close()
        return jsonify({"success": True, "message": "Chave removida com sucesso."})
    except Exception as e:
        return jsonify({"success": False, "error": str(e)}), 500

if __name__ == "__main__":
    init_db()
    app.run(debug=False, host="0.0.0.0", port=5000)
