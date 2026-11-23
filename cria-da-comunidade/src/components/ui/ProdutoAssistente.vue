<template>
  <section class="assist" :style="{ '--cor': cor }">
    <button v-if="!aberto" class="assist-abrir" @click="abrir">
      <span class="assist-ico">💬</span>
      <span>
        <strong>Tire suas dúvidas</strong>
        <small>Garantia, entrega, cores, pagamento…</small>
      </span>
      <span class="assist-seta">›</span>
    </button>

    <div v-else class="assist-box">
      <header class="assist-head">
        <span class="assist-ico">💬</span>
        <div>
          <strong>Assistente da loja</strong>
          <small>Responde com as informações que a loja cadastrou</small>
        </div>
        <button class="assist-fechar" @click="aberto = false" aria-label="Fechar">✕</button>
      </header>

      <div ref="lista" class="assist-msgs">
        <div class="msg bot">Olá! Posso tirar suas dúvidas sobre <b>{{ produtoNome }}</b>. O que você quer saber?</div>

        <template v-for="(m, i) in mensagens" :key="i">
          <div class="msg" :class="m.role === 'user' ? 'eu' : 'bot'">{{ m.content }}</div>
          <div v-if="m.acoes?.length" class="msg-acoes">
            <template v-for="a in m.acoes" :key="a.tipo">
              <a v-if="a.tipo === 'whatsapp'" :href="a.url" target="_blank" rel="noopener" class="acao acao-wpp">
                Falar com a loja no WhatsApp
              </a>
              <button
                v-else-if="a.tipo === 'confirmar_interesse'"
                class="acao acao-interesse"
                :disabled="interesseEnviado"
                @click="$emit('interesse')"
              >
                {{ interesseEnviado ? '✓ Contato enviado para a loja' : 'Enviar meu contato para a loja' }}
              </button>
            </template>
          </div>
        </template>

        <div v-if="pensando" class="msg bot digitando"><span></span><span></span><span></span></div>
        <div v-if="erro" class="msg erro">{{ erro }}</div>
      </div>

      <div v-if="!mensagens.length" class="assist-chips">
        <button v-for="s in sugestoes" :key="s" class="chip" @click="enviar(s)">{{ s }}</button>
      </div>

      <form class="assist-form" @submit.prevent="enviar()">
        <input
          v-model="texto"
          class="assist-input"
          placeholder="Escreva sua pergunta…"
          maxlength="600"
          :disabled="pensando"
          enterkeyhint="send"
        />
        <button class="assist-enviar" :disabled="pensando || !texto.trim()" aria-label="Enviar">➤</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, nextTick, watch } from 'vue'
import { api } from '../../services/api'

type Acao = { tipo: 'whatsapp'; url: string } | { tipo: 'confirmar_interesse' }
type Msg = { role: 'user' | 'assistant'; content: string; acoes?: Acao[] }

const props = defineProps<{ produtoId: string; produtoNome: string; cor: string; interesseEnviado: boolean }>()
defineEmits<{ interesse: [] }>()

const sugestoes = ['Tem garantia?', 'Quanto tempo para entregar?', 'Tem outras cores?', 'Entrega no meu bairro?', 'Quais formas de pagamento?']
// O servidor aceita até 20 mensagens por conversa; manda só as últimas.
const MAX_HISTORICO = 16

const aberto    = ref(false)
const mensagens = ref<Msg[]>([])
const texto     = ref('')
const pensando  = ref(false)
const erro      = ref('')
const lista     = ref<HTMLElement | null>(null)

// Trocou de produto: começa conversa nova.
watch(() => props.produtoId, () => { mensagens.value = []; erro.value = ''; aberto.value = false })

function abrir() {
  aberto.value = true
}

async function rolar() {
  await nextTick()
  lista.value?.scrollTo({ top: lista.value.scrollHeight, behavior: 'smooth' })
}

async function enviar(pergunta?: string) {
  const conteudo = (pergunta ?? texto.value).trim()
  if (!conteudo || pensando.value) return

  erro.value = ''
  texto.value = ''
  mensagens.value.push({ role: 'user', content: conteudo })
  pensando.value = true
  rolar()

  try {
    const historico = mensagens.value.slice(-MAX_HISTORICO).map(m => ({ role: m.role, content: m.content }))
    const r = await api.post<{ resposta: string; acoes: Acao[] }>(`/produtos/${props.produtoId}/assistente`, { mensagens: historico })
    mensagens.value.push({ role: 'assistant', content: r.resposta || 'Não consegui responder isso. Quer falar com a loja?', acoes: r.acoes })
  } catch (e: any) {
    // Tira a pergunta que falhou do histórico para não confundir a próxima.
    mensagens.value.pop()
    texto.value = conteudo
    erro.value = e?.message?.includes('Too Many') ? 'Muitas perguntas seguidas. Espere um minuto e tente de novo.' : (e?.message || 'Não consegui responder agora. Tente de novo.')
  } finally {
    pensando.value = false
    rolar()
  }
}
</script>

<style scoped>
.assist { margin-top: 14px; }

.assist-abrir {
  width: 100%; display: flex; align-items: center; gap: 12px; text-align: left;
  padding: 14px 16px; border-radius: 14px; cursor: pointer;
  background: var(--card); border: 1px solid var(--line); color: var(--cream);
  transition: border-color .15s;
}
.assist-abrir:hover { border-color: var(--cor); }
.assist-abrir strong { display: block; font-size: 14px; }
.assist-abrir small { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }
.assist-ico { font-size: 22px; line-height: 1; }
.assist-seta { margin-left: auto; font-size: 22px; color: var(--muted); }

.assist-box { border: 1px solid var(--line); border-radius: 14px; background: var(--card); overflow: hidden; }
.assist-head { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-bottom: 1px solid var(--line); }
.assist-head strong { display: block; font-size: 14px; color: var(--cream); }
.assist-head small { display: block; font-size: 11px; color: var(--muted); }
.assist-fechar { margin-left: auto; background: none; border: none; color: var(--muted); font-size: 16px; cursor: pointer; padding: 4px; }

.assist-msgs { max-height: 360px; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 8px; }
.msg { max-width: 85%; padding: 9px 12px; border-radius: 14px; font-size: 14px; line-height: 1.45; white-space: pre-wrap; word-wrap: break-word; }
.msg.bot { align-self: flex-start; background: var(--bg); color: var(--cream); border-bottom-left-radius: 4px; }
.msg.eu { align-self: flex-end; background: var(--cor); color: #fff; border-bottom-right-radius: 4px; }
.msg.erro { align-self: stretch; max-width: none; background: #EF444418; color: #F87171; font-size: 13px; }

.msg-acoes { display: flex; flex-wrap: wrap; gap: 8px; }
.acao {
  display: inline-flex; align-items: center; padding: 9px 14px; border-radius: 99px;
  font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer; border: none;
}
.acao-wpp { background: #25D366; color: #fff; }
.acao-interesse { background: var(--cor); color: #fff; }
.acao-interesse:disabled { opacity: .75; cursor: default; }

.digitando { display: flex; gap: 4px; padding: 12px 14px; }
.digitando span { width: 6px; height: 6px; border-radius: 50%; background: var(--muted); animation: pulo 1s infinite; }
.digitando span:nth-child(2) { animation-delay: .15s; }
.digitando span:nth-child(3) { animation-delay: .3s; }
@keyframes pulo { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-3px); } }

.assist-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 0 14px 12px; }
.chip {
  padding: 7px 12px; border-radius: 99px; font-size: 12px; cursor: pointer;
  background: transparent; color: var(--cream); border: 1px solid var(--line);
}
.chip:hover { border-color: var(--cor); }

.assist-form { display: flex; gap: 8px; padding: 10px 12px; border-top: 1px solid var(--line); }
.assist-input {
  flex: 1; min-width: 0; padding: 10px 14px; border-radius: 99px; font-size: 15px;
  background: var(--bg); color: var(--cream); border: 1px solid var(--line); outline: none;
}
.assist-input:focus { border-color: var(--cor); }
.assist-enviar {
  width: 42px; height: 42px; flex-shrink: 0; border-radius: 50%; border: none; cursor: pointer;
  background: var(--cor); color: #fff; font-size: 16px;
}
.assist-enviar:disabled { opacity: .4; cursor: default; }
</style>
