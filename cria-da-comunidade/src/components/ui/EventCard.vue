<template>
  <div class="event-card" :class="{ featured }" @click="emit('click')">
    <div class="card-bg" :style="{ background: `linear-gradient(135deg, ${event.c1}, ${event.c2})` }">
      <img v-if="event.imagemCapaUrl" :src="event.imagemCapaUrl" class="card-cover-img" :alt="event.title" />
      <div class="card-overlay"></div>
      <div class="card-date-pill">
        <span class="card-day">{{ event.day }}</span>
        <span class="card-month">{{ event.month }}</span>
      </div>
      <div class="card-cat-pill">
        <span class="cat-dot"></span>
        {{ event.cat }}
      </div>
      <div class="card-bottom">
        <div class="card-title">{{ event.title }}</div>
        <div class="card-meta">
          <span>📍 {{ event.place }}</span>
          <span>👥 {{ event.going }} vão</span>
          <span v-if="event.free" class="free-tag">Grátis</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Event } from '../../types'
defineProps<{ event: Event; featured?: boolean }>()
const emit = defineEmits<{ click: [] }>()
</script>

<style scoped>
.event-card {
  border-radius: var(--radius-xl);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  min-height: 200px;
}
.event-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-card);
}
.event-card.featured { min-height: 240px; }

.card-bg {
  position: relative;
  width: 100%; height: 100%;
  min-height: inherit;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 14px;
}
.card-cover-img {
  position: absolute;
  inset: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  z-index: 0;
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);
  pointer-events: none;
}

.card-date-pill {
  position: absolute;
  top: 12px; left: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 6px 10px;
  background: rgba(0,0,0,0.5);
  border-radius: 10px;
  backdrop-filter: blur(8px);
}
.card-day {
  font-family: var(--display);
  font-size: 22px;
  font-weight: 800;
  line-height: 1;
  color: white;
}
.card-month {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--yellow);
}

.card-cat-pill {
  position: absolute;
  top: 12px; right: 12px;
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  background: rgba(0,0,0,0.5);
  border-radius: 999px;
  font-size: 10px;
  font-weight: 600;
  color: white;
  text-transform: capitalize;
  backdrop-filter: blur(8px);
}
.cat-dot {
  width: 5px; height: 5px;
  background: var(--orange);
  border-radius: 50%;
  animation: pulse-dot 1.6s ease-out infinite;
  color: var(--orange);
}

.card-bottom { position: relative; z-index: 1; }
.card-title {
  font-family: var(--display);
  font-weight: 700;
  font-size: 15px;
  letter-spacing: -0.02em;
  color: white;
  margin-bottom: 6px;
  line-height: 1.2;
}
.card-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: rgba(255,255,255,0.8);
  flex-wrap: wrap;
}
.free-tag {
  padding: 2px 8px;
  border-radius: 999px;
  background: var(--green);
  color: var(--black);
  font-weight: 700;
  font-size: 10px;
}
</style>
