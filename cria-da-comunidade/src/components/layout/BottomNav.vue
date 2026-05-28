<template>
  <nav class="bottom-nav">
    <button
      v-for="item in navItems"
      :key="item.view ?? item.label"
      class="bn-item"
      :class="{ active: item.view && ui.activeView === item.view, cta: item.isCta }"
      @click="item.isCta ? openAnunciar() : item.view && ui.goTo(item.view)"
    >
      <span class="bn-icon" v-html="item.icon"></span>
      <span class="bn-label">{{ item.label }}</span>
    </button>

    <AnunciarModal v-if="showAnunciar" @close="showAnunciar = false" />
  </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useAuthStore } from '../../stores/auth'
import AnunciarModal from '../ui/AnunciarModal.vue'
import type { WebView } from '../../types'

const ui = useUiStore()
const auth = useAuthStore()
const showAnunciar = ref(false)

function openAnunciar() {
  if (!auth.isAuthenticated) { ui.goTo('login'); return }
  showAnunciar.value = true
}

const navItems: { view?: WebView; label: string; icon: string; isCta?: boolean }[] = [
  {
    view: 'inicio', label: 'Início',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
  },
  {
    view: 'eventos', label: 'Eventos',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
  },
  {
    label: '+',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
    isCta: true,
  },
  {
    view: 'vagas', label: 'Vagas',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>',
  },
  {
    view: 'profissionais', label: 'Profissionais',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>',
  },
]
</script>

<style scoped>
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: var(--bn-h);
  background: rgba(17, 14, 11, 0.92);
  backdrop-filter: blur(20px);
  border-top: 1px solid var(--line);
  display: none;
  align-items: center;
  justify-content: space-around;
  padding: 0 8px;
  z-index: 100;
  padding-bottom: env(safe-area-inset-bottom);
}

@media (max-width: 768px) {
  .bottom-nav { display: flex; }
}

.bn-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 8px 12px;
  border-radius: 12px;
  color: var(--muted);
  cursor: pointer;
  transition: color 0.15s, background 0.15s;
  flex: 1;
}
.bn-item:hover { color: var(--cream); }
.bn-item.active { color: var(--orange); }

.bn-item.cta {
  background: var(--orange);
  color: var(--black);
  width: 48px;
  height: 48px;
  border-radius: 16px;
  flex: none;
  padding: 0;
  justify-content: center;
  box-shadow: 0 6px 18px -4px rgba(255,94,26,0.6);
  transition: transform 0.15s, box-shadow 0.15s;
}
.bn-item.cta:hover { transform: scale(1.08); box-shadow: 0 8px 22px -4px rgba(255,94,26,0.7); }
.bn-item.cta .bn-label { display: none; }

.bn-icon { display: flex; align-items: center; }
.bn-label { font-size: 10px; font-weight: 500; }
</style>
