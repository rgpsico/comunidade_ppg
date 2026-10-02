<template>
  <Teleport to="body">
    <div class="wz-backdrop" @click.self="$emit('close')">
      <div class="wz-sheet" role="dialog" aria-modal="true" aria-label="Criar pelada">
        <div class="wz-handle"></div>

        <div class="wz-head">
          <button v-if="step > 1" class="wz-back" @click="step--">← Voltar</button>
          <span v-else></span>
          <span class="wz-step">Passo {{ step }} de {{ TOTAL }}</span>
        </div>
        <div class="wz-progress"><div class="wz-progress-fill" :style="{ width: (step / TOTAL) * 100 + '%' }"></div></div>

        <!-- 1. Tipo -->
        <section v-if="step === 1">
          <h2 class="wz-title">Que tipo de pelada é?</h2>
          <p class="wz-sub">Escolha o mais parecido. Você ajusta os números no próximo passo.</p>
          <div class="wz-options">
            <button
              v-for="t in TIPOS"
              :key="t.value"
              class="wz-option"
              :class="{ active: form.tipo === t.value }"
              @click="escolherTipo(t)"
            >
              <span class="wz-option-emoji">{{ t.emoji }}</span>
              <span class="wz-option-main">
                <strong>{{ t.label }}</strong>
                <small>{{ t.desc }}</small>
              </span>
            </button>
          </div>
        </section>

        <!-- 2. Jogadores -->
        <section v-else-if="step === 2">
          <h2 class="wz-title">Quantos jogam por time?</h2>
          <p class="wz-sub">Cada time é um bloco de cor na lista.</p>

          <div class="wz-field">
            <label>Jogadores na linha</label>
            <div class="wz-stepper">
              <button :disabled="form.jogadores_linha <= 2" @click="form.jogadores_linha--" aria-label="Menos um">−</button>
              <span class="wz-stepper-value">{{ form.jogadores_linha }}</span>
              <button :disabled="form.jogadores_linha >= 10" @click="form.jogadores_linha++" aria-label="Mais um">+</button>
            </div>
          </div>

          <div class="wz-field">
            <label>Tem goleiro fixo por time?</label>
            <div class="wz-chips">
              <button class="wz-chip" :class="{ active: form.goleiros_por_time === 1 }" @click="form.goleiros_por_time = 1">🧤 Sim, 1 no gol</button>
              <button class="wz-chip" :class="{ active: form.goleiros_por_time === 0 }" @click="form.goleiros_por_time = 0">Não, só linha</button>
            </div>
          </div>

          <p class="wz-hint">Cada time terá {{ form.jogadores_linha + form.goleiros_por_time }} jogador(es):
            {{ form.jogadores_linha }} na linha{{ form.goleiros_por_time ? ' + 1 goleiro' : '' }}.</p>
        </section>

        <!-- 3. Tempo -->
        <section v-else-if="step === 3">
          <h2 class="wz-title">Quanto tempo dura cada jogo?</h2>
          <p class="wz-sub">Em minutos. Serve de referência pra todo mundo na fila.</p>
          <div class="wz-chips">
            <button
              v-for="m in TEMPOS"
              :key="m"
              class="wz-chip"
              :class="{ active: form.tempo_jogo_min === m }"
              @click="form.tempo_jogo_min = m"
            >{{ m }} min</button>
          </div>
          <div class="wz-field">
            <label>Outro valor</label>
            <input v-model.number="form.tempo_jogo_min" type="number" min="3" max="120" class="wz-input" inputmode="numeric" />
          </div>
        </section>

        <!-- 4. Detalhes -->
        <section v-else>
          <h2 class="wz-title">Dá um nome pra pelada</h2>
          <div class="wz-field">
            <label>Nome *</label>
            <input v-model="form.nome" class="wz-input" maxlength="120" placeholder="Ex: Pelada de quinta na quadra do morro" />
          </div>
          <div class="wz-field">
            <label>Local</label>
            <input v-model="form.local" class="wz-input" maxlength="160" placeholder="Ex: Quadra da associação" />
          </div>
          <div class="wz-field">
            <label>Quando</label>
            <input v-model="form.quando" class="wz-input" maxlength="120" placeholder="Ex: Quintas, 19h" />
          </div>
          <div class="wz-summary">
            ⚽ {{ resumo }}
          </div>
        </section>

        <p v-if="erro" class="wz-erro">{{ erro }}</p>

        <div class="wz-actions">
          <button class="wz-btn-ghost" @click="$emit('close')">Cancelar</button>
          <button v-if="step < TOTAL" class="wz-btn" :disabled="!podeAvancar" @click="step++">Continuar</button>
          <button v-else class="wz-btn" :disabled="!podeAvancar || salvando" @click="criar">
            {{ salvando ? 'Criando...' : 'Criar pelada' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue'
import { useFutebolStore } from '../../stores/futebol'
import type { PeladaForm, PeladaTipo } from '../../types'

const emit = defineEmits<{ close: []; created: [id: number] }>()

const futebol = useFutebolStore()
const TOTAL = 4
const step = ref(1)
const salvando = ref(false)
const erro = ref('')

const TIPOS: { value: PeladaTipo; label: string; desc: string; emoji: string; linha: number; gol: number; tempo: number }[] = [
  { value: 'quadra', label: 'Quadra / Futsal', desc: '4 na linha + 1 no gol', emoji: '🏟️', linha: 4, gol: 1, tempo: 10 },
  { value: 'society', label: 'Society', desc: '6 na linha + 1 no gol', emoji: '🌱', linha: 6, gol: 1, tempo: 10 },
  { value: 'campo', label: 'Campo', desc: '10 na linha + 1 no gol', emoji: '🥅', linha: 10, gol: 1, tempo: 15 },
  { value: 'racha', label: 'Racha', desc: '3 na linha, sem goleiro', emoji: '🔥', linha: 3, gol: 0, tempo: 7 },
]
const TEMPOS = [5, 7, 10, 15, 20]

const form = reactive<PeladaForm>({
  nome: '',
  tipo: 'quadra',
  jogadores_linha: 4,
  goleiros_por_time: 1,
  tempo_jogo_min: 10,
  local: '',
  quando: '',
})

function escolherTipo(t: (typeof TIPOS)[number]) {
  form.tipo = t.value
  form.jogadores_linha = t.linha
  form.goleiros_por_time = t.gol
  form.tempo_jogo_min = t.tempo
  step.value = 2
}

const podeAvancar = computed(() => {
  if (step.value === 3) return form.tempo_jogo_min >= 3 && form.tempo_jogo_min <= 120
  if (step.value === 4) return form.nome.trim().length > 1
  return true
})

const resumo = computed(() => {
  const gol = form.goleiros_por_time ? ' + gol' : ''
  return `${form.jogadores_linha} na linha${gol} · ${form.tempo_jogo_min} min por jogo`
})

async function criar() {
  erro.value = ''
  salvando.value = true
  try {
    const p = await futebol.criar({ ...form, nome: form.nome.trim() })
    emit('created', p.id)
  } catch (e: unknown) {
    erro.value = e instanceof Error ? e.message : 'Não deu pra criar a pelada.'
  } finally {
    salvando.value = false
  }
}

function onKey(e: KeyboardEvent) {
  if (e.key === 'Escape') emit('close')
}
onMounted(() => {
  document.body.style.overflow = 'hidden'
  window.addEventListener('keydown', onKey)
})
onBeforeUnmount(() => {
  document.body.style.overflow = ''
  window.removeEventListener('keydown', onKey)
})
</script>

<style scoped>
.wz-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.65);
  z-index: 1000;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.wz-sheet {
  background: var(--card);
  border-radius: 20px 20px 0 0;
  padding: 16px 22px calc(24px + env(safe-area-inset-bottom, 0px));
  width: 100%;
  max-width: 560px;
  max-height: 92vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.wz-handle { width: 40px; height: 4px; background: var(--line-strong); border-radius: 2px; margin: 0 auto; }

.wz-head { display: flex; justify-content: space-between; align-items: center; min-height: 28px; }
.wz-back { background: none; border: none; color: var(--muted); font-size: 13px; cursor: pointer; padding: 6px 0; }
.wz-step { font-family: var(--mono); font-size: 11px; letter-spacing: 0.1em; color: var(--muted); text-transform: uppercase; }
.wz-progress { height: 4px; background: var(--bg); border-radius: 2px; overflow: hidden; }
.wz-progress-fill { height: 100%; background: var(--orange); transition: width 0.25s ease; }

.wz-title { font-size: 20px; font-weight: 800; color: var(--cream); margin: 4px 0 0; }
.wz-sub { font-size: 13px; color: var(--muted); margin: 6px 0 0; }
.wz-hint { font-size: 13px; color: var(--muted); margin: 0; }

.wz-options { display: flex; flex-direction: column; gap: 10px; margin-top: 14px; }
.wz-option {
  display: flex;
  align-items: center;
  gap: 14px;
  text-align: left;
  min-height: 56px;
  padding: 12px 14px;
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 14px;
  color: var(--cream);
  cursor: pointer;
}
.wz-option.active, .wz-option:hover { border-color: var(--orange); }
.wz-option-emoji { font-size: 26px; }
.wz-option-main { display: flex; flex-direction: column; gap: 2px; }
.wz-option-main small { color: var(--muted); font-size: 12px; }

.wz-field { display: flex; flex-direction: column; gap: 8px; margin-top: 14px; }
.wz-field label { font-size: 13px; color: var(--muted); }
.wz-input {
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 10px;
  padding: 12px;
  min-height: 46px;
  color: var(--cream);
  font-size: 15px;
  width: 100%;
  box-sizing: border-box;
}
.wz-input:focus { outline: none; border-color: var(--orange); }

.wz-stepper { display: flex; align-items: center; gap: 18px; }
.wz-stepper button {
  width: 48px; height: 48px;
  border-radius: 50%;
  border: 1px solid var(--line-strong);
  background: var(--bg);
  color: var(--cream);
  font-size: 24px;
  cursor: pointer;
}
.wz-stepper button:disabled { opacity: 0.35; cursor: not-allowed; }
.wz-stepper-value { font-size: 34px; font-weight: 900; color: var(--cream); min-width: 48px; text-align: center; }

.wz-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
.wz-field .wz-chips { margin-top: 0; }
.wz-chip {
  min-height: 44px;
  padding: 0 16px;
  border-radius: 999px;
  border: 1px solid var(--line);
  background: var(--bg);
  color: var(--muted);
  font-size: 14px;
  cursor: pointer;
}
.wz-chip.active { border-color: var(--orange); color: var(--orange); background: #FF5E1A11; }

.wz-summary {
  margin-top: 16px;
  padding: 12px 14px;
  border-radius: 12px;
  background: var(--bg);
  border: 1px dashed var(--line-strong);
  color: var(--cream);
  font-size: 14px;
}

.wz-erro { color: #EF4444; font-size: 13px; margin: 0; }

.wz-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 4px; }
.wz-btn {
  min-height: 46px;
  padding: 0 24px;
  background: var(--orange);
  color: #fff;
  border: none;
  border-radius: 999px;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
}
.wz-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.wz-btn-ghost {
  min-height: 46px;
  padding: 0 20px;
  background: transparent;
  color: var(--muted);
  border: 1px solid var(--line);
  border-radius: 999px;
  font-size: 14px;
  cursor: pointer;
}

@media (min-width: 769px) {
  .wz-backdrop { align-items: center; }
  .wz-sheet { border-radius: 20px; }
}
</style>
