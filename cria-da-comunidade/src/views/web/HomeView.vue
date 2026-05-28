<template>
  <div class="home fade-up">
    <!-- Hero -->
    <section class="hero">
      <div class="skyline">
        <div v-for="h in skylineHouses" :key="h.id" class="house" :style="h.style">
          <div v-for="w in h.windows" :key="w" class="window" :style="{ opacity: Math.random() > 0.3 ? 1 : 0.2 }"></div>
        </div>
        <div class="moon"></div>
      </div>
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="hero-tag">
          <span class="tag-dot"></span>
          favela viva
        </div>
        <h1 class="hero-title display">
          Tudo que acontece na <span class="text-gradient-orange">comunidade</span> em um só lugar.
        </h1>
        <p class="hero-sub">Profissionais, eventos, projetos sociais e oportunidades — do Alemão pro mundo.</p>
        <div class="hero-search">
          <svg width="16" height="16" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="text" placeholder="Qual serviço você procura?" />
          <button class="hero-search-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
          </button>
        </div>
      </div>
      <div class="hero-stats">
        <div class="hstat"><span class="hstat-n">12k+</span><span class="hstat-l">moradores</span></div>
        <div class="hstat"><span class="hstat-n">2.4k</span><span class="hstat-l">profissionais</span></div>
        <div class="hstat"><span class="hstat-n">38</span><span class="hstat-l">projetos ativos</span></div>
      </div>
    </section>

    <!-- Quick actions -->
    <section class="section">
      <div class="quick-grid">
        <button v-for="action in quickActions" :key="action.label" class="qa-btn">
          <div class="qa-icon" :style="{ background: action.color + '22' }">{{ action.icon }}</div>
          <span class="qa-label">{{ action.label }}</span>
          <span class="qa-count">{{ action.count }}+ ativos</span>
        </button>
      </div>
    </section>

    <!-- Profissionais -->
    <section class="section">
      <div class="section-head">
        <div>
          <div class="eyebrow">⭐ profissionais</div>
          <h2 class="section-title display">Profissionais perto de você</h2>
        </div>
        <div class="section-actions">
          <div class="tabs-row">
            <button
              v-for="cat in proCategories"
              :key="cat"
              class="tab-btn"
              :class="{ active: activeCat === cat }"
              @click="activeCat = cat"
            >{{ cat }}</button>
          </div>
          <button class="link-btn" @click="ui.goTo('profissionais')">Ver todos →</button>
        </div>
      </div>
      <div v-if="!data.loading" class="pros-grid">
        <ProCard
          v-for="pro in filteredPros.slice(0, 4)"
          :key="pro.id"
          :pro="pro"
          @click="ui.openPro(pro)"
        />
      </div>
    </section>

    <!-- Eventos -->
    <section class="section">
      <div class="section-head">
        <div>
          <div class="eyebrow">🎉 rola na comunidade</div>
          <h2 class="section-title display">Eventos</h2>
        </div>
        <button class="link-btn" @click="ui.goTo('eventos')">Ver todos →</button>
      </div>
      <div v-if="data.events.length > 0" class="events-grid">
        <EventCard
          :event="data.events[0]"
          :featured="true"
          @click="ui.openEvent(data.events[0])"
        />
        <div class="events-col">
          <EventCard
            v-for="ev in data.events.slice(1, 3)"
            :key="ev.id"
            :event="ev"
            @click="ui.openEvent(ev)"
          />
        </div>
      </div>
      <div v-else class="skeleton-row">Carregando eventos...</div>
    </section>

    <!-- Projetos sociais -->
    <section class="section">
      <div class="section-head">
        <div>
          <div class="eyebrow">❤ projetos sociais</div>
          <h2 class="section-title display">Projetos que <span class="text-gradient-green">mudam</span> a quebrada</h2>
        </div>
        <button class="link-btn" @click="ui.goTo('projetos')">Ver todos →</button>
      </div>
      <div v-if="data.projects.length > 0" class="projs-grid">
        <ProjectCard
          v-for="proj in data.projects.slice(0, 3)"
          :key="proj.id"
          :proj="proj"
          @click="ui.openProj(proj)"
        />
      </div>
    </section>

    <!-- Vagas + Feed -->
    <section class="section two-col-section">
      <div class="vagas-col">
        <div class="section-head">
          <div>
            <div class="eyebrow">💼 vagas</div>
            <h2 class="section-title display">Oportunidades</h2>
          </div>
          <button class="link-btn" @click="ui.goTo('vagas')">Ver todas →</button>
        </div>
        <div v-if="data.vagas.length > 0" class="vagas-list">
          <VagaCard
            v-for="vaga in data.vagas.slice(0, 3)"
            :key="vaga.id"
            :vaga="vaga"
            @click="ui.openVaga(vaga)"
          />
        </div>
      </div>
      <div class="feed-col">
        <div class="section-head">
          <div>
            <div class="eyebrow">📸 feed</div>
            <h2 class="section-title display">Da quebrada</h2>
          </div>
        </div>
        <div class="feed-grid">
          <div v-for="(item, i) in feedItems" :key="i" class="feed-item" :class="item.size"
            :style="{ background: `linear-gradient(135deg, ${item.c1}, ${item.c2})` }">
            <div class="feed-overlay"></div>
            <div class="feed-info">
              <div class="feed-author">{{ item.author }}</div>
              <div class="feed-time">{{ item.time }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import ProCard from '../../components/ui/ProCard.vue'
import EventCard from '../../components/ui/EventCard.vue'
import ProjectCard from '../../components/ui/ProjectCard.vue'
import VagaCard from '../../components/ui/VagaCard.vue'

const ui = useUiStore()
const data = useDataStore()

const activeCat = ref('Todos')
const proCategories = ['Todos', 'Beleza', 'Construção', 'Casa', 'Transporte']

const filteredPros = computed(() =>
  activeCat.value === 'Todos'
    ? data.pros
    : data.pros.filter(p => p.category === activeCat.value)
)

const quickActions = [
  { icon: '⚡', label: 'Eletricista', count: '48', color: '#FFD23F' },
  { icon: '🧹', label: 'Diarista', count: '120', color: '#2BD96B' },
  { icon: '✂️', label: 'Barbeiro', count: '35', color: '#FF5E1A' },
  { icon: '🏍️', label: 'Mototáxi', count: '89', color: '#FFD23F' },
  { icon: '💪', label: 'Personal', count: '22', color: '#2BD96B' },
  { icon: '💅', label: 'Manicure', count: '67', color: '#FF5E1A' },
  { icon: '🔨', label: 'Pedreiro', count: '41', color: '#FFD23F' },
  { icon: '🎨', label: 'Designer', count: '18', color: '#2BD96B' },
]

const feedItems = [
  { author: 'MC Bravão', time: '2h', c1: '#FF5E1A', c2: '#FFD23F', size: 'tall' },
  { author: 'Bia Arte', time: '4h', c1: '#2BD96B', c2: '#FFD23F', size: '' },
  { author: 'Karatê Kids', time: '6h', c1: '#FFD23F', c2: '#FF5E1A', size: '' },
  { author: 'ONG Raízes', time: '1d', c1: '#2BD96B', c2: '#FF5E1A', size: 'wide' },
]

const skylineHouses = Array.from({ length: 18 }, (_, i) => ({
  id: i,
  style: {
    left: `${(i / 18) * 100}%`,
    width: `${40 + Math.random() * 40}px`,
    height: `${60 + Math.random() * 100}px`,
    bottom: '0',
    background: `hsl(${20 + Math.random() * 15}, ${15 + Math.random() * 10}%, ${12 + Math.random() * 8}%)`,
  },
  windows: Array.from({ length: Math.floor(Math.random() * 6) + 2 }, (_, j) => j),
}))
</script>

<style scoped>
.home { padding: 28px 32px; max-width: 1480px; }

/* Hero */
.hero {
  height: 360px;
  border-radius: var(--radius-2xl);
  overflow: hidden;
  position: relative;
  margin-bottom: 32px;
  background: #0a0806;
}
.skyline {
  position: absolute;
  inset: 0;
  overflow: hidden;
}
.house {
  position: absolute;
  border-radius: 4px 4px 0 0;
  display: flex;
  flex-wrap: wrap;
  align-content: flex-start;
  padding: 8px 4px;
  gap: 3px;
}
.window {
  width: 6px; height: 7px;
  background: rgba(255, 210, 63, 0.6);
  border-radius: 1px;
}
.moon {
  position: absolute;
  top: 32px; right: 80px;
  width: 48px; height: 48px;
  background: radial-gradient(circle at 40% 40%, #FFD23F, #F0B81B);
  border-radius: 50%;
  box-shadow: 0 0 30px rgba(255,210,63,0.3);
}
.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.8) 100%);
}
.hero-content {
  position: absolute;
  bottom: 60px; left: 40px;
  right: 200px;
  z-index: 2;
}
.hero-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  background: rgba(0,0,0,0.5);
  border: 1px solid rgba(255,94,26,0.3);
  border-radius: 999px;
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--orange);
  margin-bottom: 14px;
}
.tag-dot {
  width: 6px; height: 6px;
  background: var(--orange);
  border-radius: 50%;
  animation: pulse-dot 1.6s ease-out infinite;
  color: var(--orange);
}
.hero-title {
  font-size: clamp(28px, 3.5vw, 52px);
  font-weight: 800;
  color: white;
  margin-bottom: 10px;
  line-height: 1.05;
}
.hero-sub { font-size: 15px; color: rgba(255,255,255,0.7); margin-bottom: 20px; }
.hero-search {
  display: flex;
  align-items: center;
  gap: 10px;
  max-width: 480px;
  padding: 0 14px;
  height: 46px;
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 12px;
  transition: border-color 0.2s;
}
.hero-search:focus-within { border-color: var(--orange); }
.hero-search input {
  flex: 1; background: none; border: none; outline: none;
  color: white; font-size: 13px;
}
.hero-search input::placeholder { color: rgba(255,255,255,0.5); }
.hero-search-btn {
  width: 30px; height: 30px;
  background: var(--orange);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: white;
  flex-shrink: 0;
}
.hero-stats {
  position: absolute;
  bottom: 24px; right: 28px;
  display: flex; gap: 20px;
  z-index: 2;
}
.hstat { display: flex; flex-direction: column; align-items: flex-end; }
.hstat-n {
  font-family: var(--display);
  font-weight: 800;
  font-size: 22px;
  letter-spacing: -0.03em;
  color: white;
}
.hstat-l { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.5); }

/* Sections */
.section { margin-bottom: 40px; }
.section-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 18px;
  flex-wrap: wrap;
  gap: 12px;
}
.section-title {
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.03em;
  margin-top: 4px;
}
.section-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.tabs-row { display: flex; gap: 6px; flex-wrap: wrap; }
.tab-btn {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid var(--line);
  background: transparent;
  color: var(--muted);
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
}
.tab-btn:hover { border-color: var(--line-strong); color: var(--cream); }
.tab-btn.active { background: rgba(255,94,26,0.12); border-color: rgba(255,94,26,0.3); color: var(--orange); }

.link-btn {
  font-size: 12px;
  font-weight: 600;
  color: var(--orange);
  white-space: nowrap;
}
.link-btn:hover { color: var(--yellow); }

/* Grids */
.quick-grid {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  gap: 8px;
}
.qa-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 14px 8px;
  border-radius: var(--radius-md);
  border: 1px solid var(--line);
  background: var(--card);
  cursor: pointer;
  gap: 6px;
  transition: border-color 0.15s, transform 0.15s, background 0.15s;
}
.qa-btn:hover { border-color: var(--line-strong); background: var(--card-2); transform: translateY(-2px); }
.qa-icon { font-size: 24px; }
.qa-label { font-size: 11px; font-weight: 600; text-align: center; }
.qa-count { font-family: var(--mono); font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; }

.pros-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }

.events-grid {
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 12px;
}
.events-col { display: flex; flex-direction: column; gap: 12px; }

.projs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }

.two-col-section { display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; }
.vagas-list { display: flex; flex-direction: column; gap: 10px; }

.feed-grid { display: grid; grid-template-columns: 1fr 1fr; grid-auto-rows: 100px; gap: 8px; }
.feed-item {
  border-radius: var(--radius-md);
  position: relative;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s;
}
.feed-item:hover { transform: scale(1.02); }
.feed-item.tall { grid-row: span 2; }
.feed-item.wide { grid-column: span 2; }
.feed-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
}
.feed-info {
  position: absolute; bottom: 8px; left: 10px; z-index: 1;
}
.feed-author { font-size: 11px; font-weight: 600; color: white; }
.feed-time { font-family: var(--mono); font-size: 9px; color: rgba(255,255,255,0.6); text-transform: uppercase; }

.skeleton-row { color: var(--muted); font-size: 13px; padding: 20px 0; }

@media (max-width: 768px) {
  .home { padding: 16px; }

  .hero { height: 260px; margin-bottom: 20px; }
  .hero-content { left: 20px; right: 20px; bottom: 20px; }
  .hero-title { font-size: 22px; }
  .hero-sub { font-size: 13px; display: none; }
  .hero-search { max-width: 100%; }
  .hero-stats { display: none; }

  .section { margin-bottom: 28px; }
  .section-title { font-size: 20px; }
  .section-actions .tabs-row { display: none; }

  .quick-grid { grid-template-columns: repeat(4, 1fr); gap: 6px; }
  .qa-btn { padding: 10px 4px; }
  .qa-icon { font-size: 20px; }

  .pros-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }

  .events-grid { grid-template-columns: 1fr; }
  .events-col { display: none; }

  .projs-grid { grid-template-columns: 1fr; }

  .two-col-section { grid-template-columns: 1fr; gap: 28px; }
  .feed-col { display: none; }
}
</style>
