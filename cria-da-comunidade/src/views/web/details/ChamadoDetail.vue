<template>
  <div v-if="chamado" class="chamado-detail fade-up">
    <!-- Header -->
    <div class="detail-header">
      <button class="back-btn" @click="ui.goTo('chamados')">
        ← Chamados
      </button>
      <div class="badges">
        <span class="badge-tipo" :class="chamado.tipo">
          {{ chamado.tipo === 'problema' ? '⚠️ Problema' : '🔧 Serviço' }}
        </span>
        <span class="badge-urgencia" :class="chamado.urgencia">{{ urgenciaLabel }}</span>
        <span class="badge-status" :class="chamado.status">{{ statusLabel }}</span>
      </div>
    </div>

    <h1 class="detail-titulo">{{ chamado.titulo }}</h1>

    <!-- Meta -->
    <div class="meta-row">
      <span class="meta-item">📂 {{ chamado.categoria }}</span>
      <span v-if="chamado.local" class="meta-item">📍 {{ chamado.local }}</span>
      <span v-if="chamado.estimativa_valor" class="meta-item">💰 {{ chamado.estimativa_valor }}</span>
      <span class="meta-item">🗓 {{ formatDate(chamado.created_at) }}</span>
    </div>

    <!-- Descrição -->
    <section class="section-block">
      <h2 class="block-title">Descrição</h2>
      <p class="block-text">{{ chamado.descricao }}</p>
    </section>

    <!-- Criador -->
    <section class="section-block">
      <h2 class="block-title">Criado por</h2>
      <div class="user-row">
        <div class="avatar">{{ initials(chamado.user?.name ?? 'U') }}</div>
        <span>{{ chamado.user?.name ?? 'Usuário' }}</span>
      </div>
    </section>

    <!-- Profissional responsável -->
    <section v-if="chamado.profissional" class="section-block accepted-block">
      <h2 class="block-title">✅ Profissional responsável</h2>
      <div class="user-row">
        <img
          v-if="chamado.profissional.foto_url"
          :src="chamado.profissional.foto_url"
          class="avatar-img"
          alt=""
        />
        <div v-else class="avatar">{{ initials(chamado.profissional.nome) }}</div>
        <div>
          <div class="pro-nome">{{ chamado.profissional.nome }}</div>
          <a
            v-if="chamado.profissional.whatsapp"
            :href="`https://wa.me/55${chamado.profissional.whatsapp.replace(/\D/g,'')}`"
            target="_blank"
            rel="noopener"
            class="link-whatsapp"
          >
            💬 Falar no WhatsApp
          </a>
        </div>
      </div>
      <p v-if="chamado.aceito_em" class="aceito-em">Aceito em {{ formatFull(chamado.aceito_em) }}</p>
    </section>

    <!-- Ações -->
    <section v-if="auth.user" class="section-block acoes-block">
      <button
        v-if="chamado.status === 'aberto'"
        class="btn-primary"
        :disabled="aceitando"
        @click="aceitar"
      >
        {{ aceitando ? 'Aceitando...' : '✅ Aceitar Chamado' }}
      </button>

      <button
        v-if="podeResolver"
        class="btn-success"
        :disabled="resolvendo"
        @click="resolver"
      >
        {{ resolvendo ? 'Marcando...' : '🏁 Marcar como Resolvido' }}
      </button>

      <button
        v-if="chamado.status !== 'cancelado' && chamado.status !== 'resolvido'"
        class="btn-doacao"
        @click="abrirModalDoacao"
      >
        💰 Registrar Doação Externa
      </button>

      <p v-if="acaoErro" class="form-erro">{{ acaoErro }}</p>
    </section>

    <!-- Doações -->
    <section class="section-block" v-if="chamado.doacoes.length > 0 || chamado.total_doacoes > 0">
      <h2 class="block-title">
        💰 Doações registradas
        <span class="total-doacoes">Total: R$ {{ formatMoney(chamado.total_doacoes) }}</span>
      </h2>
      <div class="doacoes-list">
        <div v-for="d in chamado.doacoes" :key="d.id" class="doacao-item">
          <div class="doacao-header">
            <span class="doacao-user">{{ d.user?.name ?? 'Usuário' }}</span>
            <span class="doacao-valor">R$ {{ formatMoney(d.valor) }}</span>
          </div>
          <p v-if="d.mensagem" class="doacao-msg">{{ d.mensagem }}</p>
          <span class="doacao-date">{{ formatDate(d.created_at) }}</span>
        </div>
      </div>
    </section>

    <!-- Modal doação -->
    <Teleport to="body">
      <div v-if="modalDoacaoAberto" class="modal-backdrop" @click.self="fecharModalDoacao">
        <div class="modal-sheet">
          <div class="modal-handle"></div>
          <h2 class="modal-titulo">Registrar Doação</h2>
          <p class="modal-sub">
            O pagamento é realizado externamente (PIX, dinheiro, etc.).
            Aqui você apenas registra sua contribuição.
          </p>

          <div class="form-group">
            <label>Valor doado (R$) *</label>
            <input v-model.number="doacaoForm.valor" type="number" min="1" class="form-input" placeholder="Ex: 50" />
          </div>

          <div class="form-group">
            <label>Mensagem (opcional)</label>
            <textarea v-model="doacaoForm.mensagem" class="form-input" rows="2" placeholder="Deixe uma mensagem de apoio..."></textarea>
          </div>

          <p v-if="doacaoErro" class="form-erro">{{ doacaoErro }}</p>

          <div class="modal-actions">
            <button class="btn-secondary" @click="fecharModalDoacao">Cancelar</button>
            <button class="btn-primary" :disabled="enviandoDoacao" @click="confirmarDoacao">
              {{ enviandoDoacao ? 'Enviando...' : 'Registrar' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>

  <div v-else class="no-chamado">
    <p>Chamado não encontrado.</p>
    <button class="btn-secondary" @click="ui.goTo('chamados')">← Voltar</button>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, onMounted } from 'vue'
import { useUiStore } from '../../../stores/ui'
import { useDataStore } from '../../../stores/data'
import { useAuthStore } from '../../../stores/auth'
import type { Chamado } from '../../../types'

const ui = useUiStore()
const data = useDataStore()
const auth = useAuthStore()

const chamado = ref<Chamado | null>(ui.selectedChamado)

const URGENCIA_LABELS: Record<string, string> = { normal: '✅ Normal', urgente: '⚡ Urgente', critico: '🚨 Crítico' }
const STATUS_LABELS: Record<string, string> = { aberto: 'Aberto', aceito: 'Aceito', em_andamento: 'Em andamento', resolvido: 'Resolvido', cancelado: 'Cancelado' }

const urgenciaLabel = computed(() => URGENCIA_LABELS[chamado.value?.urgencia ?? ''] ?? '')
const statusLabel = computed(() => STATUS_LABELS[chamado.value?.status ?? ''] ?? '')

const podeResolver = computed(() => {
  if (!chamado.value || !auth.user) return false
  if (!['aceito', 'em_andamento'].includes(chamado.value.status)) return false
  const isCreator = chamado.value.user?.id === auth.user.id
  return isCreator
})

// Ações
const aceitando = ref(false)
const resolvendo = ref(false)
const acaoErro = ref('')

async function aceitar() {
  if (!chamado.value) return
  acaoErro.value = ''
  aceitando.value = true
  try {
    chamado.value = await data.aceitarChamado(chamado.value.id)
  } catch (e: any) {
    acaoErro.value = e?.response?.data?.message ?? 'Erro ao aceitar chamado.'
  } finally {
    aceitando.value = false
  }
}

async function resolver() {
  if (!chamado.value) return
  acaoErro.value = ''
  resolvendo.value = true
  try {
    chamado.value = await data.resolverChamado(chamado.value.id)
  } catch (e: any) {
    acaoErro.value = e?.response?.data?.message ?? 'Erro ao resolver chamado.'
  } finally {
    resolvendo.value = false
  }
}

// Modal doação
const modalDoacaoAberto = ref(false)
const enviandoDoacao = ref(false)
const doacaoErro = ref('')
const doacaoForm = reactive({ valor: 0, mensagem: '' })

function abrirModalDoacao() {
  doacaoForm.valor = 0
  doacaoForm.mensagem = ''
  doacaoErro.value = ''
  modalDoacaoAberto.value = true
  document.body.style.overflow = 'hidden'
}

function fecharModalDoacao() {
  modalDoacaoAberto.value = false
  document.body.style.overflow = ''
}

async function confirmarDoacao() {
  if (!chamado.value) return
  doacaoErro.value = ''
  if (!doacaoForm.valor || doacaoForm.valor < 1) {
    doacaoErro.value = 'Informe um valor válido.'
    return
  }
  enviandoDoacao.value = true
  try {
    await data.registrarDoacao(chamado.value.id, doacaoForm.valor, doacaoForm.mensagem)
    fecharModalDoacao()
    // Reload chamado detail
    const updated = await data.fetchChamadoDetail(chamado.value.id)
    if (updated) chamado.value = updated
  } catch (e: any) {
    doacaoErro.value = e?.response?.data?.message ?? 'Erro ao registrar doação.'
  } finally {
    enviandoDoacao.value = false
  }
}

// Load full detail on mount to get doacoes
onMounted(async () => {
  if (chamado.value) {
    const full = await data.fetchChamadoDetail(chamado.value.id)
    if (full) chamado.value = full
  }
})

function initials(name: string): string {
  return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
}

function formatDate(iso: string): string {
  const d = new Date(iso)
  const diff = Math.floor((Date.now() - d.getTime()) / 86400000)
  if (diff === 0) return 'hoje'
  if (diff === 1) return 'ontem'
  return `${diff}d atrás`
}

function formatFull(iso: string): string {
  return new Date(iso).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

function formatMoney(val: number): string {
  return val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>

<style scoped>
.chamado-detail {
  padding: 40px 48px;
  max-width: 800px;
  margin: 0 auto;
}

.detail-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.back-btn {
  background: transparent;
  border: 1px solid var(--line);
  color: var(--muted);
  padding: 6px 14px;
  border-radius: var(--radius-full);
  font-size: 13px;
  cursor: pointer;
}
.back-btn:hover { color: var(--cream); border-color: var(--muted); }

.badges { display: flex; gap: 6px; flex-wrap: wrap; }

.badge-tipo, .badge-urgencia, .badge-status {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: var(--radius-full);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.badge-tipo.problema { background: #FF5E1A22; color: var(--orange); }
.badge-tipo.servico  { background: #2BD96B22; color: var(--green); }
.badge-urgencia.normal  { background: #2BD96B18; color: var(--green); }
.badge-urgencia.urgente { background: #FFD23F22; color: var(--yellow); }
.badge-urgencia.critico { background: #EF444422; color: #EF4444; }
.badge-status.aberto       { background: #3B82F622; color: #60A5FA; }
.badge-status.aceito       { background: #FFD23F22; color: var(--yellow); }
.badge-status.em_andamento { background: #8B5CF622; color: #A78BFA; }
.badge-status.resolvido    { background: #2BD96B22; color: var(--green); }
.badge-status.cancelado    { background: #6B728022; color: var(--muted); }

.detail-titulo {
  font-size: 28px;
  font-weight: 900;
  color: var(--cream);
  line-height: 1.2;
  margin: 0 0 16px;
}

.meta-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 28px;
}

.meta-item {
  font-size: 13px;
  color: var(--muted);
  background: var(--card);
  border: 1px solid var(--line);
  padding: 4px 10px;
  border-radius: var(--radius-full);
}

.section-block {
  margin-bottom: 28px;
  padding-bottom: 28px;
  border-bottom: 1px solid var(--line);
}
.section-block:last-child { border-bottom: none; }

.block-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--cream);
  margin: 0 0 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.block-text {
  font-size: 15px;
  color: var(--muted);
  line-height: 1.7;
  margin: 0;
  white-space: pre-wrap;
}

.user-row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: var(--orange);
  color: #fff;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.avatar-img {
  width: 40px; height: 40px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
}

.pro-nome { font-weight: 700; color: var(--cream); font-size: 15px; }

.link-whatsapp {
  font-size: 13px;
  color: var(--green);
  text-decoration: none;
  margin-top: 4px;
  display: block;
}

.aceito-em {
  font-size: 12px;
  color: var(--muted);
  margin: 8px 0 0;
  font-family: var(--mono);
}

.accepted-block {
  background: #2BD96B08;
  border: 1px solid #2BD96B33;
  border-radius: var(--radius-lg);
  padding: 20px;
}

.acoes-block {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
}

.total-doacoes {
  font-size: 13px;
  font-weight: 700;
  color: var(--green);
}

.doacoes-list { display: flex; flex-direction: column; gap: 12px; }
.doacao-item {
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  padding: 12px;
}
.doacao-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 4px;
}
.doacao-user { font-size: 13px; font-weight: 600; color: var(--cream); }
.doacao-valor { font-size: 13px; font-weight: 700; color: var(--green); }
.doacao-msg { font-size: 13px; color: var(--muted); margin: 4px 0; }
.doacao-date { font-size: 11px; color: var(--muted); font-family: var(--mono); }

.no-chamado {
  padding: 80px 20px;
  text-align: center;
  color: var(--muted);
}

/* Buttons */
.btn-primary {
  padding: 10px 22px;
  background: var(--orange);
  color: #fff;
  border: none;
  border-radius: var(--radius-full);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: filter 0.15s;
}
.btn-primary:hover { filter: brightness(1.1); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-success {
  padding: 10px 22px;
  background: var(--green);
  color: #fff;
  border: none;
  border-radius: var(--radius-full);
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
}

.btn-doacao {
  padding: 10px 22px;
  background: transparent;
  color: var(--yellow);
  border: 1px solid var(--yellow);
  border-radius: var(--radius-full);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}

.btn-secondary {
  padding: 10px 22px;
  background: transparent;
  color: var(--muted);
  border: 1px solid var(--line);
  border-radius: var(--radius-full);
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
}

.form-erro { color: #EF4444; font-size: 13px; margin: 0; width: 100%; }

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.6);
  z-index: 1000;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}
.modal-sheet {
  background: var(--card);
  border-radius: 20px 20px 0 0;
  padding: 24px 24px 40px;
  width: 100%;
  max-width: 500px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.modal-handle {
  width: 40px; height: 4px;
  background: var(--line-strong);
  border-radius: 2px;
  margin: 0 auto 4px;
}
.modal-titulo { font-size: 18px; font-weight: 800; color: var(--cream); margin: 0; }
.modal-sub { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.5; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 13px; color: var(--muted); }
.form-input {
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: var(--radius-md);
  padding: 10px 12px;
  color: var(--cream);
  font-size: 14px;
  width: 100%;
  box-sizing: border-box;
  resize: vertical;
}
.form-input:focus { outline: none; border-color: var(--orange); }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

@media (max-width: 768px) {
  .chamado-detail { padding: 20px 16px; }
  .detail-titulo { font-size: 22px; }
}
</style>
