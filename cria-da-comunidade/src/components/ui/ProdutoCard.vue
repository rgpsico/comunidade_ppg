<template>
  <div class="produto-card">
    <!-- Imagem -->
    <div class="pc-img-wrap">
      <img
        v-if="produto.imagemPrincipalUrl"
        :src="produto.imagemPrincipalUrl"
        :alt="produto.nome"
        class="pc-img"
      />
      <div v-else class="pc-img-placeholder">📦</div>
      <div v-if="produto.destaque" class="pc-badge-destaque">⭐</div>
      <div v-if="produto.precoPromocional" class="pc-badge-promo">
        -{{ Math.round((1 - produto.precoPromocional / produto.preco) * 100) }}%
      </div>
    </div>

    <!-- Info -->
    <div class="pc-info">
      <div v-if="produto.categoria" class="pc-cat">{{ produto.categoria }}</div>
      <div class="pc-nome">{{ produto.nome }}</div>
      <div class="pc-preco">
        <span v-if="produto.precoPromocional" class="pc-de">{{ fmtMoney(produto.preco) }}</span>
        <span class="pc-por" :style="{ color: cor }">{{ fmtMoney(produto.precoPromocional ?? produto.preco) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Produto } from '../../types'

const props = defineProps<{ produto: Produto; cor: string }>()

function fmtMoney(val: number): string {
  return 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>

<style scoped>
.produto-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
}
.produto-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -6px rgba(0,0,0,0.35);
  border-color: rgba(255,94,26,0.2);
}

.pc-img-wrap {
  position: relative;
  aspect-ratio: 1;
  overflow: hidden;
}
.pc-img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.25s;
}
.produto-card:hover .pc-img {
  transform: scale(1.04);
}
.pc-img-placeholder {
  width: 100%; height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  background: var(--bg-2);
}

.pc-badge-destaque {
  position: absolute;
  top: 6px;
  left: 6px;
  background: rgba(255,210,63,0.9);
  border-radius: 20px;
  font-size: 11px;
  padding: 2px 6px;
}
.pc-badge-promo {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(43,217,107,0.9);
  color: #000;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
}

.pc-info {
  padding: 8px 10px 10px;
}
.pc-cat {
  font-size: 9px;
  font-family: var(--mono);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted-2);
  margin-bottom: 2px;
}
.pc-nome {
  font-size: 12px;
  font-weight: 700;
  color: var(--cream);
  line-height: 1.3;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  margin-bottom: 4px;
}
.pc-preco {
  display: flex;
  flex-direction: column;
  gap: 1px;
}
.pc-de {
  font-size: 10px;
  color: var(--muted);
  text-decoration: line-through;
  line-height: 1;
}
.pc-por {
  font-size: 13px;
  font-weight: 800;
  font-family: var(--display);
  line-height: 1;
}
</style>
