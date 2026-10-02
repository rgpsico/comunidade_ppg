<template>
  <div class="pelada fade-up">
    <button class="back-btn" @click="ui.goTo('futebol')">← Peladas</button>

    <div v-if="futebol.loadingAtual && !p" class="loading-msg">Carregando pelada...</div>
    <div v-else-if="futebol.notFound || !p" class="empty-state">
      <div class="empty-icon">⚽</div>
      <p>Pelada não encontrada.</p>
      <button class="btn-secondary" @click="ui.goTo('futebol')">Ver todas as peladas</button>
    </div>

    <template v-else>
      <header class="head">
        <div class="eyebrow">⚽ {{ TIPO_LABEL[p.tipo] }} · <span class="live"><i class="live-dot"></i>ao vivo</span></div>
        <h1 class="title display">{{ p.nome }}</h1>
        <div class="chips">
          <span class="chip">{{ formato }}</span>
          <span class="chip">⏱ {{ p.tempo_jogo_min }} min</span>
          <span v-if="p.quando" class="chip">🗓 {{ p.quando }}</span>
          <span v-if="p.local" class="chip">📍 {{ p.local }}</span>
          <span class="chip">👥 {{ p.total_jogadores }} na lista</span>
        </div>
      </header>

      <div v-if="msg" class="toast" :class="{ erro: msgErro }" role="status">{{ msg }}</div>

      <!-- Lista vazia -->
      <div v-if="p.times.length === 0" class="empty-state">
        <div class="empty-icon">🙋</div>
        <p>Ninguém na lista ainda. Seja o primeiro!</p>
      </div>

      <!-- Em quadra -->
      <section v-if="emQuadra.length" class="bloco">
        <h2 class="bloco-title">Em quadra</h2>
        <div class="times">
          <template v-for="(t, i) in emQuadra" :key="t.indice">
            <TeamCard :t="t" />
            <div v-if="i === 0 && emQuadra.length > 1" class="vs">VS</div>
          </template>
        </div>
      </section>

      <!-- Fila -->
      <section v-if="naFila.length" class="bloco">
        <h2 class="bloco-title">Na fila</h2>
        <div class="times">
          <TeamCard v-for="t in naFila" :key="t.indice" :t="t" />
        </div>
      </section>

      <!-- Painel do organizador -->
      <section v-if="souOrganizador" class="bloco org">
        <button class="org-toggle" @click="orgAberto = !orgAberto">
          🛠 Painel do organizador <span>{{ orgAberto ? '▲' : '▼' }}</span>
        </button>
        <div v-if="orgAberto" class="org-body">
          <div v-if="emQuadra.length" class="org-group">
            <div class="org-label">Quem perdeu? O time vai pro fim da fila.</div>
            <div class="org-row">
              <button
                v-for="t in emQuadra"
                :key="t.indice"
                class="btn-org"
                :disabled="ocupado"
                @click="rodizio(t.indice)"
              >
                <i class="swatch" :style="{ background: cor(t.indice) }"></i> Time {{ t.indice + 1 }} perdeu
              </button>
            </div>
          </div>

          <form class="org-group" @submit.prevent="addConvidado">
            <div class="org-label">Adicionar jogador sem conta</div>
            <div class="org-row">
              <input v-model="convidadoNome" class="input" maxlength="80" placeholder="Nome do jogador" />
              <label v-if="p.goleiros_por_time > 0" class="check">
                <input v-model="convidadoGoleiro" type="checkbox" /> Goleiro
              </label>
              <button class="btn-org" :disabled="ocupado || !convidadoNome.trim()">Adicionar</button>
            </div>
          </form>

          <div class="org-group">
            <div class="org-label">Zona de risco</div>
            <div class="org-row">
              <button class="btn-danger" :disabled="ocupado || p.total_jogadores === 0" @click="zerar">Zerar lista</button>
              <button class="btn-danger" :disabled="ocupado" @click="excluir">Excluir pelada</button>
            </div>
          </div>
        </div>
      </section>

      <div class="action-spacer"></div>

      <!-- Barra de ação fixa (teleportada: o fade-up do container quebraria o position: fixed) -->
      <Teleport to="body">
      <div class="action-bar">
        <template v-if="!auth.isAuthenticated">
          <button class="btn-main" @click="ui.goTo('login')">Entrar para participar</button>
        </template>
        <template v-else-if="!euNaLista">
          <label v-if="p.goleiros_por_time > 0" class="check big">
            <input v-model="entrarGoleiro" type="checkbox" /> 🧤 Sou goleiro
          </label>
          <button class="btn-main" :disabled="ocupado" @click="entrar">Entrar na lista</button>
        </template>
        <template v-else>
          <button class="btn-ghost" :disabled="ocupado" @click="sair">Sair da lista</button>
          <button class="btn-main" :disabled="ocupado" @click="voltarFila">Perdi, voltar pra fila</button>
        </template>
      </div>
      </Teleport>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed, defineComponent, h, onMounted, ref, watch, type PropType } from 'vue'
import { useIntervalFn } from '@vueuse/core'
import { useUiStore } from '../../../stores/ui'
import { useAuthStore } from '../../../stores/auth'
import { useFutebolStore } from '../../../stores/futebol'
import type { PeladaJogador, PeladaTime, PeladaTipo } from '../../../types'

const ui = useUiStore()
const auth = useAuthStore()
const futebol = useFutebolStore()

const TIPO_LABEL: Record<PeladaTipo, string> = { quadra: 'Quadra', society: 'Society', campo: 'Campo', racha: 'Racha' }
const CORES = ['#3B82F6', '#EF4444', '#2BD96B', '#FFD23F', '#A855F7', '#EC4899', '#14B8A6', '#F97316']
const cor = (indice: number) => CORES[indice % CORES.length]

const p = computed(() => futebol.atual)
const id = computed(() => ui.selectedPeladaId)

const emQuadra = computed(() => p.value?.times.filter(t => t.status === 'quadra') ?? [])
const naFila = computed(() => p.value?.times.filter(t => t.status === 'fila') ?? [])
const formato = computed(() => {
  if (!p.value) return ''
  return p.value.goleiros_por_time > 0
    ? `${p.value.jogadores_linha} na linha + gol`
    : `${p.value.jogadores_linha} na linha`
})

const todosJogadores = computed<PeladaJogador[]>(() =>
  (p.value?.times ?? []).flatMap(t => [...t.linha, ...(t.goleiro ? [t.goleiro] : [])])
)
const euNaLista = computed(() => !!auth.user && todosJogadores.value.some(j => j.user_id === auth.user!.id))
const souOrganizador = computed(() => {
  const u = auth.user
  if (!u || !p.value) return false
  return u.id === p.value.organizador?.id || u.roles?.some(r => ['super_admin', 'admin', 'moderador'].includes(r.name))
})

const ocupado = ref(false)
const msg = ref('')
const msgErro = ref(false)
const orgAberto = ref(false)
const entrarGoleiro = ref(false)
const convidadoNome = ref('')
const convidadoGoleiro = ref(false)

let msgTimer: ReturnType<typeof setTimeout> | undefined
function aviso(texto: string, erro = false) {
  msg.value = texto
  msgErro.value = erro
  clearTimeout(msgTimer)
  msgTimer = setTimeout(() => (msg.value = ''), 3500)
}

async function executar(fn: () => Promise<void>, ok?: string) {
  if (ocupado.value) return
  ocupado.value = true
  try {
    await fn()
    if (ok) aviso(ok)
  } catch (e: unknown) {
    aviso(e instanceof Error ? e.message : 'Algo deu errado.', true)
  } finally {
    ocupado.value = false
  }
}

const entrar = () => executar(() => futebol.entrar(p.value!.id, entrarGoleiro.value), 'Você entrou na lista!')
const sair = () => executar(() => futebol.sair(p.value!.id), 'Você saiu da lista.')
const voltarFila = () => executar(() => futebol.voltarFila(p.value!.id), 'Você voltou pro fim da fila.')
const rodizio = (indice: number) =>
  executar(() => futebol.rodizio(p.value!.id, indice), `Time ${indice + 1} foi pro fim da fila.`)

const addConvidado = () =>
  executar(async () => {
    await futebol.addConvidado(p.value!.id, convidadoNome.value.trim(), convidadoGoleiro.value)
    convidadoNome.value = ''
    convidadoGoleiro.value = false
  }, 'Jogador adicionado.')

const remover = (j: PeladaJogador) =>
  executar(() => futebol.removerJogador(p.value!.id, j.id), `${j.nome} saiu da lista.`)

const zerar = () => {
  if (!window.confirm('Zerar a lista inteira? Todo mundo sai da fila.')) return
  return executar(() => futebol.zerar(p.value!.id), 'Lista zerada.')
}

const excluir = () => {
  if (!window.confirm('Excluir esta pelada de vez?')) return
  return executar(async () => {
    await futebol.excluir(p.value!.id)
    ui.goTo('futebol')
  })
}

// Atualização automática a cada 5s (pausa com a aba escondida)
const { resume } = useIntervalFn(() => {
  if (document.hidden || ocupado.value || id.value == null) return
  futebol.fetchPelada(id.value, true)
}, 5000, { immediate: false })

onMounted(() => {
  if (id.value != null) futebol.fetchPelada(id.value)
  resume()
})
watch(id, v => { if (v != null) futebol.fetchPelada(v) })

const TeamCard = defineComponent({
  props: { t: { type: Object as PropType<PeladaTime>, required: true } },
  setup(props) {
    return () => {
      const t = props.t
      const status = t.status === 'quadra' ? 'Em quadra' : `${t.ordem_fila}º na fila`
      const linhaItens = t.linha.map(j => jogadorLi(j))
      for (let i = 0; i < t.vagas_linha; i++) {
        linhaItens.push(h('li', { class: 'slot vazio' }, 'vaga livre'))
      }
      if (p.value && p.value.goleiros_por_time > 0) {
        linhaItens.unshift(
          t.goleiro
            ? jogadorLi(t.goleiro)
            : h('li', { class: 'slot vazio gol' }, '🧤 vaga de goleiro')
        )
      }
      return h('article', { class: 'team', style: { '--tc': cor(t.indice) } }, [
        h('header', { class: 'team-head' }, [
          h('strong', `Time ${t.indice + 1}`),
          h('span', { class: ['team-status', t.status] }, status),
        ]),
        h('ol', { class: 'team-list' }, linhaItens),
      ])
    }
  },
})

function jogadorLi(j: PeladaJogador) {
  const souEu = !!auth.user && j.user_id === auth.user.id
  return h('li', { class: ['slot', { eu: souEu }], key: j.id }, [
    h('span', { class: 'slot-nome' }, [
      j.goleiro ? '🧤 ' : '',
      j.nome,
      souEu ? h('em', ' (você)') : null,
      j.convidado ? h('small', ' · convidado') : null,
    ]),
    souOrganizador.value
      ? h('button', { class: 'slot-x', title: `Remover ${j.nome}`, onClick: () => remover(j) }, '✕')
      : null,
  ])
}
</script>

<style scoped>
.pelada { padding: 32px 48px 0; max-width: 900px; margin: 0 auto; }

.back-btn { background: none; border: none; color: var(--muted); font-size: 14px; cursor: pointer; padding: 6px 0; margin-bottom: 12px; }
.back-btn:hover { color: var(--cream); }

.eyebrow {
  font-family: var(--mono);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--orange);
  margin-bottom: 8px;
}
.live { color: var(--green); }
.live-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: var(--green); margin-right: 5px; animation: pulse 1.6s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

.title { font-size: 30px; font-weight: 900; color: var(--cream); margin: 0 0 12px; }
.chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 20px; }
.chip { font-size: 12px; padding: 5px 11px; border-radius: 999px; background: var(--card); border: 1px solid var(--line); color: var(--muted); }

.toast { position: sticky; top: 8px; z-index: 5; padding: 10px 14px; border-radius: 12px; background: #2BD96B22; border: 1px solid var(--green); color: var(--cream); font-size: 14px; margin-bottom: 14px; }
.toast.erro { background: #EF444422; border-color: #EF4444; }

.bloco { margin-bottom: 26px; }
.bloco-title { font-family: var(--mono); font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); margin: 0 0 12px; }
.times { display: grid; grid-template-columns: 1fr; gap: 12px; }
.vs { text-align: center; font-weight: 900; color: var(--muted); letter-spacing: 0.2em; font-size: 13px; }

:deep(.team) {
  background: color-mix(in srgb, var(--tc) 13%, var(--card));
  border: 1px solid color-mix(in srgb, var(--tc) 40%, var(--line));
  border-left: 6px solid var(--tc);
  border-radius: 14px;
  padding: 14px 16px;
}
:deep(.team-head) { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; color: var(--cream); font-size: 16px; }
:deep(.team-status) { font-size: 11px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; padding: 4px 9px; border-radius: 999px; background: var(--bg); color: var(--muted); }
:deep(.team-status.quadra) { color: var(--green); border: 1px solid var(--green); }
:deep(.team-list) { list-style: none; counter-reset: j; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
:deep(.slot) {
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
  min-height: 44px; padding: 8px 12px; border-radius: 10px;
  background: color-mix(in srgb, var(--bg) 70%, transparent);
  color: var(--cream); font-size: 15px;
}
:deep(.slot.eu) { outline: 2px solid var(--cream); }
:deep(.slot.vazio) { border: 1px dashed var(--line-strong); background: transparent; color: var(--muted); font-size: 13px; }
:deep(.slot-nome small), :deep(.slot-nome em) { color: var(--muted); font-style: normal; font-size: 12px; }
:deep(.slot-x) { min-width: 36px; min-height: 36px; background: none; border: 1px solid var(--line); border-radius: 8px; color: var(--muted); cursor: pointer; }
:deep(.slot-x:hover) { color: #EF4444; border-color: #EF4444; }

.org { border: 1px solid var(--line); border-radius: 14px; background: var(--card); }
.org-toggle { width: 100%; min-height: 48px; display: flex; justify-content: space-between; align-items: center; padding: 0 16px; background: none; border: none; color: var(--cream); font-weight: 700; font-size: 14px; cursor: pointer; }
.org-body { padding: 4px 16px 16px; display: flex; flex-direction: column; gap: 16px; }
.org-label { font-size: 12px; color: var(--muted); margin-bottom: 8px; }
.org-row { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.swatch { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; }
.input { flex: 1; min-width: 160px; min-height: 44px; background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: 0 12px; color: var(--cream); font-size: 14px; }
.input:focus { outline: none; border-color: var(--orange); }
.btn-org, .btn-danger, .btn-ghost {
  min-height: 44px; padding: 0 16px; border-radius: 999px; font-weight: 600; font-size: 14px; cursor: pointer;
  background: var(--bg); border: 1px solid var(--line-strong); color: var(--cream);
}
.btn-danger { border-color: #EF4444; color: #EF4444; }
.btn-org:disabled, .btn-danger:disabled, .btn-ghost:disabled, .btn-main:disabled { opacity: 0.45; cursor: not-allowed; }

.check { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: var(--cream); min-height: 44px; }
.check input { width: 18px; height: 18px; }

.action-spacer { height: 96px; }
.action-bar {
  position: fixed; left: var(--sb-w); right: 0; bottom: 0; z-index: 40;
  display: flex; gap: 10px; align-items: center; justify-content: center;
  padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
  background: color-mix(in srgb, var(--bg-2) 94%, transparent);
  border-top: 1px solid var(--line);
  backdrop-filter: blur(8px);
}
.btn-main { flex: 1; max-width: 360px; min-height: 52px; background: var(--orange); color: #fff; border: none; border-radius: 999px; font-weight: 800; font-size: 16px; cursor: pointer; }
.btn-main:hover:not(:disabled) { filter: brightness(1.1); }
.btn-secondary { min-height: 44px; padding: 0 22px; background: transparent; color: var(--muted); border: 1px solid var(--line); border-radius: 999px; cursor: pointer; }

.loading-msg, .empty-state { text-align: center; padding: 50px 20px; color: var(--muted); }
.empty-icon { font-size: 44px; margin-bottom: 10px; }

@media (min-width: 769px) {
  .times { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
  .pelada { padding: 16px 16px 0; }
  .title { font-size: 24px; }
  .action-bar { left: 0; bottom: var(--bn-h); }
  .action-spacer { height: calc(96px + var(--bn-h)); }
}
</style>
