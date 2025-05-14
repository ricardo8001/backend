import asyncio
from telethon import TelegramClient, events
from telethon.tl.types import Chat, Channel

# Suas credenciais
api_id = 27082494
api_hash = '212e3977ead802faf3d92e0b239c8f1e'
source_group_id = -1002422757085

client = TelegramClient('forwarder_with_delay', api_id, api_hash)
target_entities = []

# Buscar grupos/canais válidos
async def get_target_groups():
    print("\n🔍 Buscando grupos e canais onde é possível encaminhar...")
    groups = []
    async for dialog in client.iter_dialogs():
        entity = dialog.entity

        if entity.id == source_group_id or dialog.id == source_group_id:
            continue

        if isinstance(entity, (Chat, Channel)) and (dialog.is_group or dialog.is_channel):
            if isinstance(entity, Channel) and entity.broadcast:
                print(f"⛔ Ignorado (canal de transmissão): {dialog.name}")
                continue
            try:
                input_entity = await client.get_input_entity(entity)
                print(f"✅ Adicionado: {dialog.name} (ID: {entity.id})")
                groups.append(input_entity)
            except Exception as e:
                print(f"⚠️ Erro ao adicionar {dialog.name}: {e}")
    print(f"\n🔢 Total de destinos válidos: {len(groups)}\n")
    return groups

# Encaminhar mensagem com delay entre os envios
@client.on(events.NewMessage(chats=source_group_id))
async def forward_handler(event):
    print("📥 Nova mensagem recebida. Iniciando encaminhamento...")
    for entity in target_entities:
        try:
            await event.message.forward_to(entity)
            print(f"✅ Encaminhado para: {entity}")
            await asyncio.sleep(1)  # ⏳ Espera 1 segundos antes de enviar para o próximo
        except Exception as e:
            print(f"❌ Erro ao encaminhar para {entity}: {e}")

# Função principal
async def main():
    await client.start()
    print("🚀 Conectado com sucesso.")
    global target_entities
    target_entities = await get_target_groups()
    print("🟢 Escutando mensagens do grupo de origem...")
    await client.run_until_disconnected()

# Executa
with client:
    client.loop.run_until_complete(main())
