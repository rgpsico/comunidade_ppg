<template>
  <div class="mt-page fade-up">
    <div class="page-head">
      <div>
        <div class="eyebrow">🏍️ mototáxi</div>
        <h1 class="page-title display">Mototáxi da comunidade</h1>
        <p class="page-sub">
          <template v-if="carregando">Carregando…</template>
          <template v-else-if="lista.length === 0">Ainda não há mototaxistas cadastrados.</template>
          <template v-else><strong class="mt-on">{{ onlineTotal }} online agora</strong> · {{ lista.length }} cadastrados</template>
        </p>
      </div>
      <button class="cad-cta" type="button" @click="ui.goTo('mototaxiCadastro')">🏍️ Sou mototaxista: quero me cadastrar</button>
    </div>

    <p v-if="erro" class="mt-erro">{{ erro }}</p>

    <ul class="mt-list">
      <li v-for="m in lista" :key="m.id" class="mt-card" :class="{ on: m.online }">
        <img class="mt-foto" :src="m.foto_url" :alt="m.nome" loading="lazy" />
        <div class="mt-info">
          <strong class="mt-nome">{{ m.nome }}</strong>
          <span v-if="m.horario" class="mt-hor">🕒 {{ m.horario }}</span>
          <span class="mt-badge" :class="{ on: m.online }">
            <i class="mt-dot" aria-hidden="true"></i>{{ rotulo(m) }}
          </span>
        </div>
        <a class="mt-zap" :href="zap(m.whatsapp)" target="_blank" rel="noopener" :aria-label="`Chamar ${m.nome} no WhatsApp`">Chamar no WhatsApp</a>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { api } from '../../services/api'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'

interface Mototaxi { id: number; nome: string; whatsapp: string; horario: string | null; foto_url: string; online: boolean; visto_em: string | null }

const ui = useUiStore()
const dataStore = useDataStore()
const lista = ref<Mototaxi[]>([])
const carregando = ref(true)
const erro = ref('')
const agora = ref(Date.now())
let timer: number | undefined

const onlineTotal = computed(() => lista.value.filter((m) => m.online).length)

async function carregar() {
  try {
    const cid = dataStore.activeComunidadeId
    const r = await api.get<{ data: Mototaxi[] }>(`/mototaxis${cid ? `?comunidade_id=${cid}` : ''}`)
    lista.value = r.data
    agora.value = Date.now()
    erro.value = ''
  } catch {
    erro.value = 'Não foi possível atualizar a lista agora.'
  } finally {
    carregando.value = false
  }
}

function rotulo(m: Mototaxi): string {
  if (m.online) return 'Online agora'
  if (!m.visto_em) return 'Offline'
  const min = Math.round((agora.value - new Date(m.visto_em).getTime()) / 60000)
  if (min < 60) return `Offline · visto há ${Math.max(min, 1)} min`
  const h = Math.round(min / 60)
  return h < 24 ? `Offline · visto há ${h} h` : 'Offline'
}

function zap(numero: string): string {
  const d = numero.replace(/\D/g, '')
  return `https://wa.me/${d.length <= 11 ? '55' + d : d}`
}

// A lista se atualiza sozinha: quem fica online ou sai aparece em até 30s
onMounted(() => { carregar(); timer = window.setInterval(carregar, 30000) })
onBeforeUnmount(() => clearInterval(timer))
</script>

<style scoped>
.mt-page { padding: 28px 32px; max-width: 900px; }
.page-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: clamp(28px, 3.5vw, 44px); font-weight: 800; margin: 6px 0 4px; }
.page-sub { font-size: 14px; color: var(--muted); }
.mt-on { color: var(--green); }
.cad-cta { background: color-mix(in srgb, var(--orange) 16%, var(--card)); border: 1px solid var(--orange); color: var(--cream); border-radius: var(--radius-full); padding: 10px 18px; font: inherit; font-size: 13px; font-weight: 700; cursor: pointer; min-height: 44px; }
.cad-cta:hover { background: var(--orange); color: #fff; }
.mt-erro { color: #ff8a8a; font-size: 13px; }
.mt-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 12px; }
.mt-card { display: grid; grid-template-columns: 56px minmax(0, 1fr) auto; gap: 14px; align-items: center; background: var(--card); border: 1px solid var(--line); border-radius: 18px; padding: 14px 16px; }
.mt-card.on { border-color: color-mix(in srgb, var(--green) 55%, var(--line)); }
.mt-foto { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; background: var(--bg); }
.mt-info { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
.mt-nome { font-size: 16px; overflow-wrap: anywhere; }
.mt-hor { font-size: 13px; color: var(--muted); }
.mt-badge { display: inline-flex; align-items: center; gap: 7px; font-size: 12px; font-weight: 700; color: var(--muted); }
.mt-badge.on { color: var(--green); }
.mt-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--muted-2); display: inline-block; }
.mt-badge.on .mt-dot { background: var(--green); }
.mt-zap { background: var(--orange); color: #fff; text-decoration: none; border-radius: var(--radius-full); padding: 11px 16px; font-size: 13px; font-weight: 700; text-align: center; min-height: 20px; }
.mt-zap:hover { filter: brightness(1.1); }
@media (max-width: 640px) {
  .mt-page { padding: 20px 16px; }
  .mt-card { grid-template-columns: 48px minmax(0, 1fr); }
  .mt-foto { width: 48px; height: 48px; }
  .mt-zap { grid-column: 1 / -1; }
}
</style>
