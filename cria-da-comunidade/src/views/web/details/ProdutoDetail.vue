<template>
  <div class="produto-detail fade-up" v-if="produto && loja">
    <!-- Top bar -->
    <div class="top-bar">
      <button class="back-btn" @click="ui.openLoja(loja)">
        ← {{ loja.nome }}
      </button>
      <a
        :href="`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`"
        target="_blank"
        rel="noopener"
        class="btn-share-fb"
        title="Compartilhar no Facebook"
      >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
        Compartilhar
      </a>
    </div>

    <!-- Galeria -->
    <div class="gallery-wrap">
      <div v-if="produto.imagens.length" class="gallery">
        <div class="gallery-main">
          <img :src="produto.imagens[activeImg]" :alt="produto.nome" class="gallery-img" />
          <span v-if="produto.precoPromocional" class="gallery-badge-off">
            -{{ Math.round((1 - produto.precoPromocional / produto.preco) * 100) }}%
          </span>
        </div>
        <div v-if="produto.imagens.length > 1" class="gallery-thumbs">
          <img
            v-for="(img, i) in produto.imagens"
            :key="i"
            :src="img"
            :alt="produto.nome"
            class="thumb"
            :class="{ active: activeImg === i }"
            @click="activeImg = i"
          />
        </div>
      </div>
      <div v-else class="gallery-empty" :style="{ background: `linear-gradient(135deg, ${loja.cor1}22, ${loja.cor2}22)` }">
        <span class="gallery-icon">📦</span>
      </div>
    </div>

    <!-- Info -->
    <div class="info-wrap">
      <div v-if="produto.categoria" class="prod-cat">{{ produto.categoria }}</div>
      <h1 class="prod-nome">{{ produto.nome }}</h1>

      <div class="preco-row">
        <span v-if="produto.precoPromocional" class="preco-de">{{ fmt(produto.preco) }}</span>
        <span class="preco-por" :style="{ color: loja.cor1 }">{{ fmt(produto.precoPromocional ?? produto.preco) }}</span>
      </div>

      <p v-if="produto.descricao" class="prod-desc">{{ produto.descricao }}</p>

      <!-- Loja info -->
      <div class="loja-strip" @click="ui.openLoja(loja)">
        <img :src="loja.logoUrl" :alt="loja.nome" class="loja-logo" />
        <div class="loja-strip-info">
          <div class="loja-strip-nome">{{ loja.nome }}</div>
          <div class="loja-strip-cat">{{ loja.categoria }}</div>
        </div>
        <svg class="loja-strip-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </div>

      <!-- CTA -->
      <button
        class="btn-interesse"
        :class="{ sent: enviado }"
        :style="{ background: loja.cor1 }"
        :disabled="enviado"
        @click="abrirModal"
      >
        {{ enviado ? '✓ Interesse enviado!' : 'Tenho interesse' }}
      </button>

      <div v-if="enviado" class="feedback-sent">
        Recebemos seu interesse! Entraremos em contato em breve.
      </div>
    </div>
  </div>

  <!-- Modal de contato -->
  <Teleport to="body">
    <div v-if="modalAberto" class="modal-overlay" @click.self="fecharModal">
      <div class="modal-box">
        <button class="modal-close" @click="fecharModal">✕</button>

        <div class="modal-prod-info">
          <img v-if="produto?.imagens?.length" :src="produto.imagens[0]" class="modal-prod-img" />
          <div>
            <div class="modal-prod-nome">{{ produto?.nome }}</div>
            <div class="modal-prod-preco" :style="{ color: loja?.cor1 }">{{ fmt(produto?.precoPromocional ?? produto?.preco ?? 0) }}</div>
          </div>
        </div>

        <p class="modal-desc">Deixe seu nome e telefone e entraremos em contato.</p>

        <div class="modal-form">
          <label class="modal-label">
            Nome
            <input
              v-model="form.nome"
              class="modal-input"
              placeholder="Seu nome completo"
              maxlength="120"
              autocomplete="name"
            />
          </label>
          <label class="modal-label">
            Telefone / WhatsApp
            <input
              v-model="form.fone"
              class="modal-input"
              placeholder="(11) 99999-9999"
              type="tel"
              maxlength="20"
              autocomplete="tel"
            />
          </label>

          <div v-if="formErro" class="modal-erro">{{ formErro }}</div>

          <button
            class="modal-submit"
            :style="{ background: loja?.cor1 }"
            :disabled="loading"
            @click="confirmarInteresse"
          >
            {{ loading ? 'Enviando...' : 'Confirmar interesse' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>

  <div v-if="!produto || !loja" class="no-produto">
    <button class="back-btn" @click="ui.goTo('lojas')">← Lojas</button>
    <p>Produto não encontrado.</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive } from 'vue'
import { useUiStore } from '../../../stores/ui'
import { api } from '../../../services/api'

const ui = useUiStore()
const produto = computed(() => ui.selectedProduto)
const loja    = computed(() => ui.selectedLoja)

const activeImg   = ref(0)
const loading     = ref(false)
const enviado     = ref(false)
const modalAberto = ref(false)
const formErro    = ref('')
const form        = reactive({ nome: '', fone: '' })

const shareUrl = computed(() =>
  produto.value
    ? encodeURIComponent(`https://api.comunidadeppg.com.br/share/produto/${produto.value.id}`)
    : ''
)

function fmt(val: number): string {
  return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function abrirModal() {
  if (enviado.value) return
  formErro.value = ''
  modalAberto.value = true
}

function fecharModal() {
  modalAberto.value = false
}

async function confirmarInteresse() {
  if (!produto.value || !loja.value || loading.value) return

  formErro.value = ''
  if (!form.nome.trim()) { formErro.value = 'Informe seu nome.'; return }
  if (!form.fone.trim()) { formErro.value = 'Informe seu telefone.'; return }

  loading.value = true
  try {
    const preco = produto.value.precoPromocional ?? produto.value.preco
    await api.post('/interesse', {
      produto_nome:   produto.value.nome,
      produto_preco:  fmt(preco),
      loja_nome:      loja.value.nome,
      loja_id:        loja.value.id,
      comprador_nome: form.nome.trim(),
      comprador_fone: form.fone.trim(),
    })
    enviado.value = true
    modalAberto.value = false
  } catch {
    formErro.value = 'Erro ao enviar. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.produto-detail {
  max-width: 680px;
  margin: 0 auto;
  padding: 0 0 80px;
}

.top-bar {
  padding: 20px 24px 0;
}
.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--muted);
  cursor: pointer;
  background: none;
  border: none;
  padding: 0;
  transition: color 0.15s;
}
.back-btn:hover { color: var(--cream); }

.btn-share-fb {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: rgba(24,119,242,0.12);
  color: #1877F2;
  border: 1px solid rgba(24,119,242,0.3);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  text-decoration: none;
  transition: background 0.15s;
}
.btn-share-fb:hover { background: rgba(24,119,242,0.22); }

/* ── Galeria ── */
.gallery-wrap {
  margin: 16px 24px 0;
}
.gallery-main {
  position: relative;
  border-radius: 18px;
  overflow: hidden;
  background: var(--bg-2);
}
.gallery-img {
  width: 100%;
  height: 340px;
  object-fit: cover;
  display: block;
}
.gallery-badge-off {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(43,217,107,0.9);
  color: #000;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 20px;
}
.gallery-thumbs {
  display: flex;
  gap: 8px;
  margin-top: 8px;
  overflow-x: auto;
}
.thumb {
  width: 64px;
  height: 64px;
  border-radius: 10px;
  object-fit: cover;
  flex-shrink: 0;
  cursor: pointer;
  opacity: 0.5;
  border: 2px solid transparent;
  transition: opacity 0.15s, border-color 0.15s;
}
.thumb.active,
.thumb:hover {
  opacity: 1;
  border-color: var(--orange);
}
.gallery-empty {
  height: 280px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.gallery-icon { font-size: 64px; }

/* ── Info ── */
.info-wrap {
  padding: 24px 24px 0;
}
.prod-cat {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--muted-2);
  margin-bottom: 8px;
}
.prod-nome {
  font-size: 26px;
  font-weight: 900;
  color: var(--cream);
  font-family: var(--display);
  letter-spacing: -0.02em;
  margin-bottom: 12px;
  text-wrap: balance;
}
.preco-row {
  display: flex;
  align-items: baseline;
  gap: 10px;
  margin-bottom: 16px;
}
.preco-de {
  font-size: 16px;
  color: var(--muted);
  text-decoration: line-through;
}
.preco-por {
  font-size: 32px;
  font-weight: 800;
  font-family: var(--display);
  letter-spacing: -0.02em;
}
.prod-desc {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.7;
  margin-bottom: 20px;
}

/* ── Loja strip ── */
.loja-strip {
  display: flex;
  align-items: center;
  gap: 12px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 12px 16px;
  cursor: pointer;
  margin-bottom: 20px;
  transition: border-color 0.15s, background 0.15s;
}
.loja-strip:hover {
  border-color: var(--orange);
  background: var(--card-2);
}
.loja-logo {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  object-fit: cover;
  flex-shrink: 0;
}
.loja-strip-info { flex: 1; min-width: 0; }
.loja-strip-nome {
  font-size: 14px;
  font-weight: 700;
  color: var(--cream);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.loja-strip-cat { font-size: 12px; color: var(--muted); margin-top: 1px; }
.loja-strip-arrow { color: var(--muted); flex-shrink: 0; }

/* ── CTA ── */
.btn-interesse {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 14px;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 800;
  color: #fff;
  border: none;
  cursor: pointer;
  transition: filter 0.15s, opacity 0.15s;
  letter-spacing: 0.01em;
}
.btn-interesse:hover:not(:disabled) { filter: brightness(1.1); }
.btn-interesse.sent { cursor: default; opacity: 0.85; }
.btn-interesse.loading { opacity: 0.7; cursor: wait; }
.btn-interesse:disabled { cursor: default; }

.feedback-sent {
  margin-top: 12px;
  text-align: center;
  font-size: 13px;
  color: var(--green);
}

.fade-up {
  animation: fade-up 0.3s ease both;
}
@keyframes fade-up {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

.no-produto { padding: 40px; color: var(--muted); }

/* ── Modal ── */
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0,0,0,0.65);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 0;
  animation: overlay-in 0.2s ease;
}
@keyframes overlay-in { from { opacity: 0 } to { opacity: 1 } }

.modal-box {
  position: relative;
  background: var(--bg);
  border-radius: 24px 24px 0 0;
  width: 100%;
  max-width: 520px;
  padding: 28px 24px 40px;
  animation: slide-up 0.25s ease;
}
@keyframes slide-up { from { transform: translateY(60px); opacity: 0 } to { transform: translateY(0); opacity: 1 } }

.modal-close {
  position: absolute;
  top: 16px; right: 20px;
  background: var(--bg-2);
  border: none;
  color: var(--muted);
  font-size: 16px;
  width: 32px; height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: color 0.15s;
}
.modal-close:hover { color: var(--cream); }

.modal-prod-info {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}
.modal-prod-img {
  width: 56px; height: 56px;
  border-radius: 12px;
  object-fit: cover;
  flex-shrink: 0;
}
.modal-prod-nome { font-size: 15px; font-weight: 700; color: var(--cream); }
.modal-prod-preco { font-size: 18px; font-weight: 800; margin-top: 2px; }

.modal-desc {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 20px;
  line-height: 1.5;
}

.modal-form { display: flex; flex-direction: column; gap: 14px; }

.modal-label {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.modal-input {
  background: var(--bg-2);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 15px;
  color: var(--cream);
  outline: none;
  transition: border-color 0.15s;
  width: 100%;
  box-sizing: border-box;
}
.modal-input:focus { border-color: var(--orange); }

.modal-erro {
  font-size: 13px;
  color: #f87171;
  text-align: center;
}

.modal-submit {
  width: 100%;
  padding: 14px;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 800;
  color: #fff;
  border: none;
  cursor: pointer;
  transition: filter 0.15s, opacity 0.15s;
  margin-top: 4px;
}
.modal-submit:hover:not(:disabled) { filter: brightness(1.1); }
.modal-submit:disabled { opacity: 0.6; cursor: wait; }

@media (max-width: 768px) {
  .gallery-img { height: 260px; }
  .modal-box { border-radius: 20px 20px 0 0; }
}
</style>
