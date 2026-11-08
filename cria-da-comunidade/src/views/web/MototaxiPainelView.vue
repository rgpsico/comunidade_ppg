<template>
  <div class="mp">
    <header class="mp-top">
      <span class="mp-brand display">{{ data.configuracao.nome_plataforma || 'Comunidade PPG' }}</span>
    </header>

    <main class="mp-card" aria-live="polite">
      <p v-if="carregando" class="mp-sub">Carregando…</p>

      <section v-else-if="invalido" class="mp-center">
        <div class="mp-emoji" aria-hidden="true">🔒</div>
        <h1 class="mp-title">Link inválido</h1>
        <p class="mp-sub">Confira se você abriu o link inteiro que recebeu no cadastro.</p>
      </section>

      <section v-else class="mp-center">
        <p class="mp-ola">Olá, {{ primeiroNome }} 👋</p>

        <div class="mp-status" :class="{ on: online }">
          <span class="mp-dot" aria-hidden="true"></span>
          {{ online ? 'Você está ONLINE' : 'Você está offline' }}
        </div>

        <button class="mp-botao" :class="{ on: online }" type="button" :disabled="trocando" @click="alternar">
          {{ online ? 'Parar de rodar' : 'Estou rodando 🏍️' }}
        </button>

        <p v-if="online" class="mp-sub">
          Você aparece como <strong>online</strong> na lista da comunidade. <strong>Deixe esta tela aberta</strong>: se o celular
          bloquear ou você sair daqui, em até 2 minutos você volta a aparecer como offline.
        </p>
        <p v-else class="mp-sub">Toque quando começar a rodar. O pessoal da comunidade vê que você está disponível e te chama no WhatsApp.</p>

        <p v-if="online && !temLocal" class="mp-aviso">Sem localização: você aparece online mesmo assim. Se quiser compartilhar onde está, libere a localização no navegador.</p>
        <p v-if="!aprovado" class="mp-aviso">Seu cadastro ainda está em análise. Você só aparece na lista depois de aprovado.</p>
        <p v-if="erro" class="mp-erro">{{ erro }}</p>

        <button class="mp-link" type="button" @click="verLista">Ver a lista de mototáxi</button>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { api } from '../../services/api'
import { useDataStore } from '../../stores/data'

interface Resumo { nome: string; horario: string | null; aprovado: boolean; online: boolean }

const PING_MS = 30000

const data = useDataStore()

const token = window.location.pathname.match(/\/mototaxi\/painel\/([A-Za-z0-9]+)/)?.[1] ?? ''
const carregando = ref(true)
const invalido = ref(false)
const trocando = ref(false)
const nome = ref('')
const aprovado = ref(true)
const online = ref(false)
const temLocal = ref(false)
const erro = ref('')

let timer: number | undefined
let watchId: number | undefined
let posicao: { lat: number; lng: number } | null = null
let wakeLock: { release: () => Promise<void> } | null = null

const primeiroNome = computed(() => nome.value.trim().split(/\s+/)[0])

function aplicar(r: Resumo) {
  nome.value = r.nome
  aprovado.value = r.aprovado
  online.value = r.online
}

async function ping() {
  try {
    const r = await api.post<{ data: Resumo }>(`/mototaxi/painel/${token}/ping`, posicao ?? {})
    aplicar(r.data)
    erro.value = ''
  } catch {
    erro.value = 'Sem internet agora. Continuamos tentando.'
  }
}

function iniciarLocalizacao() {
  if (!('geolocation' in navigator) || watchId !== undefined) return
  watchId = navigator.geolocation.watchPosition(
    (p) => { posicao = { lat: p.coords.latitude, lng: p.coords.longitude }; temLocal.value = true },
    () => { posicao = null; temLocal.value = false },
    { enableHighAccuracy: false, maximumAge: 20000, timeout: 20000 },
  )
}

async function manterTelaLigada() {
  try {
    const wl = (navigator as unknown as { wakeLock?: { request: (t: string) => Promise<{ release: () => Promise<void> }> } }).wakeLock
    wakeLock = (await wl?.request('screen')) ?? null
  } catch { /* alguns celulares recusam: o aviso na tela já pede para deixar aberta */ }
}

function comecar() {
  iniciarLocalizacao()
  manterTelaLigada()
  clearInterval(timer)
  timer = window.setInterval(ping, PING_MS)
}

function parar() {
  clearInterval(timer)
  timer = undefined
  if (watchId !== undefined) navigator.geolocation.clearWatch(watchId)
  watchId = undefined
  posicao = null
  temLocal.value = false
  wakeLock?.release().catch(() => {})
  wakeLock = null
}

async function alternar() {
  trocando.value = true
  try {
    if (online.value) {
      parar()
      const r = await api.post<{ data: Resumo }>(`/mototaxi/painel/${token}/offline`, {})
      aplicar(r.data)
    } else {
      comecar()
      // dá um instante para o navegador pedir/entregar a localização antes do primeiro aviso
      await new Promise((ok) => setTimeout(ok, 800))
      await ping()
    }
  } catch {
    erro.value = 'Não deu certo agora. Tente de novo.'
  } finally {
    trocando.value = false
  }
}

// Ao voltar para a tela: avisa na hora e retoma a tela ligada (o navegador solta o wake lock em segundo plano)
function aoVoltar() {
  if (document.hidden || !online.value) return
  manterTelaLigada()
  ping()
}

onMounted(async () => {
  document.addEventListener('visibilitychange', aoVoltar)
  try {
    const r = await api.get<{ data: Resumo }>(`/mototaxi/painel/${token}`)
    aplicar(r.data)
    if (r.data.online) { comecar(); ping() }
  } catch {
    invalido.value = true
  } finally {
    carregando.value = false
  }
})

onBeforeUnmount(() => {
  document.removeEventListener('visibilitychange', aoVoltar)
  parar()
})

function verLista() {
  window.location.href = '/mototaxi'
}
</script>

<style scoped>
.mp { min-height: 100vh; background: var(--bg); color: var(--cream); font-family: var(--body); display: flex; flex-direction: column; align-items: center; padding: 0 16px calc(32px + env(safe-area-inset-bottom, 0px)); }
.mp-top { width: 100%; max-width: 560px; padding: 18px 2px 10px; }
.mp-brand { font-size: 18px; font-weight: 800; color: var(--orange); }
.mp-card { width: 100%; max-width: 560px; background: var(--card); border: 1px solid var(--line); border-radius: 22px; padding: 26px 20px 24px; box-sizing: border-box; }
.mp-center { display: flex; flex-direction: column; gap: 16px; align-items: stretch; text-align: center; }
.mp-emoji { font-size: 54px; line-height: 1; }
.mp-title { font-family: var(--display); font-size: 24px; font-weight: 800; margin: 0; }
.mp-ola { font-family: var(--display); font-size: 20px; font-weight: 800; margin: 0; }
.mp-sub { font-size: 14px; color: var(--muted); margin: 0; line-height: 1.55; }
.mp-status { display: inline-flex; align-self: center; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 999px; background: var(--bg); border: 1px solid var(--line-strong); font-weight: 700; font-size: 15px; }
.mp-status.on { border-color: var(--green); color: var(--green); }
.mp-dot { width: 12px; height: 12px; border-radius: 50%; background: var(--muted-2); }
.mp-status.on .mp-dot { background: var(--green); box-shadow: 0 0 0 0 color-mix(in srgb, var(--green) 60%, transparent); animation: mp-pulso 1.6s infinite; }
@keyframes mp-pulso { 70% { box-shadow: 0 0 0 10px transparent; } 100% { box-shadow: 0 0 0 0 transparent; } }
.mp-botao { min-height: 84px; border: none; border-radius: 22px; background: var(--orange); color: #fff; font: inherit; font-size: 22px; font-weight: 800; cursor: pointer; }
.mp-botao.on { background: var(--bg); color: var(--cream); border: 2px solid var(--line-strong); font-size: 18px; min-height: 64px; }
.mp-botao:disabled { opacity: .6; cursor: wait; }
.mp-aviso { margin: 0; font-size: 13px; color: var(--muted); background: var(--bg); border-radius: 12px; padding: 10px 12px; line-height: 1.5; }
.mp-erro { margin: 0; font-size: 13px; color: #ff8a8a; }
.mp-link { background: none; border: none; color: var(--orange); text-decoration: underline; font: inherit; font-size: 14px; cursor: pointer; padding: 10px; }
@media (prefers-reduced-motion: reduce) { .mp-status.on .mp-dot { animation: none; } }
</style>
