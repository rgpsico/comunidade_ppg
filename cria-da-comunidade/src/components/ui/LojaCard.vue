<template>
  <div class="loja-card" @click="$emit('click')">
    <!-- Capa -->
    <div class="loja-capa" :style="capaStyle">
      <div class="loja-cat-badge">{{ loja.categoria }}</div>
      <div v-if="loja.verificado" class="loja-verified-badge">✓ Verificada</div>
    </div>

    <!-- Logo + Info -->
    <div class="loja-body">
      <div class="loja-logo-wrap">
        <img :src="loja.logoUrl" :alt="loja.nome" class="loja-logo" />
      </div>

      <div class="loja-info">
        <div class="loja-nome">{{ loja.nome }}</div>
        <div v-if="loja.descricao" class="loja-desc">{{ loja.descricao }}</div>
        <div class="loja-meta">
          <span v-if="loja.endereco" class="loja-meta-item">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ loja.endereco }}
          </span>
          <span class="loja-meta-item loja-prod-count">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            {{ loja.produtosCount }} produto{{ loja.produtosCount !== 1 ? 's' : '' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Produtos preview (primeiros 3) -->
    <div v-if="previewProdutos.length" class="loja-preview-produtos">
      <div
        v-for="p in previewProdutos"
        :key="p.id"
        class="preview-produto"
      >
        <div class="pp-img-wrap">
          <img v-if="p.imagemPrincipalUrl" :src="p.imagemPrincipalUrl" :alt="p.nome" class="pp-img" />
          <div v-else class="pp-img-placeholder" :style="{ background: `linear-gradient(135deg, ${loja.cor1}33, ${loja.cor2}33)` }">
            <span>📦</span>
          </div>
        </div>
        <div class="pp-nome">{{ p.nome }}</div>
        <div class="pp-preco">
          <span v-if="p.precoPromocional" class="pp-de">{{ fmtMoney(p.preco) }}</span>
          <span class="pp-por">{{ fmtMoney(p.precoPromocional ?? p.preco) }}</span>
        </div>
      </div>
    </div>

    <!-- CTA -->
    <div class="loja-footer">
      <button class="btn-ver-loja" :style="{ background: loja.cor1 }">
        Ver loja →
      </button>
      <a
        v-if="loja.whatsapp"
        :href="`https://wa.me/${loja.whatsapp}`"
        target="_blank"
        rel="noopener"
        class="btn-wpp"
        @click.stop
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        WhatsApp
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Loja } from '../../types'

const props = defineProps<{ loja: Loja }>()
defineEmits<{ click: [] }>()

const previewProdutos = props.loja.produtos?.filter(p => p.disponivel).slice(0, 3) ?? []

function fmtMoney(val: number): string {
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
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.18s, box-shadow 0.18s, border-color 0.18s;
  display: flex;
  flex-direction: column;
}
.loja-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 32px -8px rgba(0,0,0,0.35);
  border-color: rgba(255,94,26,0.25);
}

/* Capa */
.loja-capa {
  height: 100px;
  position: relative;
  display: flex;
  align-items: flex-end;
  padding: 8px 10px;
  gap: 6px;
}
.loja-cat-badge {
  background: rgba(0,0,0,0.55);
  backdrop-filter: blur(6px);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 20px;
  letter-spacing: 0.02em;
}
.loja-verified-badge {
  background: rgba(43,217,107,0.85);
  color: #000;
  font-size: 10px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 20px;
  letter-spacing: 0.02em;
}

/* Body */
.loja-body {
  display: flex;
  gap: 12px;
  padding: 0 14px 10px;
  margin-top: -22px;
  align-items: flex-end;
}
.loja-logo-wrap {
  flex-shrink: 0;
}
.loja-logo {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  border: 3px solid var(--card);
  object-fit: cover;
  display: block;
  background: var(--bg-2);
}
.loja-info {
  flex: 1;
  min-width: 0;
  padding-top: 24px;
}
.loja-nome {
  font-size: 14px;
  font-weight: 700;
  color: var(--cream);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.loja-desc {
  font-size: 11px;
  color: var(--muted);
  line-height: 1.4;
  margin-top: 2px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
}
.loja-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 4px;
}
.loja-meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: var(--muted);
}
.loja-prod-count {
  color: var(--orange);
  font-weight: 600;
}

/* Preview produtos */
.loja-preview-produtos {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 6px;
  padding: 0 14px 10px;
}
.preview-produto {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.pp-img-wrap {
  width: 100%;
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
}
.pp-img {
  width: 100%; height: 100%;
  object-fit: cover;
}
.pp-img-placeholder {
  width: 100%; height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}
.pp-nome {
  font-size: 10px;
  color: var(--cream);
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.pp-preco {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.pp-de {
  font-size: 9px;
  color: var(--muted);
  text-decoration: line-through;
  line-height: 1;
}
.pp-por {
  font-size: 11px;
  color: var(--green);
  font-weight: 700;
  line-height: 1;
}

/* Footer */
.loja-footer {
  display: flex;
  gap: 8px;
  padding: 10px 14px 14px;
  margin-top: auto;
  border-top: 1px solid var(--line);
}
.btn-ver-loja {
  flex: 1;
  padding: 8px 0;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  cursor: pointer;
  transition: filter 0.15s;
}
.btn-ver-loja:hover { filter: brightness(1.1); }

.btn-wpp {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 8px 12px;
  border-radius: 10px;
  background: rgba(43,217,107,0.15);
  color: var(--green);
  font-size: 12px;
  font-weight: 600;
  border: 1px solid rgba(43,217,107,0.3);
  transition: background 0.15s;
  text-decoration: none;
}
.btn-wpp:hover { background: rgba(43,217,107,0.25); }
</style>
