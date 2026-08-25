<template>
  <!-- Bottom Nav: só visível no mobile -->
  <nav class="bottom-nav">
    <button
      v-for="item in mainItems"
      :key="item.view ?? item.label"
      class="bn-item"
      :class="{ active: item.view && ui.activeView === item.view, cta: item.isCta }"
      @click="item.isCta ? openAnunciar() : item.view && ui.goTo(item.view)"
    >
      <span class="bn-icon" v-html="item.icon"></span>
      <span class="bn-label">{{ item.label }}</span>
    </button>

    <!-- Mais -->
    <button class="bn-item" :class="{ active: showDrawer }" @click="showDrawer = !showDrawer">
      <span class="bn-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="5" cy="5" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="12" cy="5" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="19" cy="5" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="5" cy="12" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="19" cy="12" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="5" cy="19" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="12" cy="19" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="19" cy="19" r="1.5" fill="currentColor" stroke="none"/>
        </svg>
      </span>
      <span class="bn-label">Mais</span>
    </button>
  </nav>

  <!-- Drawer overlay -->
  <Transition name="overlay">
    <div v-if="showDrawer" class="drawer-overlay" @click.self="showDrawer = false">
      <Transition name="drawer" appear>
        <div v-if="showDrawer" class="drawer">
          <div class="drawer-handle"></div>

          <div class="drawer-grid">
            <button
              v-for="item in drawerItems"
              :key="item.view"
              class="drawer-item"
              :class="{ active: ui.activeView === item.view }"
              @click="goToAndClose(item.view)"
            >
              <span class="drawer-icon" v-html="item.icon"></span>
              <span class="drawer-label">{{ item.label }}</span>
            </button>
          </div>

          <div class="drawer-divider"></div>

          <!-- Usuário -->
          <button class="drawer-user" @click="goToAndClose(auth.isAuthenticated ? 'perfil' : 'login')">
            <div class="drawer-av">{{ auth.initials }}</div>
            <div class="drawer-user-info">
              <div class="drawer-user-name">{{ auth.user?.name ?? 'Visitante' }}</div>
              <div class="drawer-user-sub">{{ auth.isAuthenticated ? 'Ver meu perfil →' : 'Entrar na conta →' }}</div>
            </div>
          </button>

          <button class="drawer-anunciar" @click="openAnunciarAndClose">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Anunciar grátis
          </button>
        </div>
      </Transition>
    </div>
  </Transition>

  <AnunciarModal v-if="showAnunciar" @close="showAnunciar = false" />
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
const showDrawer = ref(false)

function openAnunciar() {
  if (!auth.isAuthenticated) { ui.goTo('login'); return }
  showAnunciar.value = true
}

function openAnunciarAndClose() {
  showDrawer.value = false
  if (!auth.isAuthenticated) { ui.goTo('login'); return }
  showAnunciar.value = true
}

function goToAndClose(view: WebView) {
  showDrawer.value = false
  ui.goTo(view)
}

const mainItems: { view?: WebView; label: string; icon: string; isCta?: boolean }[] = [
  {
    view: 'inicio', label: 'Início',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
  },
  {
    view: 'vagas', label: 'Vagas',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>',
  },
  {
    label: '+',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
    isCta: true,
  },
  {
    view: 'curriculos', label: 'Currículo',
    icon: '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
  },
]

const drawerItems: { view: WebView; label: string; icon: string }[] = [
  {
    view: 'profissionais', label: 'Profissionais',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>',
  },
  {
    view: 'eventos', label: 'Eventos',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
  },
  {
    view: 'projetos', label: 'Projetos',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>',
  },
  {
    view: 'chamados', label: 'Chamados',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
  },
  {
    view: 'lojas', label: 'Lojas',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>',
  },
  {
    view: 'artigos', label: 'Artigos',
    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16v2H4zM4 9h10v2H4zM4 14h12v2H4zM4 19h8v2H4z"/></svg>',
  },
]
</script>

<style scoped>
/* ── Bottom nav ── */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: var(--bn-h);
  background: rgba(17, 14, 11, 0.96);
  backdrop-filter: blur(20px);
  border-top: 1px solid var(--line);
  display: none;
  align-items: center;
  justify-content: space-around;
  padding: 0 4px;
  padding-bottom: env(safe-area-inset-bottom);
  z-index: 100;
}

@media (max-width: 768px) {
  .bottom-nav { display: flex; }
}

.bn-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  padding: 6px 10px;
  border-radius: 12px;
  color: var(--muted);
  cursor: pointer;
  transition: color 0.15s;
  flex: 1;
}
.bn-item:hover, .bn-item.active { color: var(--orange); }

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
.bn-item.cta:hover { transform: scale(1.08); }
.bn-item.cta .bn-label { display: none; }

.bn-icon { display: flex; align-items: center; }
.bn-label {
  font-size: 9px;
  font-weight: 600;
  white-space: nowrap;
  letter-spacing: 0.02em;
}

/* ── Drawer overlay ── */
.drawer-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  z-index: 200;
  display: flex;
  align-items: flex-end;
}

.drawer {
  width: 100%;
  background: var(--card);
  border-top: 1px solid var(--line);
  border-radius: 20px 20px 0 0;
  padding: 12px 20px 32px;
  padding-bottom: calc(32px + env(safe-area-inset-bottom));
}

.drawer-handle {
  width: 36px;
  height: 4px;
  background: var(--line);
  border-radius: 2px;
  margin: 0 auto 20px;
}

/* Grid de itens do drawer */
.drawer-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 8px;
  margin-bottom: 20px;
}

.drawer-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 14px 6px;
  border-radius: 14px;
  border: 1px solid var(--line);
  background: var(--bg-2);
  color: var(--muted);
  cursor: pointer;
  transition: background 0.15s, color 0.15s, border-color 0.15s;
}
.drawer-item:hover, .drawer-item.active {
  background: rgba(255,94,26,0.10);
  border-color: rgba(255,94,26,0.25);
  color: var(--orange);
}
.drawer-icon { display: flex; align-items: center; }
.drawer-label { font-size: 10px; font-weight: 600; text-align: center; line-height: 1.2; }

.drawer-divider {
  height: 1px;
  background: var(--line);
  margin: 0 0 16px;
}

/* Usuário no drawer */
.drawer-user {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px 14px;
  border-radius: 14px;
  border: 1px solid var(--line);
  background: var(--bg-2);
  cursor: pointer;
  transition: background 0.15s;
  margin-bottom: 12px;
  text-align: left;
}
.drawer-user:hover { background: rgba(255,94,26,0.06); }

.drawer-av {
  width: 40px; height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: 15px; color: var(--black);
  flex-shrink: 0;
}
.drawer-user-info { flex: 1; }
.drawer-user-name { font-size: 14px; font-weight: 700; color: var(--cream); }
.drawer-user-sub { font-size: 12px; color: var(--muted); margin-top: 1px; }

.drawer-anunciar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 13px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  color: var(--black);
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
  transition: opacity 0.15s;
}
.drawer-anunciar:hover { opacity: 0.9; }

/* ── Transitions ── */
.overlay-enter-active, .overlay-leave-active { transition: opacity 0.25s ease; }
.overlay-enter-from, .overlay-leave-to { opacity: 0; }

.drawer-enter-active { transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1); }
.drawer-leave-active { transition: transform 0.2s ease-in; }
.drawer-enter-from, .drawer-leave-to { transform: translateY(100%); }
</style>
