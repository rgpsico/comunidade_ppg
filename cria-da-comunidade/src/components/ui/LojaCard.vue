<template>
  <div class="loja-card" @click="$emit('click')">
    <!-- Capa -->
    <div class="loja-capa" :style="capaStyle">
      <div class="capa-overlay"></div>
      <div class="capa-badges">
        <span class="badge-cat">{{ loja.categoria }}</span>
        <span v-if="loja.verificado" class="badge-verified">✓ Verificada</span>
      </div>
      <!-- Logo flutuando sobre a capa -->
      <div class="loja-logo-wrap">
        <img :src="loja.logoUrl" :alt="loja.nome" class="loja-logo" />
      </div>
    </div>

    <!-- Info principal -->
    <div class="loja-info">
      <div class="loja-nome">{{ loja.nome }}</div>
      <div v-if="loja.descricao" class="loja-desc">{{ loja.descricao }}</div>
      <div class="loja-meta">
        <span v-if="loja.endereco" class="meta-item">
          <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
          </svg>
          {{ loja.endereco }}
        </span>
        <span class="meta-item meta-prod" :style="{ color: loja.cor1 }">
          <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
          </svg>
          {{ loja.produtosCount }} produto{{ loja.produtosCount !== 1 ? 's' : '' }}
        </span>
      </div>
    </div>

    <!-- Preview de produtos -->
    <div v-if="previewProdutos.length" class="produtos-preview">
      <div v-for="p in previewProdutos" :key="p.id" class="pp-item">
        <div class="pp-foto">
          <img v-if="p.imagemPrincipalUrl" :src="p.imagemPrincipalUrl" :alt="p.nome" />
          <div v-else class="pp-placeholder" :style="{ background: `linear-gradient(135deg, ${loja.cor1}22, ${loja.cor2}22)` }">📦</div>
        </div>
        <div class="pp-info">
          <div class="pp-nome">{{ p.nome }}</div>
          <div class="pp-preco">
            <span v-if="p.precoPromocional" class="pp-de">{{ fmt(p.preco) }}</span>
            <span class="pp-por">{{ fmt(p.precoPromocional ?? p.preco) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Rodapé -->
    <div class="loja-footer">
      <button class="btn-ver" :style="{ background: loja.cor1 }">
        Ver loja →
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Loja } from '../../types'

const props = defineProps<{ loja: Loja }>()
defineEmits<{ click: [] }>()

const previewProdutos = props.loja.produtos?.filter(p => p.disponivel).slice(0, 3) ?? []

function fmt(val: number): string {
  return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const capaStyle = props.loja.capaUrl
  ? { backgroundImage: `url(${props.loja.capaUrl})`, backgroundSize: 'cover', backgroundPosition: 'center' }
  : { background: `linear-gradient(135deg, ${props.loja.cor1}, ${props.loja.cor2})` }
</script>

<style scoped>
.loja-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 18px;
  overflow: hidden;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
}
.loja-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 40px -8px rgba(0,0,0,0.4);
  border-color: rgba(255,94,26,0.3);
}

/* ── Capa ── */
.loja-capa {
  height: 140px;
  position: relative;
  flex-shrink: 0;
}
.capa-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.55) 100%);
}
.capa-badges {
  position: absolute;
  top: 10px;
  left: 12px;
  display: flex;
  gap: 6px;
}
.badge-cat {
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(8px);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.badge-verified {
  background: rgba(43,217,107,0.88);
  color: #000;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
}

/* Logo posicionado no canto inferior da capa */
.loja-logo-wrap {
  position: absolute;
  bottom: -24px;
  left: 16px;
}
.loja-logo {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  border: 3px solid var(--card);
  object-fit: cover;
  background: var(--bg-2);
  display: block;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* ── Info ── */
.loja-info {
  padding: 32px 16px 12px;
}
.loja-nome {
  font-size: 16px;
  font-weight: 800;
  color: var(--cream);
  letter-spacing: -0.01em;
  margin-bottom: 5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.loja-desc {
  font-size: 12px;
  color: var(--muted);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 8px;
}
.loja-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: var(--muted);
}
.meta-prod { font-weight: 700; }

/* ── Produtos preview ── */
.produtos-preview {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 0 16px 8px;
  border-top: 1px solid var(--line);
  margin-top: 4px;
  padding-top: 12px;
}
.pp-item {
  display: flex;
  align-items: center;
  gap: 10px;
}
.pp-foto {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  background: var(--bg-2);
}
.pp-foto img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.pp-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}
.pp-info {
  flex: 1;
  min-width: 0;
}
.pp-nome {
  font-size: 12px;
  font-weight: 600;
  color: var(--cream);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.pp-preco {
  display: flex;
  align-items: baseline;
  gap: 5px;
  margin-top: 2px;
}
.pp-de {
  font-size: 10px;
  color: var(--muted);
  text-decoration: line-through;
}
.pp-por {
  font-size: 13px;
  color: var(--green);
  font-weight: 700;
}

/* ── Footer ── */
.loja-footer {
  display: flex;
  gap: 8px;
  padding: 12px 16px 16px;
  margin-top: auto;
  border-top: 1px solid var(--line);
}
.btn-ver {
  flex: 1;
  padding: 10px 0;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 700;
  color: #fff;
  cursor: pointer;
  transition: filter 0.15s;
  letter-spacing: 0.01em;
}
.btn-ver:hover { filter: brightness(1.1); }

</style>
