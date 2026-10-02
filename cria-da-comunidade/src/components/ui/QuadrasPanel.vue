<template>
  <div>
    <div v-if="futebol.loadingQuadras" class="loading-msg">Carregando quadras...</div>
    <div v-else-if="futebol.quadras.length === 0" class="empty-state">
      <div class="empty-icon">🏟️</div>
      <p>Nenhuma quadra com horários cadastrados ainda.</p>
    </div>

    <div v-else class="quadras">
      <article v-for="q in futebol.quadras" :key="q.id" class="quadra">
        <header class="q-head">
          <h2 class="q-nome">{{ q.nome }}</h2>
          <p v-if="q.endereco" class="q-end">📍 {{ q.endereco }}</p>
          <p v-if="q.descricao" class="q-desc">{{ q.descricao }}</p>
        </header>

        <div v-if="q.semana.length === 0" class="q-vazio">Sem horários cadastrados nesta quadra.</div>

        <div v-for="d in q.semana" :key="d.dia" class="dia">
          <div class="dia-nome">
            {{ d.nome }}
            <span v-if="d.dia === hoje" class="hoje">hoje</span>
          </div>
          <ul class="slots">
            <li v-for="s in d.slots" :key="d.dia + '-' + s.time_id + '-' + s.hora_inicio">
              <button
                class="slot"
                :class="{ ativo: aberto[q.id] === s.time_id }"
                :style="{ '--tc': cor(q, s.time_id) }"
                @click="alternar(q.id, s.time_id)"
              >
                <span class="hora">{{ s.hora_inicio }}–{{ s.hora_fim }}</span>
                <span class="titular">{{ s.time_nome }}</span>
                <span class="seta" aria-hidden="true">{{ aberto[q.id] === s.time_id ? '▲' : '▼' }}</span>
              </button>
            </li>
          </ul>
        </div>

        <div v-if="detalhe(q)" class="detalhe" :style="{ '--tc': cor(q, detalhe(q)!.id) }">
          <h3>{{ detalhe(q)!.nome }}</h3>
          <p v-if="detalhe(q)!.descricao">{{ detalhe(q)!.descricao }}</p>
          <div v-if="detalhe(q)!.regras">
            <strong>Regras</strong>
            <p class="regras">{{ detalhe(q)!.regras }}</p>
          </div>
          <p class="dias-do-time">
            <strong>Dias e horários:</strong>
            {{ detalhe(q)!.horarios.map(h => `${DIAS_CURTOS[h.dia_semana]} ${h.hora_inicio}–${h.hora_fim}`).join(' · ') }}
          </p>
        </div>
      </article>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, watch } from 'vue'
import { useDataStore } from '../../stores/data'
import { useFutebolStore } from '../../stores/futebol'
import type { Quadra, QuadraTimeInfo } from '../../types'

const data = useDataStore()
const futebol = useFutebolStore()

const DIAS_CURTOS = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
const CORES = ['#3B82F6', '#EF4444', '#2BD96B', '#FFD23F', '#A855F7', '#EC4899', '#14B8A6', '#F97316']
const hoje = new Date().getDay()

const aberto = reactive<Record<number, number | null>>({})

const cor = (q: Quadra, timeId: number) => {
  const i = q.times.findIndex(t => t.id === timeId)
  return CORES[(i < 0 ? 0 : i) % CORES.length]
}

function alternar(quadraId: number, timeId: number) {
  aberto[quadraId] = aberto[quadraId] === timeId ? null : timeId
}

function detalhe(q: Quadra): QuadraTimeInfo | undefined {
  const id = aberto[q.id]
  return id ? q.times.find(t => t.id === id) : undefined
}

onMounted(() => futebol.fetchQuadras())
watch(() => data.activeComunidadeId, () => futebol.fetchQuadras())
</script>

<style scoped>
.quadras { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; align-items: start; }
.quadra { background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 18px; display: flex; flex-direction: column; gap: 14px; }
.q-nome { font-size: 20px; font-weight: 800; color: var(--cream); margin: 0; }
.q-end { font-size: 13px; color: var(--muted); margin: 6px 0 0; }
.q-desc { font-size: 13px; color: var(--muted); margin: 6px 0 0; }
.q-vazio { font-size: 13px; color: var(--muted); }

.dia-nome { font-family: var(--mono); font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
.hoje { color: var(--green); border: 1px solid var(--green); border-radius: 999px; padding: 1px 8px; font-size: 10px; }
.slots { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 6px; }
.slot {
  width: 100%;
  min-height: 48px;
  display: flex; align-items: center; gap: 12px;
  padding: 8px 12px;
  text-align: left;
  background: color-mix(in srgb, var(--tc) 12%, var(--bg));
  border: 1px solid color-mix(in srgb, var(--tc) 35%, var(--line));
  border-left: 5px solid var(--tc);
  border-radius: 10px;
  color: var(--cream);
  cursor: pointer;
  font-size: 14px;
}
.slot.ativo { border-color: var(--tc); }
.hora { font-family: var(--mono); font-size: 13px; font-weight: 600; white-space: nowrap; }
.titular { flex: 1; font-weight: 600; }
.seta { color: var(--muted); font-size: 10px; }

.detalhe {
  background: color-mix(in srgb, var(--tc) 10%, var(--bg));
  border: 1px solid color-mix(in srgb, var(--tc) 35%, var(--line));
  border-radius: 12px;
  padding: 14px;
  font-size: 14px;
  color: var(--cream);
}
.detalhe h3 { margin: 0 0 6px; font-size: 16px; }
.detalhe p { margin: 4px 0 8px; color: var(--muted); }
.detalhe strong { color: var(--cream); font-size: 13px; }
.regras { white-space: pre-line; }
.dias-do-time { margin-bottom: 0 !important; }

.loading-msg, .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
.empty-icon { font-size: 48px; margin-bottom: 12px; }

@media (max-width: 768px) {
  .quadras { grid-template-columns: 1fr; }
}
</style>
