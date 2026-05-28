<template>
  <div class="vaga-card" :class="{ urgent: vaga.urgent }" @click="emit('click')">
    <div class="vaga-logo" :style="vaga.logoImagemUrl ? {} : { background: vaga.logoColor }">
      <img v-if="vaga.logoImagemUrl" :src="vaga.logoImagemUrl" class="logo-img" :alt="vaga.company" />
      <span v-else>{{ vaga.logoInitials }}</span>
    </div>
    <div class="vaga-main">
      <div class="vaga-flags">
        <span v-if="vaga.urgent" class="flag urgent-flag">urgente</span>
        <span v-if="vaga.isNew" class="flag new-flag">novo</span>
        <span class="flag type-flag">{{ vaga.type }}</span>
      </div>
      <div class="vaga-title">{{ vaga.title }}</div>
      <div class="vaga-company">{{ vaga.company }} · {{ vaga.place }} · postado {{ vaga.posted }}</div>
      <div class="vaga-desc">{{ vaga.desc }}</div>
    </div>
    <div class="vaga-right">
      <div class="vaga-pay">{{ vaga.pay }}</div>
      <div class="vaga-per">{{ vaga.per }}</div>
      <div class="vaga-applicants">{{ vaga.applicants }} candidatos</div>
      <button class="btn-apply" @click.stop="emit('click')">Candidatar →</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Vaga } from '../../types'
defineProps<{ vaga: Vaga }>()
const emit = defineEmits<{ click: [] }>()
</script>

<style scoped>
.vaga-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  padding: 18px 20px;
  display: grid;
  grid-template-columns: 52px 1fr auto;
  gap: 14px;
  cursor: pointer;
  transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
  align-items: start;
}
.vaga-card:hover {
  transform: translateY(-2px);
  border-color: var(--line-strong);
  box-shadow: var(--shadow-card);
}
.vaga-card.urgent {
  border-color: rgba(255,94,26,0.3);
  border-left: 3px solid var(--orange);
}

.vaga-logo {
  width: 52px; height: 52px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display);
  font-weight: 800;
  font-size: 18px;
  color: var(--black);
  flex-shrink: 0;
  overflow: hidden;
}
.logo-img {
  width: 100%; height: 100%;
  object-fit: cover;
}

.vaga-flags {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  margin-bottom: 6px;
}
.flag {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 3px 8px;
  border-radius: 999px;
  font-weight: 500;
}
.urgent-flag { background: rgba(255,94,26,0.15); color: var(--orange); border: 1px solid rgba(255,94,26,0.3); }
.new-flag { background: rgba(43,217,107,0.15); color: var(--green); border: 1px solid rgba(43,217,107,0.3); }
.type-flag { background: var(--card-2); color: var(--muted); border: 1px solid var(--line); }

.vaga-title {
  font-family: var(--display);
  font-weight: 700;
  font-size: 16px;
  letter-spacing: -0.02em;
  margin-bottom: 3px;
}
.vaga-company { font-size: 11px; color: var(--muted); margin-bottom: 6px; }
.vaga-desc {
  font-size: 12px;
  color: var(--muted);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.vaga-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
  flex-shrink: 0;
}
.vaga-pay {
  font-family: var(--display);
  font-size: 20px;
  font-weight: 800;
  color: var(--green);
  letter-spacing: -0.02em;
}
.vaga-per { font-size: 10px; color: var(--muted); }
.vaga-applicants {
  font-family: var(--mono);
  font-size: 10px;
  color: var(--muted-2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.btn-apply {
  margin-top: 6px;
  padding: 7px 14px;
  border-radius: 999px;
  background: var(--orange);
  color: white;
  font-weight: 700;
  font-size: 11px;
  box-shadow: var(--shadow-cta-orange);
  transition: background 0.15s, transform 0.15s;
}
.btn-apply:hover { background: var(--orange-deep); transform: translateY(-1px); }
</style>
