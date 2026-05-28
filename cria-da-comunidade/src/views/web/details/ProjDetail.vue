<template>
  <div class="proj-detail fade-up" v-if="proj">
    <button class="back-btn" @click="ui.goTo('projetos')">← Projetos sociais</button>

    <!-- Hero -->
    <div class="proj-hero" :style="{ background: `linear-gradient(135deg, ${proj.color}88, ${proj.color}22)` }">
      <div class="ph-emoji">{{ proj.icon }}</div>
      <div class="ph-overlay"></div>
      <div class="ph-content">
        <div class="ph-tag" :style="{ color: proj.color }">{{ proj.tag }} · {{ proj.yearsActive }} anos</div>
        <h1 class="ph-title display">{{ proj.name }}</h1>
        <div class="ph-meta">
          <span>📍 Complexo do Alemão</span>
          <span>👥 {{ proj.impact }} {{ proj.impactLabel }}</span>
          <span>🗓 {{ proj.yearsActive }} anos de atuação</span>
        </div>
      </div>
    </div>

    <!-- Stats strip -->
    <div class="stats-strip">
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: proj.color }">{{ proj.impact }}</div>
        <div class="stat-l">{{ proj.impactLabel }}</div>
      </div>
      <div class="stat-sep"></div>
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: proj.color }">{{ proj.yearsActive }}</div>
        <div class="stat-l">anos atuando</div>
      </div>
      <div class="stat-sep"></div>
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: proj.color }">{{ activities.length || '—' }}</div>
        <div class="stat-l">atividades</div>
      </div>
      <div class="stat-sep"></div>
      <div class="stat-cell">
        <div class="stat-n" :style="{ color: proj.color }">{{ tutors.length || '—' }}</div>
        <div class="stat-l">tutores</div>
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-main">
        <!-- Tabs -->
        <div class="tabs">
          <button v-for="tab in tabs" :key="tab" class="tab" :class="{ active: activeTab === tab }" @click="activeTab = tab">{{ tab }}</button>
        </div>

        <!-- Sobre -->
        <div v-if="activeTab === 'Sobre'" class="tab-content">
          <h3 class="content-title">Sobre o projeto</h3>
          <p class="body-text">{{ proj.desc }}</p>

          <h3 class="content-title" style="margin-top: 28px">Atualizações</h3>
          <div v-if="proj.updates.length > 0" class="update-feed">
            <div v-for="u in proj.updates" :key="u.time" class="update-item">
              <div class="update-dot" :style="{ background: proj.color }"></div>
              <div class="update-body">
                <div class="update-time">{{ u.time }}</div>
                <div class="update-title">{{ u.title }}</div>
                <p class="update-text">{{ u.text }}</p>
              </div>
            </div>
          </div>
          <p v-else class="empty-msg">Nenhuma atualização ainda.</p>
        </div>

        <!-- Atividades -->
        <div v-if="activeTab === 'Atividades'" class="tab-content">
          <div v-if="activities.length > 0" class="activities-list">
            <div v-for="act in activities" :key="act.title" class="activity-card">
              <div class="act-color-bar" :style="{ background: proj.color }"></div>
              <div class="act-body">
                <div class="act-header">
                  <div class="act-title">{{ act.title }}</div>
                  <div v-if="act.spots" class="act-spots" :style="{ color: proj.color }">{{ act.spots }} vagas</div>
                </div>
                <div class="act-schedule">
                  <span class="act-days">{{ act.days }}</span>
                  <span class="act-dot">·</span>
                  <span class="act-time">{{ act.time }}</span>
                </div>
                <p v-if="act.desc" class="act-desc">{{ act.desc }}</p>
              </div>
            </div>
          </div>
          <div v-else class="empty-block">
            <div class="empty-icon">🗓</div>
            <p class="empty-msg">Nenhuma atividade cadastrada ainda.</p>
          </div>
        </div>

        <!-- Equipe -->
        <div v-if="activeTab === 'Equipe'" class="tab-content">
          <div v-if="tutors.length > 0" class="tutors-grid">
            <div v-for="tutor in tutors" :key="tutor.name" class="tutor-card">
              <div class="tutor-av" :style="{ background: `linear-gradient(135deg, ${tutor.color}, ${proj.color})` }">
                {{ tutor.initials }}
              </div>
              <div class="tutor-name">{{ tutor.name }}</div>
              <div class="tutor-role">{{ tutor.role }}</div>
              <p v-if="tutor.bio" class="tutor-bio">{{ tutor.bio }}</p>
            </div>
          </div>
          <div v-else class="empty-block">
            <div class="empty-icon">👥</div>
            <p class="empty-msg">Equipe ainda não cadastrada.</p>
          </div>
        </div>

        <!-- Galeria -->
        <div v-if="activeTab === 'Galeria'" class="tab-content">
          <div v-if="gallery.length > 0" class="gallery-grid">
            <div
              v-for="(img, i) in gallery"
              :key="i"
              class="gallery-item"
              :class="{ wide: i === 0, tall: i === 3 }"
              :style="{ background: `linear-gradient(135deg, ${img.color1}, ${img.color2})` }"
            >
              <div class="gallery-overlay"></div>
              <div v-if="img.caption" class="gallery-caption">{{ img.caption }}</div>
            </div>
          </div>
          <div v-else class="empty-block">
            <div class="empty-icon">🖼</div>
            <p class="empty-msg">Galeria ainda sem fotos.</p>
          </div>
        </div>
      </div>

      <!-- Aside -->
      <aside class="detail-aside">
        <div class="aside-card info-card" :style="{ borderColor: proj.color + '33' }">
          <div class="info-icon" :style="{ background: proj.color + '22' }">{{ proj.icon }}</div>
          <h4 class="aside-title">{{ proj.name }}</h4>
          <p class="aside-desc">{{ proj.desc.slice(0, 120) }}{{ proj.desc.length > 120 ? '…' : '' }}</p>
          <div class="aside-tag" :style="{ color: proj.color, background: proj.color + '18', border: `1px solid ${proj.color}33` }">
            {{ proj.tag }}
          </div>
        </div>

        <div class="aside-card">
          <h4 class="aside-title">Próximas atividades</h4>
          <div v-if="activities.length > 0" class="next-acts">
            <div v-for="act in activities.slice(0, 3)" :key="act.title" class="next-act-row">
              <div class="nact-dot" :style="{ background: proj.color }"></div>
              <div>
                <div class="nact-title">{{ act.title }}</div>
                <div class="nact-schedule">{{ act.days }} · {{ act.time }}</div>
              </div>
            </div>
          </div>
          <p v-else class="empty-msg small">Sem atividades agendadas.</p>
        </div>

        <div class="aside-card">
          <h4 class="aside-title">Participar</h4>
          <div class="other-btns">
            <button class="ghost-btn" :style="{ borderColor: proj.color + '44', color: proj.color }">🙋 Ser voluntário</button>
            <button class="ghost-btn">🔗 Compartilhar</button>
            <button class="ghost-btn">🤝 Parceria</button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUiStore } from '../../../stores/ui'

const ui = useUiStore()
const proj = ui.selectedProj
const activeTab = ref('Sobre')
const tabs = ['Sobre', 'Atividades', 'Equipe', 'Galeria']

const activities = computed(() => proj?.activities ?? [
  { title: 'Reforço escolar', days: 'Seg, Qua e Sex', time: '14h – 16h', desc: 'Apoio em matemática, português e ciências para crianças de 7 a 14 anos.', spots: 20 },
  { title: 'Capoeira', days: 'Terça e Quinta', time: '17h – 18h30', desc: 'Aulas de capoeira angola para todas as idades.', spots: 15 },
  { title: 'Teatro comunitário', days: 'Sábado', time: '10h – 12h', desc: 'Oficina de expressão corporal e teatro para jovens de 12 a 18 anos.', spots: 12 },
])

const tutors = computed(() => proj?.tutors ?? [
  { name: 'Prof. Marcus Vinícius', role: 'Educação & Reforço', initials: 'MV', color: '#FF5E1A', bio: 'Professor formado em pedagogia com 8 anos de experiência em educação comunitária.' },
  { name: 'Mestre Jair', role: 'Capoeira Angola', initials: 'MJ', color: '#FFD23F', bio: 'Mestre de capoeira angola há 22 anos, fundador do grupo Raízes do Alemão.' },
  { name: 'Fabiana Luz', role: 'Teatro & Expressão', initials: 'FL', color: '#2BD96B', bio: 'Atriz e educadora social, formada pela UniRio com ênfase em teatro popular.' },
])

const gallery = computed(() => proj?.gallery ?? [
  { color1: '#FF5E1A', color2: '#FFD23F', caption: 'Roda de capoeira — Mar 2025' },
  { color1: '#2BD96B', color2: '#FFD23F', caption: 'Reforço escolar' },
  { color1: '#FFD23F', color2: '#FF5E1A', caption: 'Peça teatral 2024' },
  { color1: '#FF5E1A', color2: '#2BD96B', caption: 'Formatura da turma' },
  { color1: '#2BD96B', color2: '#FF5E1A', caption: 'Atividade externa' },
  { color1: '#FFD23F', color2: '#2BD96B', caption: 'Oficina de pintura' },
])
</script>

<style scoped>
.proj-detail { padding: 28px 32px; max-width: 1480px; }
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 4px; }
.back-btn:hover { color: var(--cream); }

/* Hero */
.proj-hero { position: relative; min-height: 220px; border-radius: var(--radius-2xl); overflow: hidden; margin-bottom: 16px; display: flex; align-items: flex-end; padding: 32px; }
.ph-emoji { position: absolute; right: 40px; top: 50%; transform: translateY(-50%); font-size: 120px; line-height: 1; opacity: 0.5; }
.ph-overlay { position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.65) 0%, transparent 70%); }
.ph-content { position: relative; z-index: 1; }
.ph-tag { font-family: var(--mono); font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; margin-bottom: 10px; }
.ph-title { font-size: 34px; font-weight: 800; color: white; margin-bottom: 10px; line-height: 1.1; }
.ph-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: rgba(255,255,255,0.8); }

/* Stats strip */
.stats-strip {
  display: flex;
  align-items: center;
  gap: 0;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-xl);
  padding: 18px 24px;
  margin-bottom: 20px;
}
.stat-cell { flex: 1; text-align: center; }
.stat-sep { width: 1px; height: 36px; background: var(--line); }
.stat-n { font-family: var(--display); font-size: 26px; font-weight: 800; letter-spacing: -0.03em; }
.stat-l { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-top: 2px; }

/* Layout */
.detail-grid { display: grid; grid-template-columns: 1fr 300px; gap: 20px; }

/* Tabs */
.tabs { display: flex; gap: 0; border-bottom: 1px solid var(--line); margin-bottom: 24px; }
.tab { padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; transition: color 0.15s; position: relative; }
.tab:hover { color: var(--cream); }
.tab.active { color: var(--orange); }
.tab.active::after { content: ""; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: var(--orange); }

.content-title { font-family: var(--display); font-size: 18px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.02em; }
.body-text { font-size: 14px; color: var(--muted); line-height: 1.65; }

/* Atividades */
.activities-list { display: flex; flex-direction: column; gap: 12px; }
.activity-card {
  display: flex;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  overflow: hidden;
  transition: border-color 0.15s;
}
.activity-card:hover { border-color: var(--line-strong); }
.act-color-bar { width: 4px; flex-shrink: 0; }
.act-body { padding: 16px 18px; flex: 1; }
.act-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.act-title { font-size: 15px; font-weight: 700; }
.act-spots { font-family: var(--mono); font-size: 11px; font-weight: 600; }
.act-schedule { display: flex; align-items: center; gap: 6px; font-family: var(--mono); font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px; }
.act-dot { color: var(--muted-2); }
.act-desc { font-size: 13px; color: var(--muted); line-height: 1.5; }

/* Equipe */
.tutors-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
.tutor-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
}
.tutor-av {
  width: 60px; height: 60px;
  border-radius: 18px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display);
  font-weight: 800;
  font-size: 20px;
  color: var(--black);
  margin-bottom: 4px;
}
.tutor-name { font-size: 14px; font-weight: 700; }
.tutor-role { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); }
.tutor-bio { font-size: 12px; color: var(--muted); line-height: 1.5; margin-top: 4px; }

/* Galeria */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-auto-rows: 140px;
  gap: 10px;
}
.gallery-item {
  border-radius: var(--radius-md);
  position: relative;
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s;
}
.gallery-item:hover { transform: scale(1.02); }
.gallery-item.wide { grid-column: span 2; }
.gallery-item.tall { grid-row: span 2; }
.gallery-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.55), transparent 50%); }
.gallery-caption { position: absolute; bottom: 10px; left: 12px; font-size: 11px; font-weight: 600; color: white; z-index: 1; }

/* Atualizations */
.update-feed { display: flex; flex-direction: column; gap: 0; border-left: 2px solid var(--line); padding-left: 16px; margin-left: 8px; }
.update-item { position: relative; padding-bottom: 20px; }
.update-item:last-child { padding-bottom: 0; }
.update-dot { position: absolute; left: -21px; top: 3px; width: 10px; height: 10px; border-radius: 50%; border: 2px solid var(--bg); }
.update-time { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); margin-bottom: 4px; }
.update-title { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
.update-text { font-size: 13px; color: var(--muted); line-height: 1.5; }

/* Empty states */
.empty-block { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 48px 0; }
.empty-icon { font-size: 40px; opacity: 0.4; }
.empty-msg { font-size: 13px; color: var(--muted); text-align: center; }
.empty-msg.small { font-size: 12px; }

/* Aside */
.detail-aside { display: flex; flex-direction: column; gap: 12px; position: sticky; top: calc(var(--tb-h) + 20px); }
.aside-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-xl); padding: 18px; }
.aside-title { font-family: var(--display); font-size: 15px; font-weight: 700; margin-bottom: 14px; letter-spacing: -0.02em; }

.info-card { display: flex; flex-direction: column; gap: 10px; }
.info-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; }
.aside-desc { font-size: 13px; color: var(--muted); line-height: 1.5; }
.aside-tag { display: inline-flex; padding: 4px 12px; border-radius: 999px; font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; }

.next-acts { display: flex; flex-direction: column; gap: 12px; }
.next-act-row { display: flex; align-items: flex-start; gap: 10px; }
.nact-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.nact-title { font-size: 13px; font-weight: 600; }
.nact-schedule { font-family: var(--mono); font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }

.other-btns { display: flex; flex-direction: column; gap: 8px; }
.ghost-btn { padding: 10px 14px; border-radius: 10px; border: 1px solid var(--line); background: transparent; color: var(--cream); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.15s; text-align: left; }
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card-2); }

@media (max-width: 768px) {
  .proj-detail { padding: 16px; }
  .proj-hero { min-height: 180px; padding: 20px; border-radius: var(--radius-xl); }
  .ph-title { font-size: 22px; }
  .ph-emoji { font-size: 70px; right: 20px; }
  .ph-meta { font-size: 11px; gap: 8px; }
  .stats-strip { padding: 14px 16px; gap: 0; flex-wrap: nowrap; overflow-x: auto; }
  .stat-n { font-size: 20px; }
  .tabs { overflow-x: auto; flex-wrap: nowrap; }
  .tab { white-space: nowrap; font-size: 12px; padding: 10px 12px; }
  .detail-grid { grid-template-columns: 1fr; }
  .detail-aside { position: static; }
  .tutors-grid { grid-template-columns: repeat(2, 1fr); }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: 100px; }
  .gallery-item.wide { grid-column: span 1; }
  .gallery-item.tall { grid-row: span 1; }
}
</style>
