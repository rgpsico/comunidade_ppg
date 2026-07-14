<template>
  <div class="pro-detail fade-up" v-if="pro">
    <button class="back-btn" @click="ui.goTo('profissionais')">← Profissionais</button>

    <!-- Hero -->
    <div class="pro-hero">
      <div class="cover" :style="{ background: `linear-gradient(135deg, ${pro.c1}, ${pro.c2})` }">
        <div class="cover-pattern"></div>
      </div>
      <div class="hero-body">
        <div class="avatar" :style="{ background: `linear-gradient(135deg, ${pro.c1}, ${pro.c2})` }">
          {{ pro.initials }}
          <span v-if="pro.verified" class="av-verified">✓</span>
        </div>
        <div class="hero-info">
          <div class="hero-name-row">
            <h1 class="hero-name display">{{ pro.name }}</h1>
            <span v-if="pro.verified" class="badge-verified">✓ verificado</span>
          </div>
          <div class="hero-role">{{ pro.role }}</div>
          <div class="hero-meta">
            <span>⭐ {{ pro.stars }} ({{ pro.reviews }})</span>
            <span>📍 {{ pro.dist }}</span>
            <span class="resp-time">Responde em {{ pro.responseTime }}</span>
            <span>Cria desde {{ pro.since }}</span>
          </div>
        </div>
        <div class="hero-quick">
          <button class="icon-btn" title="Salvar">🔖</button>
          <button class="icon-btn" title="Compartilhar">🔗</button>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-cell">
        <div class="sn" style="color: var(--yellow)">{{ pro.stars }}</div>
        <div class="sl">avaliação</div>
        <div class="ss">estrelas</div>
      </div>
      <div class="stat-cell">
        <div class="sn">{{ pro.attendances }}</div>
        <div class="sl">atendimentos</div>
        <div class="ss">realizados</div>
      </div>
      <div class="stat-cell">
        <div class="sn" style="color: var(--green)">{{ pro.yearsActive }}</div>
        <div class="sl">anos</div>
        <div class="ss">na quebrada</div>
      </div>
      <div class="stat-cell">
        <div class="sn" style="color: var(--orange)">98%</div>
        <div class="sl">satisfação</div>
        <div class="ss">dos clientes</div>
      </div>
    </div>

    <!-- Detail grid -->
    <div class="detail-grid">
      <div class="detail-main">
        <!-- Tabs -->
        <div class="tabs">
          <button
            v-for="tab in tabs"
            :key="tab"
            class="tab"
            :class="{ active: activeTab === tab }"
            @click="activeTab = tab"
          >{{ tab }}</button>
        </div>

        <!-- Sobre -->
        <div v-if="activeTab === 'Sobre'" class="tab-content">
          <h3 class="content-title">Sobre mim</h3>
          <p class="body-text">{{ pro.bio }}</p>
          <h3 class="content-title" style="margin-top: 24px">Serviços</h3>
          <div class="services-grid">
            <div v-for="svc in pro.services" :key="svc.name" class="service-row">
              <div class="sr-name">{{ svc.name }}</div>
              <div class="sr-time">🕒 {{ svc.time }}</div>
              <div class="sr-price">R$ {{ svc.price }}</div>
            </div>
          </div>
        </div>

        <!-- Serviços -->
        <div v-if="activeTab === 'Serviços'" class="tab-content">
          <div class="services-grid">
            <div v-for="svc in pro.services" :key="svc.name" class="service-row">
              <div class="sr-name">{{ svc.name }}</div>
              <div class="sr-time">🕒 {{ svc.time }}</div>
              <div class="sr-price">R$ {{ svc.price }}</div>
            </div>
          </div>
        </div>

        <!-- Galeria -->
        <div v-if="activeTab === 'Galeria'" class="tab-content">
          <div class="gallery-grid">
            <div
              v-for="(g, i) in galleryItems"
              :key="i"
              class="gallery-item"
              :class="{ tall: i === 0 }"
              :style="{ background: `linear-gradient(135deg, ${g.c1}, ${g.c2})` }"
            >
              <div class="gallery-label">{{ g.label }}</div>
            </div>
          </div>
        </div>

        <!-- Avaliações -->
        <div v-if="activeTab === 'Avaliações'" class="tab-content">
          <AvaliacaoSection v-if="pro" tipo="profissionais" :id="pro.id" />
        </div>
      </div>

      <!-- Aside -->
      <aside class="detail-aside">
        <!-- Contato -->
        <div class="aside-card">
          <h4 class="aside-title">Falar com {{ pro.name.split(' ')[0] }}</h4>
          <a
            v-if="pro.whatsapp"
            :href="`https://wa.me/${pro.whatsapp.replace(/\D/g, '')}`"
            target="_blank"
            rel="noopener noreferrer"
            class="btn-wa"
          >
            📱 Chamar no WhatsApp
          </a>
          <p v-else class="no-contact">Contato não disponível</p>
        </div>

        <!-- Info rápida -->
        <div class="aside-card">
          <h4 class="aside-title">Informações</h4>
          <div class="info-rows">
            <div class="info-row">
              <span class="info-k">Categoria</span>
              <span class="info-v">{{ pro.category }}</span>
            </div>
            <div class="info-row" v-if="pro.price">
              <span class="info-k">A partir de</span>
              <span class="info-v orange">R$ {{ pro.price }}</span>
            </div>
            <div class="info-row" v-if="pro.responseTime">
              <span class="info-k">Resposta</span>
              <span class="info-v green">{{ pro.responseTime }}</span>
            </div>
            <div class="info-row">
              <span class="info-k">Atendimentos</span>
              <span class="info-v">{{ pro.attendances }}</span>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUiStore } from '../../../stores/ui'
import AvaliacaoSection from '../../../components/ui/AvaliacaoSection.vue'

const ui = useUiStore()
const pro = computed(() => ui.selectedPro)

const activeTab = ref('Sobre')
const tabs = ['Sobre', 'Serviços', 'Galeria', 'Avaliações', 'Agenda']

const galleryItems = [
  { c1: '#FF5E1A', c2: '#FFD23F', label: 'unhas decoradas · 2 sem' },
  { c1: '#2BD96B', c2: '#FFD23F', label: 'gel completo · 1 sem' },
  { c1: '#FFD23F', c2: '#FF5E1A', label: 'mão + pé combo · 3 sem' },
  { c1: '#FF5E1A', c2: '#2BD96B', label: 'alongamento · 1 mês' },
  { c1: '#2BD96B', c2: '#FF5E1A', label: 'esmaltação · hoje' },
]
</script>

<style scoped>
.pro-detail { padding: 28px 32px; max-width: 1480px; }
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 4px; }
.back-btn:hover { color: var(--cream); }

.pro-hero { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-2xl); overflow: visible; margin-bottom: 16px; }
.cover { height: 180px; position: relative; }
.cover-pattern { position: absolute; inset: 0; background: repeating-linear-gradient(45deg, rgba(0,0,0,0.1) 0, rgba(0,0,0,0.1) 1px, transparent 0, transparent 50%); background-size: 12px 12px; }

.hero-body { display: flex; align-items: flex-end; gap: 20px; padding: 0 28px 24px; margin-top: -50px; position: relative; z-index: 1; flex-wrap: wrap; }
.avatar {
  width: 100px; height: 100px; border-radius: 24px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--display); font-weight: 800; font-size: 38px; color: rgba(0,0,0,0.6);
  border: 4px solid var(--card);
  position: relative; flex-shrink: 0;
}
.av-verified {
  position: absolute; bottom: -4px; right: -4px;
  width: 22px; height: 22px; background: var(--green);
  border-radius: 50%; border: 2px solid var(--card);
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; color: var(--black); font-weight: 700;
}

.hero-info { flex: 1; min-width: 0; }
.hero-name-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px; }
.hero-name { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; }
.badge-verified { padding: 4px 10px; background: rgba(43,217,107,0.15); border: 1px solid rgba(43,217,107,0.3); border-radius: 999px; font-size: 11px; color: var(--green); font-weight: 600; }
.hero-role { font-size: 14px; color: var(--cream); margin-bottom: 8px; }
.hero-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--muted); }
.resp-time { color: var(--green); }

.hero-quick { display: flex; gap: 8px; margin-left: auto; }
.icon-btn { width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--line); background: var(--card-2); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; transition: all 0.15s; }
.icon-btn:hover { border-color: var(--line-strong); }

.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-xl); overflow: hidden; margin-bottom: 20px; }
.stat-cell { padding: 20px 24px; border-right: 1px solid var(--line); text-align: center; }
.stat-cell:last-child { border-right: none; }
.sn { font-family: var(--display); font-size: 26px; font-weight: 800; letter-spacing: -0.03em; color: var(--cream); }
.sl { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); margin-top: 2px; }
.ss { font-size: 11px; color: var(--muted-2); }

.detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }

.tabs { display: flex; gap: 0; border-bottom: 1px solid var(--line); margin-bottom: 24px; }
.tab { padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; transition: color 0.15s; position: relative; }
.tab:hover { color: var(--cream); }
.tab.active { color: var(--orange); }
.tab.active::after { content: ""; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: var(--orange); }

.content-title { font-family: var(--display); font-size: 18px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.02em; }
.body-text { font-size: 14px; color: var(--muted); line-height: 1.65; }

.services-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.service-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: var(--card-2); border-radius: var(--radius-md); border: 1px solid var(--line); gap: 8px; }
.sr-name { font-size: 13px; font-weight: 500; flex: 1; }
.sr-time { font-size: 11px; color: var(--muted); }
.sr-price { font-size: 13px; font-weight: 700; color: var(--orange); }

.gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); grid-auto-rows: 100px; gap: 8px; }
.gallery-item { border-radius: var(--radius-md); position: relative; overflow: hidden; cursor: pointer; }
.gallery-item.tall { grid-row: span 2; }
.gallery-label { position: absolute; bottom: 8px; left: 8px; font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(255,255,255,0.8); background: rgba(0,0,0,0.5); padding: 3px 8px; border-radius: 999px; }


.detail-aside { display: flex; flex-direction: column; gap: 12px; position: sticky; top: calc(var(--tb-h) + 20px); }
.aside-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-xl); padding: 18px; }
.aside-title { font-family: var(--display); font-size: 15px; font-weight: 700; margin-bottom: 14px; letter-spacing: -0.02em; }

.btn-wa { display: block; text-align: center; text-decoration: none; padding: 12px; border-radius: 10px; background: var(--green); color: var(--black); font-weight: 700; font-size: 14px; box-shadow: var(--shadow-cta-green); transition: all 0.15s; }
.btn-wa:hover { background: var(--green-deep); transform: translateY(-1px); }
.no-contact { font-size: 13px; color: var(--muted); text-align: center; padding: 8px 0; }

.info-rows { display: flex; flex-direction: column; gap: 10px; }
.info-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; }
.info-k { color: var(--muted); }
.info-v { font-weight: 600; color: var(--cream); }
.info-v.orange { color: var(--orange); }
.info-v.green { color: var(--green); }

@media (max-width: 768px) {
  .pro-detail { padding: 16px; }

  .cover { height: 120px; }
  .hero-body { padding: 0 16px 20px; margin-top: -40px; gap: 12px; }
  .avatar { width: 72px; height: 72px; font-size: 26px; border-radius: 18px; }
  .hero-name { font-size: 20px; }
  .hero-role { font-size: 13px; }
  .hero-meta { gap: 8px; font-size: 11px; }
  .hero-quick { display: none; }

  .stats-row { grid-template-columns: repeat(2, 1fr); }
  .stat-cell { border-bottom: 1px solid var(--line); }
  .stat-cell:nth-child(odd) { border-right: 1px solid var(--line); }
  .stat-cell:nth-child(3),
  .stat-cell:nth-child(4) { border-bottom: none; }
  .sn { font-size: 20px; }

  .detail-grid { grid-template-columns: 1fr; }
  .detail-aside { position: static; }

  .tabs { overflow-x: auto; flex-wrap: nowrap; padding-bottom: 2px; }
  .tab { white-space: nowrap; padding: 10px 12px; font-size: 12px; }

  .services-grid { grid-template-columns: 1fr; }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
