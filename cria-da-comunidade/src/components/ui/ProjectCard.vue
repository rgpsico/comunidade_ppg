<template>
  <div class="proj-card" @click="emit('click')">
    <div class="proj-head">
      <div class="proj-icon">{{ proj.icon }}</div>
      <span class="proj-tag" :style="{ color: proj.color, borderColor: proj.color + '44', background: proj.color + '18' }">
        {{ proj.tag }}
      </span>
    </div>
    <div class="proj-name">{{ proj.name }}</div>
    <div class="proj-desc">{{ proj.desc }}</div>
    <div class="proj-foot">
      <div class="proj-impact">
        <span class="impact-num" :style="{ color: proj.color }">{{ proj.impact }}</span>
        <span class="impact-label">{{ proj.impactLabel }}</span>
      </div>
      <button class="btn-ver" :style="{ color: proj.color, borderColor: proj.color + '55' }" @click.stop="emit('click')">
        Ver →
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Project } from '../../types'
defineProps<{ proj: Project }>()
const emit = defineEmits<{ click: [] }>()
</script>

<style scoped>
.proj-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  padding: 20px;
  cursor: pointer;
  transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.proj-card:hover {
  transform: translateY(-3px);
  border-color: var(--line-strong);
  box-shadow: var(--shadow-card);
}

.proj-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.proj-icon { font-size: 36px; line-height: 1; }
.proj-tag {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  padding: 4px 10px;
  border-radius: 999px;
  border: 1px solid;
  font-weight: 500;
}

.proj-name {
  font-family: var(--display);
  font-weight: 700;
  font-size: 18px;
  letter-spacing: -0.02em;
}
.proj-desc {
  font-size: 12px;
  color: var(--muted);
  line-height: 1.55;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.proj-foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 4px;
}
.proj-impact {
  display: flex;
  flex-direction: column;
}
.impact-num {
  font-family: var(--display);
  font-size: 22px;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.03em;
}
.impact-label {
  font-family: var(--mono);
  font-size: 9px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--muted);
  margin-top: 2px;
}

.btn-ver {
  padding: 7px 14px;
  border-radius: 999px;
  border: 1px solid;
  background: transparent;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
  transition: opacity 0.15s, transform 0.15s;
  flex-shrink: 0;
}
.btn-ver:hover { opacity: 0.75; transform: translateY(-1px); }
</style>
