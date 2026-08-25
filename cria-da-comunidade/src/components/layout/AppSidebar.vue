<template>
  <aside class="sidebar">
    <div class="logo">
      <div class="mark"></div>
      <div>
        <div class="name">Cria da Comunidade</div>
        <div class="tag">beta · v0.9</div>
      </div>
    </div>

    <nav class="nav-group">
      <div class="nav-label">Explorar</div>
      <div
        v-for="item in exploreItems"
        :key="item.view"
        class="nav-item"
        :class="{ active: ui.activeView === item.view }"
        @click="ui.goTo(item.view)"
      >
        <span class="nav-icon" v-html="item.icon"></span>
        <span>{{ item.label }}</span>
        <span v-if="item.count" class="nav-count">{{ item.count }}</span>
        <span v-if="item.live" class="live-dot"></span>
      </div>
    </nav>

    <nav class="nav-group">
      <div class="nav-label">Você</div>
      <div
        v-for="item in userItems"
        :key="item.label"
        class="nav-item"
        :class="{ active: ui.activeView === item.view }"
        @click="item.view && ui.goTo(item.view)"
      >
        <span class="nav-icon" v-html="item.icon"></span>
        <span>{{ item.label }}</span>
        <span v-if="item.count" class="nav-count">{{ item.count }}</span>
      </div>
    </nav>

    <div class="promo-card">
      <div class="promo-eyebrow">Vira parceiro</div>
      <div class="promo-text">Cadastra teu corre e a comunidade te acha em segundos</div>
      <button class="promo-btn" @click="openAnunciar">Anunciar grátis →</button>
    </div>

    <AnunciarModal v-if="showAnunciar" @close="showAnunciar = false" />

    <div class="user-card" @click="onUserCardClick" :class="{ clickable: true }">
      <div class="user-av">{{ auth.initials }}</div>
      <div class="user-info">
        <div class="user-name">{{ auth.user?.name ?? 'Visitante' }}</div>
        <div class="user-role">{{ auth.isAuthenticated ? (auth.user?.roles?.[0]?.name ?? 'usuário') : 'clique para entrar' }}</div>
      </div>
      <button v-if="auth.isAuthenticated" class="logout-btn" @click.stop="doLogout" title="Sair">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </div>
  </aside>
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
  if (!auth.isAuthenticated) {
    ui.goTo('login')
    return
  }
  showAnunciar.value = true
}

function onUserCardClick() {
  if (auth.isAuthenticated) ui.goTo('perfil')
  else ui.goTo('login')
}

async function doLogout() {
  await auth.logout()
  ui.goTo('inicio')
}

const exploreItems: { view: WebView; label: string; icon: string; count?: string; live?: boolean }[] = [
  { view: 'inicio', label: 'Início', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>' },
  { view: 'profissionais', label: 'Profissionais', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>', count: '2.4k' },
  { view: 'eventos', label: 'Eventos', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>', live: true },
  { view: 'projetos', label: 'Projetos sociais', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>' },
  { view: 'chamados', label: 'Chamados', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>' },
  { view: 'vagas', label: 'Vagas', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>', count: '47' },
  { view: 'lojas', label: 'Lojas', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>' },
  { view: 'artigos', label: 'Artigos', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16v2H4zM4 9h10v2H4zM4 14h12v2H4zM4 19h8v2H4z"/></svg>' },
  { view: 'informativos', label: 'Informativos', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 8.5A2.5 2.5 0 0019.5 6h-15A2.5 2.5 0 002 8.5v7A2.5 2.5 0 004.5 18h3l2 3 2-3h8a2.5 2.5 0 002.5-2.5v-7z"/><line x1="8" y1="11" x2="16" y2="11"/><line x1="8" y1="14" x2="12" y2="14"/></svg>' },
  { view: 'curriculos', label: 'Meu Currículo', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>' },
]

const userItems: { view?: WebView; label: string; icon: string; count?: string }[] = [
  { label: 'Mensagens', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>', count: '8' },
  { label: 'Salvos', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>' },
  { label: 'Carteira', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>' },
  { view: 'perfil', label: 'Perfil', icon: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>' },
]
</script>

<style scoped>
.sidebar {
  position: sticky;
  top: 0;
  height: 100vh;
  width: var(--sb-w);
  background: var(--bg-2);
  border-right: 1px solid var(--line);
  display: flex;
  flex-direction: column;
  padding: 20px 16px;
  overflow-y: auto;
  z-index: 50;
  flex-shrink: 0;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 8px 18px;
  margin-bottom: 8px;
}
.logo .mark {
  width: 32px; height: 32px;
  background: var(--orange);
  border-radius: 9px;
  transform: rotate(-4deg);
  box-shadow: 0 6px 16px -4px rgba(255,94,26,0.4);
  position: relative;
  flex-shrink: 0;
}
.logo .mark::before {
  content: "";
  width: 14px; height: 14px;
  background: var(--yellow);
  border-radius: 3px;
  position: absolute;
  transform: rotate(45deg);
  top: 9px; left: 9px;
}
.logo .name {
  font-family: var(--display);
  font-weight: 800;
  font-size: 17px;
  letter-spacing: -0.02em;
  line-height: 1.0;
}
.logo .tag {
  font-family: var(--mono);
  font-size: 9px;
  color: var(--muted);
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-top: 2px;
}

.nav-group { margin-bottom: 18px; }
.nav-label {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.14em;
  color: var(--muted-2);
  padding: 6px 12px 8px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 10px;
  cursor: pointer;
  color: var(--muted);
  font-size: 13px;
  font-weight: 500;
  transition: background 0.15s, color 0.15s;
  position: relative;
  user-select: none;
}
.nav-item:hover { background: rgba(245,240,232,0.04); color: var(--cream); }
.nav-item.active { background: rgba(255,94,26,0.10); color: var(--orange); }
.nav-item.active::before {
  content: "";
  position: absolute;
  left: -16px; top: 14px; bottom: 14px;
  width: 3px;
  background: var(--orange);
  border-radius: 0 3px 3px 0;
}

.nav-icon { display: flex; align-items: center; flex-shrink: 0; }

.nav-count {
  margin-left: auto;
  font-family: var(--mono);
  font-size: 10px;
  color: var(--muted-2);
  letter-spacing: 0.04em;
}

.live-dot {
  width: 6px; height: 6px;
  background: var(--green);
  border-radius: 50%;
  margin-left: auto;
  animation: pulse-dot 1.6s ease-out infinite;
  color: var(--green);
}

.promo-card {
  margin-top: auto;
  background: linear-gradient(135deg, rgba(255,94,26,0.20), rgba(255,210,63,0.10));
  border: 1px solid rgba(255,94,26,0.25);
  border-radius: 14px;
  padding: 16px;
  margin-bottom: 12px;
}
.promo-eyebrow {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--orange);
  margin-bottom: 6px;
}
.promo-text {
  font-size: 12px;
  color: var(--cream);
  line-height: 1.5;
  margin-bottom: 12px;
}
.promo-btn {
  font-size: 12px;
  font-weight: 600;
  color: var(--orange);
  cursor: pointer;
}
.promo-btn:hover { color: var(--yellow); }

.user-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  border-radius: 12px;
  border: 1px solid var(--line);
  background: var(--card);
}
.user-av {
  width: 36px; height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--orange), var(--yellow));
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display);
  font-weight: 800;
  font-size: 14px;
  color: var(--black);
  flex-shrink: 0;
}
@media (max-width: 768px) {
  .sidebar { display: none; }
}

.user-card.clickable { cursor: pointer; transition: background 0.15s; }
.user-card.clickable:hover { background: var(--bg-2); }
.user-name { font-size: 13px; font-weight: 600; }
.user-role { font-size: 11px; color: var(--muted); margin-top: 1px; }
.logout-btn {
  margin-left: auto;
  color: var(--muted);
  display: flex;
  align-items: center;
  padding: 4px;
  border-radius: 6px;
  cursor: pointer;
  transition: color 0.15s, background 0.15s;
  flex-shrink: 0;
}
.logout-btn:hover { color: #ff6b6b; background: rgba(255,107,107,0.1); }
</style>
