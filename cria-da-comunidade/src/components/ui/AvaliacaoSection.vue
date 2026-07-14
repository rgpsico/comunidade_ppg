<template>
  <div class="av-section">

    <!-- ===== AVALIAÇÕES ===== -->
    <div class="av-header">
      <div class="av-summary">
        <div class="av-big-score">{{ interacoes.avaliacoes.media.toFixed(1) }}</div>
        <div class="av-stars-row">
          <StarRating :nota="interacoes.avaliacoes.media" readonly />
          <span class="av-count">{{ interacoes.avaliacoes.total }} avaliações</span>
        </div>
      </div>
    </div>

    <!-- Formulário de avaliação (usuário logado sem avaliação prévia) -->
    <div v-if="auth.isAuthenticated && !interacoes.avaliacoes.minhaAvaliacao" class="av-form-card">
      <p class="av-form-title">Avalie este profissional</p>
      <StarRating v-model:nota="formNota" class="av-star-pick" />
      <textarea
        v-model="formTexto"
        class="av-textarea"
        placeholder="Conte como foi sua experiência (opcional)..."
        rows="3"
        maxlength="1000"
      />
      <div class="av-form-footer">
        <span class="av-char">{{ formTexto.length }}/1000</span>
        <button
          class="av-submit"
          :disabled="formNota === 0 || submittingAv"
          @click="enviarAvaliacao"
        >
          {{ submittingAv ? 'Enviando…' : 'Enviar avaliação' }}
        </button>
      </div>
      <p v-if="errorFormAv" class="av-error">{{ errorFormAv }}</p>
    </div>

    <!-- Minha avaliação existente -->
    <div v-if="interacoes.avaliacoes.minhaAvaliacao" class="av-mine-card">
      <div class="av-mine-label">Sua avaliação</div>
      <div class="av-mine-body">
        <StarRating :nota="interacoes.avaliacoes.minhaAvaliacao.nota" readonly />
        <p v-if="interacoes.avaliacoes.minhaAvaliacao.texto" class="av-mine-text">
          "{{ interacoes.avaliacoes.minhaAvaliacao.texto }}"
        </p>
      </div>
      <button class="av-delete" @click="removerAvaliacao">Remover avaliação</button>
    </div>

    <!-- CTA para login -->
    <div v-if="!auth.isAuthenticated" class="av-login-cta">
      <p>Faça login para avaliar</p>
      <button class="av-login-btn" @click="ui.goTo('login')">Entrar</button>
    </div>

    <!-- Lista de avaliações -->
    <div v-if="loadingAv" class="av-loading">Carregando avaliações…</div>
    <div v-else-if="interacoes.avaliacoes.data.length" class="av-list">
      <div
        v-for="av in interacoes.avaliacoes.data"
        :key="av.id"
        class="av-card"
      >
        <div class="av-av" :style="{ background: userColor(av.user.name) }">
          {{ initials(av.user.name) }}
        </div>
        <div class="av-body">
          <div class="av-head-row">
            <span class="av-name">{{ av.user.name }}</span>
            <StarRating :nota="av.nota" readonly size="sm" />
            <span class="av-time">{{ timeAgo(av.createdAt) }}</span>
          </div>
          <p v-if="av.texto" class="av-text">{{ av.texto }}</p>
        </div>
      </div>
    </div>
    <p v-else-if="!loadingAv" class="av-empty">Nenhuma avaliação ainda. Seja o primeiro!</p>

    <!-- ===== COMENTÁRIOS ===== -->
    <div class="com-section">
      <h4 class="com-title">Comentários <span class="com-count">{{ interacoes.comentarios.total }}</span></h4>

      <!-- Formulário de comentário -->
      <div v-if="auth.isAuthenticated" class="com-form">
        <div class="com-av-mini" :style="{ background: userColor(auth.user?.name ?? '') }">
          {{ initials(auth.user?.name ?? '?') }}
        </div>
        <div class="com-input-wrap">
          <textarea
            v-model="formCorpo"
            class="av-textarea"
            :placeholder="replyTo ? `Respondendo a ${replyTo.user.name}…` : 'Escreva um comentário…'"
            rows="2"
            maxlength="1000"
            @keydown.ctrl.enter="enviarComentario"
          />
          <div class="com-form-footer">
            <button v-if="replyTo" class="com-cancel-reply" @click="replyTo = null">
              Cancelar resposta
            </button>
            <button
              class="av-submit sm"
              :disabled="!formCorpo.trim() || submittingCom"
              @click="enviarComentario"
            >
              {{ submittingCom ? 'Enviando…' : replyTo ? 'Responder' : 'Comentar' }}
            </button>
          </div>
        </div>
      </div>
      <div v-else class="av-login-cta sm">
        <p>Faça login para comentar</p>
        <button class="av-login-btn" @click="ui.goTo('login')">Entrar</button>
      </div>

      <p v-if="errorFormCom" class="av-error">{{ errorFormCom }}</p>

      <!-- Lista de comentários -->
      <div v-if="loadingCom" class="av-loading">Carregando comentários…</div>
      <div v-else class="com-list">
        <ComentarioItem
          v-for="com in interacoes.comentarios.data"
          :key="com.id"
          :comentario="com"
          @reply="setReplyTo"
          @delete="removerComentario"
        />
        <p v-if="!interacoes.comentarios.data.length" class="av-empty">
          Nenhum comentário ainda.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useInteracoesStore } from '../../stores/interacoes'
import { useAuthStore } from '../../stores/auth'
import { useUiStore } from '../../stores/ui'
import StarRating from './StarRating.vue'
import ComentarioItem from './ComentarioItem.vue'
import type { Comentario } from '../../types'

const props = defineProps<{
  tipo: string   // 'profissionais' | 'eventos' | ...
  id: string
}>()

const interacoes  = useInteracoesStore()
const auth        = useAuthStore()
const ui          = useUiStore()

// --- avaliações ---
const formNota    = ref(0)
const formTexto   = ref('')
const submittingAv = ref(false)
const errorFormAv  = ref('')
const loadingAv    = ref(false)

// --- comentários ---
const formCorpo    = ref('')
const submittingCom = ref(false)
const errorFormCom  = ref('')
const replyTo       = ref<Comentario | null>(null)
const loadingCom    = ref(false)

async function load() {
  loadingAv.value  = true
  loadingCom.value = true
  await Promise.all([
    interacoes.fetchAvaliacoes(props.tipo, props.id),
    interacoes.fetchComentarios(props.tipo, props.id),
  ])
  loadingAv.value  = false
  loadingCom.value = false
}

onMounted(load)
watch(() => props.id, load)

async function enviarAvaliacao() {
  if (formNota.value === 0) return
  submittingAv.value = true
  errorFormAv.value  = ''
  try {
    await interacoes.submitAvaliacao(props.tipo, props.id, formNota.value, formTexto.value)
    formNota.value  = 0
    formTexto.value = ''
  } catch (e: unknown) {
    errorFormAv.value = e instanceof Error ? e.message : 'Erro ao enviar avaliação'
  } finally {
    submittingAv.value = false
  }
}

async function removerAvaliacao() {
  const av = interacoes.avaliacoes.minhaAvaliacao
  if (!av) return
  await interacoes.deleteAvaliacao(av.id)
}

function setReplyTo(comentario: Comentario) {
  replyTo.value   = comentario
  formCorpo.value = ''
}

async function enviarComentario() {
  if (!formCorpo.value.trim()) return
  submittingCom.value = true
  errorFormCom.value  = ''
  try {
    await interacoes.submitComentario(
      props.tipo, props.id, formCorpo.value.trim(), replyTo.value?.id
    )
    formCorpo.value = ''
    replyTo.value   = null
  } catch (e: unknown) {
    errorFormCom.value = e instanceof Error ? e.message : 'Erro ao enviar comentário'
  } finally {
    submittingCom.value = false
  }
}

async function removerComentario(id: string, parentId?: string) {
  await interacoes.deleteComentario(id, parentId)
}

// helpers
function initials(name: string): string {
  return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

const COLORS = ['#FF5E1A', '#2BD96B', '#FFD23F', '#5E5AFF', '#FF2D78', '#00C2C7']
function userColor(name: string): string {
  let h = 0
  for (let i = 0; i < name.length; i++) h = (h * 31 + name.charCodeAt(i)) & 0xffffffff
  return COLORS[Math.abs(h) % COLORS.length]
}

function timeAgo(iso: string): string {
  const diff = Date.now() - new Date(iso).getTime()
  const m = Math.floor(diff / 60000)
  if (m < 60)  return `há ${m || 1}min`
  const h = Math.floor(m / 60)
  if (h < 24)  return `há ${h}h`
  const d = Math.floor(h / 24)
  if (d < 30)  return `há ${d}d`
  const mo = Math.floor(d / 30)
  return `há ${mo} ${mo === 1 ? 'mês' : 'meses'}`
}
</script>

<style scoped>
.av-section { display: flex; flex-direction: column; gap: 20px; }

/* Header / resumo */
.av-header { display: flex; align-items: center; gap: 20px; }
.av-summary { display: flex; align-items: center; gap: 12px; }
.av-big-score { font-family: var(--display); font-size: 48px; font-weight: 800; letter-spacing: -0.04em; line-height: 1; color: var(--yellow); }
.av-stars-row { display: flex; flex-direction: column; gap: 4px; }
.av-count { font-size: 12px; color: var(--muted); }

/* Formulário avaliação */
.av-form-card, .av-mine-card {
  background: var(--card-2);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.av-form-title { font-size: 13px; font-weight: 600; color: var(--cream); }
.av-star-pick { align-self: flex-start; }
.av-textarea {
  width: 100%;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  color: var(--cream);
  font-size: 13px;
  padding: 10px 12px;
  resize: vertical;
  min-height: 72px;
  font-family: inherit;
  line-height: 1.5;
  transition: border-color 0.15s;
}
.av-textarea:focus { outline: none; border-color: var(--orange); }
.av-textarea::placeholder { color: var(--muted-2); }
.av-form-footer { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
.av-char { font-family: var(--mono); font-size: 10px; color: var(--muted-2); }
.av-submit {
  padding: 8px 18px;
  background: var(--orange);
  color: var(--black);
  border-radius: var(--radius-md);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
}
.av-submit:disabled { opacity: 0.5; cursor: default; }
.av-submit:not(:disabled):hover { transform: translateY(-1px); }
.av-submit.sm { padding: 6px 14px; font-size: 12px; }
.av-error { font-size: 12px; color: var(--red, #e53); }

/* Minha avaliação */
.av-mine-label { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--orange); }
.av-mine-body { display: flex; flex-direction: column; gap: 6px; }
.av-mine-text { font-size: 13px; color: var(--muted); font-style: italic; }
.av-delete { align-self: flex-start; font-size: 11px; color: var(--muted); cursor: pointer; padding: 4px 0; }
.av-delete:hover { color: var(--red, #e53); }

/* CTA login */
.av-login-cta { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--card-2); border: 1px dashed var(--line); border-radius: var(--radius-lg); }
.av-login-cta p { font-size: 13px; color: var(--muted); flex: 1; }
.av-login-cta.sm { padding: 10px 14px; }
.av-login-btn { padding: 6px 14px; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-md); font-size: 12px; font-weight: 600; color: var(--cream); cursor: pointer; }
.av-login-btn:hover { border-color: var(--orange); color: var(--orange); }

/* Lista */
.av-loading, .av-empty { font-size: 13px; color: var(--muted); text-align: center; padding: 16px; }
.av-list { display: flex; flex-direction: column; gap: 12px; }
.av-card { display: flex; gap: 12px; padding: 14px; background: var(--card-2); border: 1px solid var(--line); border-radius: var(--radius-lg); }
.av-av { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 700; font-size: 13px; color: var(--black); flex-shrink: 0; }
.av-body { flex: 1; min-width: 0; }
.av-head-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; }
.av-name { font-size: 13px; font-weight: 600; }
.av-time { font-family: var(--mono); font-size: 10px; color: var(--muted); text-transform: uppercase; margin-left: auto; }
.av-text { font-size: 13px; color: var(--muted); line-height: 1.55; }

/* Comentários */
.com-section { border-top: 1px solid var(--line); padding-top: 20px; display: flex; flex-direction: column; gap: 14px; }
.com-title { font-family: var(--display); font-size: 16px; font-weight: 700; letter-spacing: -0.02em; display: flex; align-items: center; gap: 8px; }
.com-count { font-family: var(--mono); font-size: 11px; background: var(--card-2); border: 1px solid var(--line); border-radius: 999px; padding: 2px 8px; color: var(--muted); }

.com-form { display: flex; gap: 10px; align-items: flex-start; }
.com-av-mini { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 700; font-size: 11px; color: var(--black); flex-shrink: 0; margin-top: 2px; }
.com-input-wrap { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.com-form-footer { display: flex; justify-content: flex-end; align-items: center; gap: 8px; }
.com-cancel-reply { font-size: 11px; color: var(--muted); cursor: pointer; padding: 4px 0; }
.com-cancel-reply:hover { color: var(--cream); }

.com-list { display: flex; flex-direction: column; gap: 12px; }
</style>
