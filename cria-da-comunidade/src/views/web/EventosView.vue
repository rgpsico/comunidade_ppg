<template>
  <div class="eventos-page fade-up">
    <div class="page-head">
      <div>
        <div class="eyebrow">🎉 eventos</div>
        <h1 class="page-title display">Rola na <span class="text-gradient-orange">comunidade</span></h1>
        <p class="page-sub">62 eventos · Novembro 2026 · Complexo do Alemão e arredores</p>
      </div>
      <button class="ghost-btn">+ Criar evento</button>
    </div>

    <!-- Evento em destaque -->
    <div class="featured-event">
      <div class="fe-bg" :style="{ background: `linear-gradient(135deg, ${data.events[0].c1}, ${data.events[0].c2})` }">
        <div class="fe-pattern"></div>
        <div class="fe-overlay"></div>
      </div>
      <div class="fe-content">
        <div class="fe-left">
          <div class="fe-tag">
            <span class="tag-dot"></span>
            destaque · sex 22h
          </div>
          <h2 class="fe-title display">{{ data.events[0].title }}</h2>
          <p class="fe-desc">{{ data.events[0].description.slice(0, 140) }}...</p>
          <div class="fe-meta">
            <span>🕒 {{ data.events[0].time }}</span>
            <span>📍 {{ data.events[0].place }}</span>
            <span>👥 {{ data.events[0].going }} confirmados</span>
            <span class="free-tag" v-if="data.events[0].free">Entrada gratuita</span>
          </div>
          <div class="fe-actions">
            <button class="btn-rsvp" @click="ui.openEvent(data.events[0])">Confirmar presença</button>
            <button class="ghost-btn small">Compartilhar</button>
          </div>
        </div>
        <div class="fe-right">
          <div class="fe-date-card">
            <div class="fdc-day">{{ data.events[0].day }}</div>
            <div class="fdc-month">{{ data.events[0].month }}</div>
            <div class="fdc-dow">sexta-feira</div>
          </div>
          <div class="fe-going">
            <div class="fg-count">+{{ data.events[0].going }}</div>
            <div class="fg-label">confirmados</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Calendar strip -->
    <div class="calendar-strip">
      <div
        v-for="(day, i) in calendarDays"
        :key="i"
        class="cal-day"
        :class="{ today: day.today, weekend: day.weekend }"
      >
        <div class="cal-dow">{{ day.dow }}</div>
        <div class="cal-num">{{ day.num }}</div>
        <div class="cal-dots">
          <span v-for="(d, j) in day.dots" :key="j" class="cal-dot" :style="{ background: d }"></span>
        </div>
        <div class="cal-count">{{ day.count }} eventos</div>
      </div>
    </div>

    <!-- Categoria chips -->
    <div class="cat-chips">
      <button
        v-for="cat in eventCats"
        :key="cat.label"
        class="cat-chip"
        :class="{ active: ui.eventsFilters.catActive === cat.label }"
        @click="ui.eventsFilters.catActive = cat.label"
      >
        <span class="cat-icon" :style="{ background: cat.color + '22' }">{{ cat.icon }}</span>
        {{ cat.label }}
      </button>
    </div>

    <div class="section-head">
      <h3 class="section-title display">Próximos eventos</h3>
    </div>

    <div class="events-grid">
      <div
        v-for="(ev, i) in filteredEvents"
        :key="ev.id"
        :class="['event-cell', { span2: i === 0 }]"
      >
        <EventCard :event="ev" :featured="i === 0" @click="ui.openEvent(ev)" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import EventCard from '../../components/ui/EventCard.vue'

const ui = useUiStore()
const data = useDataStore()

const eventCats = [
  { label: 'Todos', icon: '🎉', color: '#FF5E1A' },
  { label: 'baile', icon: '🎵', color: '#FF5E1A' },
  { label: 'pagode', icon: '🥁', color: '#FFD23F' },
  { label: 'esporte', icon: '⚽', color: '#2BD96B' },
  { label: 'cultura', icon: '🎭', color: '#FFD23F' },
  { label: 'festa', icon: '🎊', color: '#FF5E1A' },
  { label: 'workshop', icon: '📚', color: '#2BD96B' },
]

const filteredEvents = computed(() =>
  ui.eventsFilters.catActive === 'Todos'
    ? data.events
    : data.events.filter(e => e.cat === ui.eventsFilters.catActive)
)

const calendarDays = [
  { dow: 'DOM', num: '17', today: false, weekend: true, dots: ['#FFD23F'], count: 2 },
  { dow: 'SEG', num: '18', today: false, weekend: false, dots: ['#2BD96B'], count: 3 },
  { dow: 'TER', num: '19', today: false, weekend: false, dots: ['#2BD96B', '#FFD23F'], count: 4 },
  { dow: 'QUA', num: '20', today: false, weekend: false, dots: [], count: 1 },
  { dow: 'QUI', num: '21', today: false, weekend: false, dots: ['#FF5E1A'], count: 2 },
  { dow: 'SEX', num: '22', today: true, weekend: false, dots: ['#FF5E1A', '#FFD23F', '#2BD96B'], count: 6 },
  { dow: 'SÁB', num: '23', today: false, weekend: true, dots: ['#FF5E1A', '#2BD96B'], count: 5 },
]
</script>

<style scoped>
.eventos-page { padding: 28px 32px; max-width: 1480px; }

.page-head {
  display: flex; align-items: flex-end; justify-content: space-between;
  margin-bottom: 24px; flex-wrap: wrap; gap: 16px;
}
.page-title { font-size: clamp(28px, 3.5vw, 48px); font-weight: 800; margin: 6px 0 4px; }
.page-sub { font-size: 14px; color: var(--muted); }

.ghost-btn {
  padding: 10px 18px;
  border-radius: 10px;
  border: 1px solid var(--line);
  background: transparent;
  color: var(--cream);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card); }
.ghost-btn.small { padding: 8px 14px; font-size: 12px; }

.featured-event {
  position: relative;
  height: 300px;
  border-radius: var(--radius-2xl);
  overflow: hidden;
  margin-bottom: 20px;
}
.fe-bg {
  position: absolute; inset: 0;
}
.fe-pattern {
  position: absolute; inset: 0;
  background: repeating-linear-gradient(45deg, rgba(0,0,0,0.1) 0, rgba(0,0,0,0.1) 1px, transparent 0, transparent 50%);
  background-size: 12px 12px;
}
.fe-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.1) 70%);
}
.fe-content {
  position: relative; z-index: 2;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 32px 40px;
}
.fe-left { max-width: 55%; }
.fe-tag {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px;
  background: rgba(0,0,0,0.4);
  border: 1px solid rgba(255,94,26,0.3);
  border-radius: 999px;
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--orange);
  margin-bottom: 12px;
  backdrop-filter: blur(8px);
}
.tag-dot {
  width: 6px; height: 6px;
  background: var(--orange);
  border-radius: 50%;
  animation: pulse-dot 1.6s ease-out infinite;
  color: var(--orange);
}
.fe-title { font-size: 36px; font-weight: 800; color: white; margin-bottom: 10px; line-height: 1.1; }
.fe-desc { font-size: 13px; color: rgba(255,255,255,0.75); margin-bottom: 14px; line-height: 1.5; }
.fe-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: rgba(255,255,255,0.75); margin-bottom: 18px; }
.free-tag { padding: 3px 10px; border-radius: 999px; background: var(--green); color: var(--black); font-weight: 700; font-size: 11px; }
.fe-actions { display: flex; gap: 10px; }
.btn-rsvp {
  padding: 10px 20px;
  border-radius: 10px;
  background: var(--cream);
  color: var(--black);
  font-weight: 700;
  font-size: 13px;
  transition: background 0.15s, transform 0.15s;
}
.btn-rsvp:hover { background: white; transform: translateY(-1px); }

.fe-right { display: flex; flex-direction: column; align-items: flex-end; gap: 16px; }
.fe-date-card {
  padding: 16px 20px;
  background: rgba(0,0,0,0.5);
  border-radius: 16px;
  text-align: center;
  backdrop-filter: blur(8px);
}
.fdc-day { font-family: var(--display); font-size: 52px; font-weight: 800; color: white; line-height: 1; }
.fdc-month { font-family: var(--mono); font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--yellow); }
.fdc-dow { font-size: 11px; color: rgba(255,255,255,0.6); margin-top: 4px; }
.fe-going {
  padding: 10px 16px;
  background: rgba(0,0,0,0.4);
  border-radius: 12px;
  text-align: right;
  backdrop-filter: blur(8px);
}
.fg-count { font-family: var(--display); font-size: 24px; font-weight: 800; color: white; }
.fg-label { font-size: 11px; color: rgba(255,255,255,0.6); }

.calendar-strip {
  display: grid; grid-template-columns: repeat(7, 1fr);
  gap: 6px; margin-bottom: 20px;
}
.cal-day {
  padding: 12px 8px;
  border-radius: var(--radius-md);
  border: 1px solid var(--line);
  background: var(--card);
  display: flex; flex-direction: column; align-items: center; gap: 4px;
  cursor: pointer; transition: all 0.15s;
}
.cal-day:hover { border-color: var(--line-strong); background: var(--card-2); }
.cal-day.today { background: linear-gradient(135deg, rgba(255,94,26,0.2), rgba(255,210,63,0.1)); border-color: rgba(255,94,26,0.3); }
.cal-day.weekend { background: rgba(255,94,26,0.04); }
.cal-dow { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); }
.cal-num { font-family: var(--display); font-size: 22px; font-weight: 800; letter-spacing: -0.03em; }
.cal-dots { display: flex; gap: 3px; }
.cal-dot { width: 5px; height: 5px; border-radius: 50%; }
.cal-count { font-family: var(--mono); font-size: 9px; text-transform: uppercase; color: var(--muted-2); letter-spacing: 0.06em; }

.cat-chips { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 20px; }
.cat-chip {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 14px; border-radius: 999px;
  border: 1px solid var(--line); background: var(--card);
  white-space: nowrap; cursor: pointer;
  font-size: 13px; font-weight: 500; color: var(--muted);
  transition: all 0.15s; flex-shrink: 0;
}
.cat-chip:hover { border-color: var(--line-strong); color: var(--cream); }
.cat-chip.active { background: rgba(255,94,26,0.10); border-color: rgba(255,94,26,0.3); color: var(--orange); }
.cat-icon { font-size: 15px; }

.section-head { margin-bottom: 16px; }
.section-title { font-size: 24px; font-weight: 800; letter-spacing: -0.03em; }

.events-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-auto-rows: 200px;
  gap: 12px;
  padding-bottom: 40px;
}
.event-cell { min-height: 200px; }
.event-cell.span2 { grid-column: span 2; }

@media (max-width: 768px) {
  .eventos-page { padding: 16px; }
  .featured-event { height: 220px; }
  .fe-content { padding: 20px; flex-direction: column; align-items: flex-start; gap: 12px; }
  .fe-left { max-width: 100%; }
  .fe-right { display: none; }
  .days-grid { grid-template-columns: repeat(4, 1fr); gap: 8px; }
  .events-masonry { grid-template-columns: 1fr; }
  .event-cell.span2 { grid-column: span 1; }
  .filter-bar { overflow-x: auto; flex-wrap: nowrap; }
}
</style>
