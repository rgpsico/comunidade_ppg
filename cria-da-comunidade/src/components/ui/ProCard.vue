<template>
  <div class="pro-card" :class="{ 'pro-card--premium': pro.isPremium }" @click="emit('click')">
    <div class="pro-photo" :style="{ background: `linear-gradient(135deg, ${pro.c1}, ${pro.c2})` }">
      <img v-if="pro.photoUrl" :src="pro.photoUrl" class="pro-img" :alt="pro.name" />
      <div v-else class="pro-initials">{{ pro.initials }}</div>

      <!-- Badge premium -->
      <div v-if="pro.isPremium" class="badge-premium">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19l2-7 5 3 3-6 3 6 5-3 2 7H2zm0 0h20"/></svg>
        premium
      </div>

      <div v-if="pro.verified" class="badge-verified">
        <svg width="10" height="10" fill="var(--green)" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12" stroke="var(--green)" stroke-width="3" fill="none"/></svg>
        verificado
      </div>
      <div class="pro-price">R$ {{ pro.price }}</div>
    </div>
    <div class="pro-body">
      <div class="pro-name">{{ pro.name }}</div>
      <div class="pro-role">{{ pro.role }}</div>
      <div class="pro-meta">
        <span class="stars">⭐ {{ pro.stars }} ({{ pro.reviews }})</span>
        <span class="dist">📍 {{ pro.dist }}</span>
      </div>
      <div v-if="pro.tags.length" class="pro-tags">
        <span v-for="tag in pro.tags" :key="tag" class="pro-tag">{{ tag }}</span>
      </div>
      <div class="pro-actions">
        <button class="btn-wa" @click.stop>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.852L.067 23.541a.5.5 0 00.625.613l5.872-1.537A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.94 9.94 0 01-5.142-1.428l-.369-.219-3.483.912.929-3.388-.24-.381A9.96 9.96 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/>
          </svg>
          WhatsApp
        </button>
        <button class="btn-profile" @click.stop="emit('click')">Ver perfil</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Pro } from '../../types'
defineProps<{ pro: Pro }>()
const emit = defineEmits<{ click: [] }>()
</script>

<style scoped>
.pro-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
}
.pro-card:hover {
  transform: translateY(-3px);
  border-color: var(--line-strong);
  box-shadow: var(--shadow-card);
}

.pro-photo {
  height: 140px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pro-img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pro-initials {
  font-family: var(--display);
  font-weight: 800;
  font-size: 52px;
  color: rgba(0,0,0,0.55);
  letter-spacing: -0.03em;
  pointer-events: none;
}
/* Premium card */
.pro-card--premium {
  border-color: rgba(201,168,76,0.6);
  box-shadow: 0 0 0 1px rgba(201,168,76,0.25), 0 4px 20px rgba(201,168,76,0.12);
}
.pro-card--premium:hover {
  border-color: rgba(201,168,76,0.9);
  box-shadow: 0 0 0 1px rgba(201,168,76,0.4), 0 8px 32px rgba(201,168,76,0.18);
}
.badge-premium {
  position: absolute;
  top: 10px; right: 10px;
  display: flex; align-items: center; gap: 4px;
  padding: 4px 9px;
  background: linear-gradient(135deg, #C9A84C, #FFD700);
  border-radius: 999px;
  font-size: 10px;
  color: #1a1a2e;
  font-weight: 800;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  box-shadow: 0 2px 8px rgba(201,168,76,0.4);
}

.badge-verified {
  position: absolute;
  top: 10px; left: 10px;
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: rgba(0,0,0,0.6);
  border-radius: 999px;
  font-size: 10px;
  color: var(--green);
  font-weight: 600;
}
.pro-price {
  position: absolute;
  bottom: 10px; right: 10px;
  padding: 4px 10px;
  background: rgba(0,0,0,0.65);
  border-radius: 999px;
  font-family: var(--mono);
  font-size: 11px;
  font-weight: 500;
  color: var(--yellow);
}

.pro-body { padding: 14px 16px; }
.pro-name {
  font-family: var(--display);
  font-weight: 700;
  font-size: 16px;
  letter-spacing: -0.02em;
  margin-bottom: 2px;
}
.pro-role { font-size: 11px; color: var(--muted); margin-bottom: 8px; }
.pro-meta {
  display: flex;
  gap: 10px;
  font-size: 11px;
  color: var(--muted);
  margin-bottom: 8px;
}
.stars { color: var(--cream); }
.pro-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-bottom: 10px;
}
.pro-tag {
  font-size: 10px;
  padding: 3px 8px;
  border-radius: var(--radius-xs);
  background: var(--card-2);
  color: var(--muted);
  border: 1px solid var(--line);
}

.pro-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}
.btn-wa {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 8px;
  border-radius: 10px;
  background: var(--green);
  color: var(--black);
  font-weight: 700;
  font-size: 12px;
  transition: background 0.15s;
}
.btn-wa:hover { background: var(--green-deep); }
.btn-profile {
  padding: 8px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: transparent;
  color: var(--cream);
  font-weight: 600;
  font-size: 12px;
  transition: border-color 0.15s, background 0.15s;
}
.btn-profile:hover { border-color: var(--line-strong); background: var(--card-2); }
</style>
