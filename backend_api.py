import customtkinter as ctk
from tkinter import messagebox, Toplevel
import requests
import secrets
from datetime import datetime
import pyperclip
from tkcalendar import Calendar
import threading
import time
import random
from flask import Flask, request, jsonify
import sqlite3
from flask_cors import CORS

# Flask app setup
flask_app = Flask(__name__)
CORS(flask_app)
DB_PATH = "keys.db"
API_URL = "http://localhost:5000"

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

# Flask routes
@flask_app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "API Flask ativa e funcionando."})

@flask_app.route("/favicon.ico")
def favicon():
    return "", 204

@flask_app.route("/validar", methods=["POST"])
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

@flask_app.route("/listar", methods=["GET"])
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

@flask_app.route("/adicionar", methods=["POST"])
def adicionar():
    data = request.get_json()
    chave = data.get("key")
    client_name = data.get("client_name")
    data_validade = data.get("data_validade")
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

@flask_app.route("/editar", methods=["POST"])
def editar():
    data = request.get_json()
    chave = data.get("key")
    data_validade = data.get("data_validade")
    if not chave or not data_validade:
        return jsonify({"success": False, "error": "Chave ou data de validade não fornecidos."}), 400
    try:
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

@flask_app.route("/remover", methods=["DELETE"])
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

# GUI setup
ctk.set_appearance_mode("light")
ctk.set_default_color_theme("blue")

# Global variable for selected key
chave_selecionada = None

# Simulate real-time monitoring
def simulate_real_time_monitoring():
    while True:
        time.sleep(5)
        try:
            response = requests.get(f"{API_URL}/listar")
            if response.status_code == 200:
                chaves = response.json()
                for chave in chaves:
                    chave["status"] = random.choice(["Conectado", "Desconectado"])
                app.after(0, lambda: atualizar_lista(chaves))
        except requests.RequestException:
            pass

# Generate key
def gerar_chave():
    janela_calendario = Toplevel()
    janela_calendario.title("Selecionar Validade")
    janela_calendario.geometry("300x400")
    janela_calendario.resizable(False, False)
    frame = ctk.CTkFrame(janela_calendario)
    frame.pack(padx=10, pady=10, fill="both", expand=True)
    ctk.CTkLabel(frame, text="Nome do Cliente:").pack(anchor="w", pady=5)
    cliente_entry = ctk.CTkEntry(frame, width=200)
    cliente_entry.pack(pady=5, fill="x")
    calendario = Calendar(frame, selectmode="day", date_pattern="dd/mm/yyyy")
    calendario.pack(pady=10)
    botoes_frame = ctk.CTkFrame(frame)
    botoes_frame.pack(pady=5, fill="x")
    def confirmar():
        try:
            cliente = cliente_entry.get().strip()
            if not cliente:
                messagebox.showerror("Erro", "Insira o nome do cliente.")
                return
            data_validade = calendario.get_date()
            data_validade_dt = datetime.strptime(data_validade, "%d/%m/%Y")
            if data_validade_dt.date() < datetime.now().date():
                messagebox.showerror("Erro", "A validade deve ser uma data futura.")
                return
            chave = secrets.token_hex(8)
            response = requests.post(f"{API_URL}/adicionar", json={
                "key": chave,
                "client_name": cliente,
                "data_validade": data_validade
            })
            if response.status_code == 200:
                atualizar_lista()
                messagebox.showinfo("Sucesso", f"Chave gerada: {chave} para {cliente}")
                janela_calendario.destroy()
            else:
                messagebox.showerror("Erro", response.json().get("error", "Erro ao adicionar chave."))
        except requests.RequestException:
            messagebox.showerror("Erro", "Não foi possível conectar à API.")
    def cancelar():
        janela_calendario.destroy()
    ctk.CTkButton(botoes_frame, text="Confirmar", fg_color="green", text_color="white", command=confirmar).pack(side="left", padx=5)
    ctk.CTkButton(botoes_frame, text="Cancelar", fg_color="red", text_color="white", command=cancelar).pack(side="right", padx=5)

# Delete key
def excluir_chave():
    global chave_selecionada
    if chave_selecionada:
        if messagebox.askyesno("Confirmar", f"Deseja excluir a chave {chave_selecionada}?"):
            try:
                response = requests.delete(f"{API_URL}/remover", json={"key": chave_selecionada})
                if response.status_code == 200:
                    chave_selecionada = None
                    atualizar_lista()
                    messagebox.showinfo("Sucesso", "Chave excluída.")
                else:
                    messagebox.showerror("Erro", response.json().get("error", "Erro ao excluir chave."))
            except requests.RequestException:
                messagebox.showerror("Erro", "Não foi possível conectar à API.")

# Copy key
def copiar_chave():
    global chave_selecionada
    if chave_selecionada:
        pyperclip.copy(chave_selecionada)
        messagebox.showinfo("Copiado", f"Chave copiada: {chave_selecionada}")

# Update key list
def atualizar_lista(chaves=None):
    global chave_selecionada
    for widget in lista_chaves.winfo_children():
        widget.destroy()
    if chaves is None:
        try:
            response = requests.get(f"{API_URL}/listar")
            if response.status_code != 200:
                return
            chaves = response.json()
        except requests.RequestException:
            return
    chaves_ativas = 0
    for r in chaves:
        try:
            expires_at_dt = datetime.fromisoformat(r["expires_at"])
            if expires_at_dt >= datetime.now():
                chaves_ativas += 1
        except ValueError:
            continue
    total_label.configure(text=f"Chaves Ativas: {chaves_ativas}")
    for r in chaves:
        chave = r["key"]
        cliente = r["client_name"]
        expires_at = r["expires_at"]
        status = r["status"]
        try:
            expires_at_dt = datetime.fromisoformat(expires_at)
            expires_at_str = expires_at_dt.strftime("%d/%m/%Y")
            is_expired = expires_at_dt < datetime.now()
            text_color = "red" if is_expired else "green"
        except ValueError:
            expires_at_str = expires_at
            text_color = "red"
        entry_frame = ctk.CTkFrame(lista_chaves, fg_color="#FFFFFF", corner_radius=5)
        entry_frame.pack(fill="x", padx=5, pady=2)
        def selecionar_chave(chave=chave):
            global chave_selecionada
            chave_selecionada = chave
            for widget in lista_chaves.winfo_children():
                if hasattr(widget, "winfo_children") and len(widget.winfo_children()) > 0:
                    widget.configure(fg_color="#D3D3D3" if widget.winfo_children()[0].cget("text") == chave else "#FFFFFF")
        ctk.CTkLabel(entry_frame, text=chave, font=("Arial", 12), text_color=text_color, width=150).pack(side="left", padx=5)
        ctk.CTkLabel(entry_frame, text=cliente, font=("Arial", 12, "bold"), text_color=text_color, width=150).pack(side="left", padx=5)
        ctk.CTkLabel(entry_frame, text=f"Expira: {expires_at_str}", font=("Arial", 12), text_color=text_color, width=200).pack(side="left", padx=5)
        ctk.CTkLabel(entry_frame, text=f"Status: {status}", font=("Arial", 12), text_color="green" if status == "Conectado" else "#2B2D42", width=100).pack(side="left", padx=5)
        def copiar_chave_local():
            pyperclip.copy(chave)
            messagebox.showinfo("Copiado", f"Chave copiada: {chave}")
        ctk.CTkButton(entry_frame, text="Copiar", fg_color="#2B2D42", text_color="white", font=("Arial", 10), width=70, height=20, command=copiar_chave_local).pack(side="left", padx=5)
        entry_frame.bind("<Button-1>", lambda e, c=chave: selecionar_chave(c))
        for child in entry_frame.winfo_children():
            child.bind("<Button-1>", lambda e, c=chave: selecionar_chave(c))

# Edit expiration
def editar_validade():
    global chave_selecionada
    if chave_selecionada:
        janela_calendario = Toplevel()
        janela_calendario.title("Editar Validade")
        janela_calendario.geometry("300x300")
        janela_calendario.resizable(False, False)
        frame = ctk.CTkFrame(janela_calendario)
        frame.pack(padx=10, pady=10, fill="both", expand=True)
        calendario = Calendar(frame, selectmode="day", date_pattern="dd/mm/yyyy")
        calendario.pack(pady=10)
        botoes_frame = ctk.CTkFrame(frame)
        botoes_frame.pack(pady=5, fill="x")
        def confirmar():
            try:
                data_validade = calendario.get_date()
                data_validade_dt = datetime.strptime(data_validade, "%d/%m/%Y")
                if data_validade_dt.date() < datetime.now().date():
                    messagebox.showerror("Erro", "A nova validade deve ser uma data futura.")
                    return
                response = requests.post(f"{API_URL}/editar", json={
                    "key": chave_selecionada,
                    "data_validade": data_validade
                })
                if response.status_code == 200:
                    atualizar_lista()
                    messagebox.showinfo("Editado", f"Validade para {chave_selecionada} atualizada para {data_validade}.")
                    janela_calendario.destroy()
                else:
                    messagebox.showerror("Erro", response.json().get("error", "Erro ao editar validade."))
            except requests.RequestException:
                messagebox.showerror("Erro", "Não foi possível conectar à API.")
        def cancelar():
            janela_calendario.destroy()
        ctk.CTkButton(botoes_frame, text="Confirmar", fg_color="green", text_color="white", command=confirmar).pack(side="left", padx=5)
        ctk.CTkButton(botoes_frame, text="Cancelar", fg_color="red", text_color="white", command=cancelar).pack(side="right", padx=5)

# GUI setup
app = ctk.CTk()
app.title("Painel Admin - Gerador de Chaves")
app.geometry("800x500")
app.configure(fg_color="#F5F7FA")
frame = ctk.CTkFrame(app, fg_color="#F5F7FA")
frame.pack(padx=20, pady=20, fill="both", expand=True)
titulo = ctk.CTkLabel(frame, text="Gerenciador de Chaves de Acesso", font=("Arial", 24, "bold"), text_color="#2B2D42")
titulo.grid(row=0, column=0, columnspan=2, pady=(0, 20))
botoes_frame = ctk.CTkFrame(frame, fg_color="#F5F7FA")
botoes_frame.grid(row=1, column=0, padx=10, pady=10, sticky="nw")
ctk.CTkButton(botoes_frame, text="Gerar Chave", fg_color="#2B2D42", text_color="white", font=("Arial", 14), width=200, height=40, command=gerar_chave).pack(pady=10)
ctk.CTkButton(botoes_frame, text="Editar Validade", fg_color="#2B2D42", text_color="white", font=("Arial", 14), width=200, height=40, command=editar_validade).pack(pady=10)
ctk.CTkButton(botoes_frame, text="Copiar Chave", fg_color="#2B2D42", text_color="white", font=("Arial", 14), width=200, height=40, command=copiar_chave).pack(pady=10)
ctk.CTkButton(botoes_frame, text="Excluir Chave", fg_color="#D90429", text_color="white", font=("Arial", 14), width=200, height=40, command=excluir_chave).pack(pady=10)
lista_frame = ctk.CTkFrame(frame, fg_color="#FFFFFF", corner_radius=10)
lista_frame.grid(row=1, column=1, padx=10, pady=10, sticky="nsew")
header_frame = ctk.CTkFrame(lista_frame, fg_color="#000000")
header_frame.pack(fill="x", padx=10, pady=2)
ctk.CTkLabel(header_frame, text="Chave", font=("Arial", 12, "bold"), text_color="#FFFFFF", width=150).pack(side="left", padx=5)
ctk.CTkLabel(header_frame, text="Cliente", font=("Arial", 12, "bold"), text_color="#FFFFFF", width=150).pack(side="left", padx=5)
ctk.CTkLabel(header_frame, text="Expira", font=("Arial", 12, "bold"), text_color="#FFFFFF", width=200).pack(side="left", padx=5)
ctk.CTkLabel(header_frame, text="Status", font=("Arial", 12, "bold"), text_color="#FFFFFF", width=100).pack(side="left", padx=5)
ctk.CTkLabel(header_frame, text="Ações", font=("Arial", 12, "bold"), text_color="#FFFFFF", width=80).pack(side="left", padx=5)
total_label = ctk.CTkLabel(lista_frame, text="Chaves Ativas: 0", font=("Arial", 16, "bold"), text_color="#2B2D42")
total_label.pack(pady=(5, 5))
lista_chaves = ctk.CTkScrollableFrame(lista_frame, fg_color="#FFFFFF", corner_radius=5)
lista_chaves.pack(pady=5, padx=10, fill="both", expand=True)
frame.columnconfigure(1, weight=1)
frame.rowconfigure(1, weight=1)

# Main execution
if __name__ == "__main__":
    init_db()
    flask_thread = threading.Thread(target=lambda: flask_app.run(debug=True, host="0.0.0.0", port=5000, use_reloader=False))
    flask_thread.daemon = True
    flask_thread.start()
    monitor_thread = threading.Thread(target=simulate_real_time_monitoring, daemon=True)
    monitor_thread.start()
    app.after(100, atualizar_lista)
    app.mainloop()
