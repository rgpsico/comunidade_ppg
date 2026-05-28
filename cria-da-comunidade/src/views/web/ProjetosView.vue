<template>
  <div class="projetos-page fade-up">
    <div class="page-head">
      <div>
        <div class="eyebrow">❤ projetos sociais</div>
        <h1 class="page-title display">Projetos que <span class="text-gradient-green">mudam</span> a comunidade</h1>
      </div>
    </div>

    <!-- Impact banner -->
    <div class="impact-banner">
      <div v-for="stat in impactStats" :key="stat.label" class="impact-cell">
        <div class="imp-num" :style="stat.gradient ? { background: stat.gradient, WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent', backgroundClip: 'text' } : {}">
          {{ stat.value }}
        </div>
        <div class="imp-label">{{ stat.label }}</div>
        <div class="imp-trend">▲ {{ stat.trend }}</div>
      </div>
    </div>

    <!-- Causa chips -->
    <div class="cat-chips">
      <button
        v-for="causa in causas"
        :key="causa.label"
        class="cat-chip"
        :class="{ active: ui.projectsFilters.causaActive === causa.label }"
        @click="ui.projectsFilters.causaActive = causa.label"
      >
        <span class="cat-icon" :style="{ background: causa.color + '22' }">{{ causa.icon }}</span>
        {{ causa.label }}
      </button>
    </div>

    <!-- Featured project -->
    <div v-if="featuredProject" class="featured-proj" @click="ui.openProj(featuredProject)">
      <div class="fp-left" :style="{ background: `linear-gradient(135deg, ${featuredProject.color}88, ${featuredProject.color}33)` }">
        <div class="fp-icon">{{ featuredProject.icon }}</div>
        <div class="fp-badge" :style="{ color: featuredProject.color, borderColor: featuredProject.color + '44', background: featuredProject.color + '18' }">
          {{ featuredProject.tag }} · {{ featuredProject.yearsActive }} anos
        </div>
      </div>
      <div class="fp-right">
        <div class="fp-tag">
          <span :style="{ color: featuredProject.color }">{{ featuredProject.tag }}</span>
          · {{ featuredProject.yearsActive }} anos atuando
        </div>
        <h2 class="fp-title display">{{ featuredProject.name }}</h2>
        <p class="fp-desc">{{ featuredProject.desc }}</p>
        <div class="fp-impact-row">
          <div class="fp-impact-stat">
            <span class="fp-impact-num" :style="{ color: featuredProject.color }">{{ featuredProject.impact }}</span>
            <span class="fp-impact-label">{{ featuredProject.impactLabel }}</span>
          </div>
        </div>
        <div class="fp-actions">
          <button class="btn-ver-proj" :style="{ color: featuredProject.color, borderColor: featuredProject.color + '55' }" @click.stop="ui.openProj(featuredProject)">
            Ver projeto →
          </button>
        </div>
      </div>
    </div>
    <div v-else-if="data.loading" class="featured-proj fp-skeleton"></div>

    <!-- Grid de projetos -->
    <div class="section-head">
      <h3 class="section-title display">Outros projetos</h3>
    </div>

    <div class="projs-grid">
      <ProjectCard
        v-for="proj in filteredProjects.slice(1)"
        :key="proj.id"
        :proj="proj"
        @click="ui.openProj(proj)"
      />
    </div>

    <!-- Help cards -->
    <div class="help-grid">
      <div v-for="h in helpCards" :key="h.label" class="help-card">
        <div class="help-icon" :style="{ background: h.color + '22' }">{{ h.icon }}</div>
        <div class="help-title">{{ h.label }}</div>
        <p class="help-desc">{{ h.desc }}</p>
        <a class="help-link" href="#">{{ h.cta }} →</a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useUiStore } from '../../stores/ui'
import { useDataStore } from '../../stores/data'
import ProjectCard from '../../components/ui/ProjectCard.vue'

const ui = useUiStore()
const data = useDataStore()

const causas = [
  { label: 'Todos', icon: '✊', color: '#FF5E1A' },
  { label: 'educação', icon: '📚', color: '#FF5E1A' },
  { label: 'esporte', icon: '⚽', color: '#2BD96B' },
  { label: 'cultura', icon: '🎭', color: '#FFD23F' },
  { label: 'assistência', icon: '❤', color: '#FF5E1A' },
  { label: 'saúde', icon: '💊', color: '#2BD96B' },
  { label: 'música', icon: '🎵', color: '#FFD23F' },
]

const filteredProjects = computed(() =>
  ui.projectsFilters.causaActive === 'Todos'
    ? data.projects
    : data.projects.filter(p => p.tag === ui.projectsFilters.causaActive)
)

const featuredProject = computed(() => filteredProjects.value[0] ?? null)

const impactStats = [
  { value: '124', label: 'projetos ativos', trend: '8% este mês', gradient: null },
  { value: '18.4k', label: 'vidas impactadas', trend: '12% este ano', gradient: 'linear-gradient(135deg, #2BD96B, #FFD23F)' },
  { value: '847', label: 'voluntários', trend: '5% este mês', gradient: null },
  { value: '320+', label: 'atividades/mês', trend: '10% este mês', gradient: null },
]

const helpCards = [
  { icon: '🙋', label: 'Voluntariar', desc: 'Doe seu tempo e habilidades para causas da comunidade.', cta: 'Voluntariar', color: '#2BD96B' },
  { icon: '📣', label: 'Divulgar', desc: 'Compartilhe projetos nas suas redes e amplie o alcance.', cta: 'Divulgar', color: '#FFD23F' },
  { icon: '🤝', label: 'Parceria', desc: 'Sua empresa pode apoiar projetos sociais com recursos e visibilidade.', cta: 'Parceria', color: '#FF5E1A' },
  { icon: '📋', label: 'Cadastrar projeto', desc: 'Tem um projeto social? Cadastra e conecta com a comunidade.', cta: 'Cadastrar', color: '#FF5E1A' },
]
</script>

<style scoped>
.projetos-page { padding: 28px 32px; max-width: 1480px; }

.page-head { margin-bottom: 24px; }
.page-title { font-size: clamp(28px, 3.5vw, 48px); font-weight: 800; margin: 6px 0; }

.impact-banner {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 0;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  overflow: hidden;
  margin-bottom: 22px;
}
.impact-cell {
  padding: 22px 24px;
  border-right: 1px solid var(--line);
}
.impact-cell:last-child { border-right: none; }
.imp-num {
  font-family: var(--display);
  font-size: 34px;
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1;
  color: var(--cream);
}
.imp-label { font-size: 12px; color: var(--muted); margin: 4px 0 6px; }
.imp-trend { font-family: var(--mono); font-size: 10px; color: var(--green); text-transform: uppercase; letter-spacing: 0.06em; }

.cat-chips { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 22px; }
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

.featured-proj {
  display: grid; grid-template-columns: 1fr 1.4fr;
  min-height: 300px;
  border-radius: var(--radius-2xl);
  overflow: hidden;
  border: 1px solid var(--line);
  margin-bottom: 28px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}
.featured-proj:hover { transform: translateY(-2px); box-shadow: var(--shadow-card); }
.fp-left {
  position: relative;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 16px;
  padding: 32px;
}
.fp-icon { font-size: 90px; line-height: 1; }
.fp-badge {
  padding: 6px 14px;
  border-radius: 999px;
  border: 1px solid;
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 600;
}

.fp-right {
  background: var(--card);
  padding: 32px;
  display: flex; flex-direction: column; gap: 12px;
}
.fp-tag { font-size: 12px; color: var(--muted); }
.fp-title { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; }
.fp-desc { font-size: 13px; color: var(--muted); line-height: 1.6; }

.fp-impact-row { display: flex; align-items: center; gap: 12px; margin-top: 4px; }
.fp-impact-stat { display: flex; flex-direction: column; }
.fp-impact-num { font-family: var(--display); font-size: 32px; font-weight: 800; letter-spacing: -0.03em; line-height: 1; }
.fp-impact-label { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-top: 3px; }

.fp-skeleton { min-height: 280px; background: var(--card); animation: pulse 1.5s ease-in-out infinite; }
@keyframes pulse { 0%,100% { opacity: 1 } 50% { opacity: 0.4 } }

.fp-actions { display: flex; gap: 10px; margin-top: 8px; }
.btn-ver-proj {
  padding: 11px 22px; border-radius: 999px;
  border: 1px solid; background: transparent;
  font-weight: 700; font-size: 13px; cursor: pointer;
  transition: opacity 0.15s, transform 0.15s;
}
.btn-ver-proj:hover { opacity: 0.75; transform: translateY(-1px); }

.section-head { margin-bottom: 16px; }
.section-title { font-size: 24px; font-weight: 800; letter-spacing: -0.03em; }
.projs-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 28px; }

.help-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; padding-bottom: 40px; }
.help-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  padding: 20px;
  display: flex; flex-direction: column; gap: 8px;
}
.help-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.help-title { font-family: var(--display); font-size: 16px; font-weight: 700; }
.help-desc { font-size: 12px; color: var(--muted); line-height: 1.5; }
.help-link { font-size: 12px; color: var(--orange); font-weight: 600; margin-top: auto; }
.help-link:hover { color: var(--yellow); }

@media (max-width: 768px) {
  .projetos-page { padding: 16px; }
  .causa-grid { grid-template-columns: repeat(2, 1fr); }
  .impact-banner { grid-template-columns: 1fr; }
  .projs-grid { grid-template-columns: 1fr; }
  .help-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
