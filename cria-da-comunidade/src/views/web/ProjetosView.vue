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
    <div class="featured-proj" @click="ui.openProj(data.projects[0])">
      <div class="fp-left" :style="{ background: `linear-gradient(135deg, ${data.projects[0].color}88, ${data.projects[0].color}33)` }">
        <div class="fp-icon">{{ data.projects[0].icon }}</div>
        <div class="fp-badge" :style="{ color: data.projects[0].color, borderColor: data.projects[0].color + '44', background: data.projects[0].color + '18' }">
          campanha ativa · {{ data.projects[0].progress }}%
        </div>
      </div>
      <div class="fp-right">
        <div class="fp-tag">
          <span :style="{ color: data.projects[0].color }">{{ data.projects[0].tag }}</span>
          · {{ data.projects[0].yearsActive }} anos atuando
        </div>
        <h2 class="fp-title display">{{ data.projects[0].name }}</h2>
        <p class="fp-desc">{{ data.projects[0].desc }}</p>
        <div class="fp-progress">
          <div class="fp-amounts">
            <span class="fp-raised" :style="{ color: data.projects[0].color }">{{ data.projects[0].raised }}</span>
            <span class="fp-goal">de {{ data.projects[0].goal }}</span>
          </div>
          <div class="prog-bar">
            <div class="prog-fill" :style="{ width: data.projects[0].progress + '%' }"></div>
          </div>
          <div class="fp-pmeta">
            237 apoiadores · 12 dias restantes · 180 alunos
          </div>
        </div>
        <div class="fp-actions">
          <button class="btn-support" :style="{ background: data.projects[0].color }" @click.stop="ui.openProj(data.projects[0])">
            Apoiar agora
          </button>
          <button class="ghost-btn" @click.stop>Saber mais</button>
        </div>
      </div>
    </div>

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

const impactStats = [
  { value: '124', label: 'projetos ativos', trend: '8% este mês', gradient: null },
  { value: '18.4k', label: 'vidas impactadas', trend: '12% este ano', gradient: 'linear-gradient(135deg, #2BD96B, #FFD23F)' },
  { value: 'R$ 2.4M', label: 'arrecadados', trend: '18% este ano', gradient: null },
  { value: '847', label: 'voluntários', trend: '5% este mês', gradient: null },
]

const helpCards = [
  { icon: '💰', label: 'Doar', desc: 'Contribua financeiramente para projetos que você acredita.', cta: 'Doar', color: '#FF5E1A' },
  { icon: '🙋', label: 'Voluntariar', desc: 'Doe seu tempo e habilidades para causas da comunidade.', cta: 'Voluntariar', color: '#2BD96B' },
  { icon: '📣', label: 'Divulgar', desc: 'Compartilhe projetos nas suas redes e amplie o alcance.', cta: 'Divulgar', color: '#FFD23F' },
  { icon: '📋', label: 'Cadastrar projeto', desc: 'Tem um projeto social? Cadastra e conecta com apoiadores.', cta: 'Cadastrar', color: '#FF5E1A' },
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

.fp-progress { display: flex; flex-direction: column; gap: 6px; }
.fp-amounts { display: flex; align-items: baseline; gap: 6px; }
.fp-raised { font-family: var(--display); font-size: 26px; font-weight: 800; letter-spacing: -0.03em; }
.fp-goal { font-size: 13px; color: var(--muted); }
.prog-bar { height: 8px; background: var(--card-2); border-radius: 999px; overflow: hidden; }
.prog-fill { height: 100%; background: linear-gradient(90deg, var(--green), var(--yellow)); border-radius: 999px; }
.fp-pmeta { font-size: 11px; color: var(--muted); }

.fp-actions { display: flex; gap: 10px; margin-top: 4px; }
.btn-support {
  padding: 11px 22px; border-radius: 999px;
  color: var(--black); font-weight: 700; font-size: 13px;
  box-shadow: var(--shadow-cta-green);
  transition: opacity 0.15s, transform 0.15s;
}
.btn-support:hover { opacity: 0.85; transform: translateY(-1px); }
.ghost-btn {
  padding: 10px 18px; border-radius: 10px;
  border: 1px solid var(--line); background: transparent;
  color: var(--cream); font-size: 13px; font-weight: 600; cursor: pointer;
  transition: all 0.15s;
}
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card-2); }

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
