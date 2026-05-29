<template>
  <div class="vaga-detail fade-up" v-if="vaga">
    <button class="back-btn" @click="ui.goTo('vagas')">← Vagas</button>

    <!-- Hero -->
    <div class="vaga-hero">
      <div class="vh-logo" :style="{ background: vaga.logoColor }">{{ vaga.logoInitials }}</div>
      <div class="vh-main">
        <div class="vaga-flags">
          <span v-if="vaga.urgent" class="flag urgent-flag">urgente</span>
          <span v-if="vaga.isNew" class="flag new-flag">novo</span>
          <span class="flag type-flag">{{ vaga.type }}</span>
        </div>
        <h1 class="vh-title display">{{ vaga.title }}</h1>
        <div class="vh-company">{{ vaga.company }} · empresa verificada</div>
        <div class="vh-meta">
          <span>📍 {{ vaga.place }}</span>
          <span>🕒 postado {{ vaga.posted }}</span>
          <span>👥 {{ vaga.applicants }} candidatos</span>
        </div>
      </div>
      <div class="vh-right">
        <div class="vh-pay">{{ vaga.pay }}</div>
        <div class="vh-per">{{ vaga.per }}</div>
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-main">
        <div class="tabs">
          <button v-for="tab in tabs" :key="tab" class="tab" :class="{ active: activeTab === tab }" @click="activeTab = tab">{{ tab }}</button>
        </div>

        <div v-if="activeTab === 'Descrição'" class="tab-content">
          <h3 class="content-title">Sobre a vaga</h3>
          <p class="body-text">{{ vaga.desc }}</p>

          <h3 class="content-title" style="margin-top: 24px">Requisitos</h3>
          <div class="req-list">
            <div v-for="req in vaga.requirements" :key="req.text" class="req-row">
              <div class="req-check">✓</div>
              <div class="req-text">{{ req.text }}</div>
              <div class="req-level" :class="req.level">{{ req.level }}</div>
            </div>
          </div>

          <h3 class="content-title" style="margin-top: 24px">O que oferece</h3>
          <div class="ben-list">
            <div v-for="b in vaga.benefits" :key="b" class="ben-row">
              <div class="ben-star">⭐</div>
              <div>{{ b }}</div>
            </div>
          </div>

          <h3 class="content-title" style="margin-top: 24px">Sobre a empresa</h3>
          <div class="organizer-card">
            <div class="org-av">{{ vaga.companyInfo.name.slice(0,2).toUpperCase() }}</div>
            <div class="org-info">
              <div class="org-role">{{ vaga.companyInfo.verified ? '✓ verificada' : 'empresa' }} · desde {{ vaga.companyInfo.since }}</div>
              <div class="org-name">{{ vaga.companyInfo.name }}</div>
              <div class="org-meta">⭐ {{ vaga.companyInfo.rating }}</div>
            </div>
            <button class="ghost-btn">+ Seguir</button>
          </div>
        </div>
      </div>

      <aside class="detail-aside">
        <div class="aside-card contact-card">
          <h4 class="aside-title">Falar com a empresa</h4>
          <p class="aside-sub">Entre em contato direto com o anunciante</p>
          <div class="contact-actions">
            <a
              v-if="vaga.whatsapp"
              :href="`https://wa.me/55${vaga.whatsapp.replace(/\D/g, '')}?text=${encodeURIComponent('Oi! Vi a vaga de ' + vaga.title + ' e tenho interesse!')}`"
              target="_blank"
              class="btn-whatsapp"
              @click.stop
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
              Falar no WhatsApp
            </a>
            <a
              v-if="vaga.emailContato"
              :href="`mailto:${vaga.emailContato}?subject=${encodeURIComponent('Interesse na vaga: ' + vaga.title)}`"
              class="btn-email"
              @click.stop
            >
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
              Enviar e-mail
            </a>
            <p v-if="!vaga.whatsapp && !vaga.emailContato" class="no-contact-msg">
              Contato não informado pelo anunciante.
            </p>
          </div>
          <button class="btn-apply" :class="{ applied }" @click.stop="doCandidatar">
            {{ applied ? '✓ Candidatura registrada' : 'Me candidatar →' }}
          </button>
        </div>

        <div class="aside-card">
          <h4 class="aside-title">Detalhes</h4>
          <div class="detail-rows">
            <div class="drow"><span class="drow-k">Tipo</span><span>{{ vaga.type }}</span></div>
            <div class="drow"><span class="drow-k">Local</span><span>{{ vaga.place }}</span></div>
            <div class="drow"><span class="drow-k">Salário</span><span class="green-text">{{ vaga.pay }}</span></div>
            <div class="drow"><span class="drow-k">Período</span><span>{{ vaga.per }}</span></div>
            <div class="drow"><span class="drow-k">Candidatos</span><span>{{ vaga.applicants }}</span></div>
            <div class="drow"><span class="drow-k">Status</span><span class="green-text">aberta</span></div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useUiStore } from '../../../stores/ui'
import { useDataStore } from '../../../stores/data'

const ui = useUiStore()
const data = useDataStore()
const vaga = computed(() => ui.selectedVaga)
const activeTab = ref('Descrição')
const tabs = ['Descrição', 'Empresa', 'Similares (8)']
const applied = ref(false)

async function doCandidatar() {
  if (!vaga.value || applied.value) return
  await data.candidatar(vaga.value.id)
  applied.value = true
}
</script>

<style scoped>
.vaga-detail { padding: 28px 32px; max-width: 1480px; }
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 4px; }
.back-btn:hover { color: var(--cream); }

.vaga-hero { display: grid; grid-template-columns: 80px 1fr auto; gap: 20px; align-items: start; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-2xl); padding: 28px 32px; margin-bottom: 20px; }
.vh-logo { width: 80px; height: 80px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 800; font-size: 28px; color: var(--black); flex-shrink: 0; }
.vaga-flags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
.flag { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; padding: 3px 8px; border-radius: 999px; font-weight: 500; }
.urgent-flag { background: rgba(255,94,26,0.15); color: var(--orange); border: 1px solid rgba(255,94,26,0.3); }
.new-flag { background: rgba(43,217,107,0.15); color: var(--green); border: 1px solid rgba(43,217,107,0.3); }
.type-flag { background: var(--card-2); color: var(--muted); border: 1px solid var(--line); }
.vh-title { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 4px; }
.vh-company { font-size: 14px; color: var(--cream); margin-bottom: 8px; }
.vh-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--muted); }
.vh-right { text-align: right; }
.vh-pay { font-family: var(--display); font-size: 28px; font-weight: 800; color: var(--green); letter-spacing: -0.03em; }
.vh-per { font-size: 12px; color: var(--muted); }

.detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
.tabs { display: flex; gap: 0; border-bottom: 1px solid var(--line); margin-bottom: 24px; }
.tab { padding: 12px 16px; font-size: 13px; font-weight: 600; color: var(--muted); cursor: pointer; transition: color 0.15s; position: relative; }
.tab:hover { color: var(--cream); }
.tab.active { color: var(--orange); }
.tab.active::after { content: ""; position: absolute; bottom: -1px; left: 0; right: 0; height: 2px; background: var(--orange); }
.content-title { font-family: var(--display); font-size: 18px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.02em; }
.body-text { font-size: 14px; color: var(--muted); line-height: 1.65; }

.req-list { display: flex; flex-direction: column; gap: 8px; }
.req-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--card-2); border-radius: var(--radius-md); border: 1px solid var(--line); }
.req-check { width: 22px; height: 22px; background: rgba(43,217,107,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; color: var(--green); flex-shrink: 0; }
.req-text { flex: 1; font-size: 13px; }
.req-level { font-family: var(--mono); font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em; }
.req-level.obrigatório { color: var(--orange); }
.req-level.desejável { color: var(--yellow); }
.req-level.opcional { color: var(--muted); }

.ben-list { display: flex; flex-direction: column; gap: 8px; }
.ben-row { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.ben-star { font-size: 16px; }

.organizer-card { display: flex; align-items: center; gap: 12px; padding: 16px; background: var(--card-2); border-radius: var(--radius-lg); border: 1px solid var(--line); }
.org-av { width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, var(--orange), var(--yellow)); display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 800; font-size: 18px; color: var(--black); flex-shrink: 0; }
.org-info { flex: 1; }
.org-role { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); }
.org-name { font-size: 14px; font-weight: 700; margin: 2px 0; }
.org-meta { font-size: 11px; color: var(--muted); }
.ghost-btn { padding: 8px 16px; border-radius: 10px; border: 1px solid var(--line); background: transparent; color: var(--cream); font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card); }

.detail-aside { display: flex; flex-direction: column; gap: 12px; position: sticky; top: calc(var(--tb-h) + 20px); }
.aside-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-xl); padding: 18px; }
.contact-card { background: rgba(43,217,107,0.04); border-color: rgba(43,217,107,0.18); }
.aside-title { font-family: var(--display); font-size: 15px; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.02em; }
.aside-sub { font-size: 12px; color: var(--muted); margin-bottom: 14px; }

.contact-actions { display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px; }
.btn-whatsapp {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 12px; border-radius: 10px;
  background: #25D366; color: white;
  font-weight: 700; font-size: 13px;
  box-shadow: 0 4px 16px -4px rgba(37,211,102,0.45);
  transition: all 0.15s; text-decoration: none;
}
.btn-whatsapp:hover { background: #1db954; transform: translateY(-1px); }
.btn-email {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  padding: 11px; border-radius: 10px;
  border: 1px solid var(--line); background: var(--card-2);
  color: var(--cream); font-weight: 600; font-size: 13px;
  transition: all 0.15s; text-decoration: none;
}
.btn-email:hover { border-color: var(--line-strong); background: var(--card); }
.no-contact-msg { font-size: 12px; color: var(--muted); text-align: center; padding: 8px 0; }

.btn-apply {
  width: 100%; padding: 12px; border-radius: 10px;
  background: var(--orange); color: white;
  font-weight: 700; font-size: 13px;
  box-shadow: var(--shadow-cta-orange); transition: all 0.15s;
}
.btn-apply:hover { background: var(--orange-deep); transform: translateY(-1px); }
.btn-apply.applied { background: var(--green); box-shadow: 0 4px 16px -4px rgba(43,217,107,0.4); cursor: default; }
.btn-apply.applied:hover { transform: none; }
.detail-rows { display: flex; flex-direction: column; gap: 10px; }
.drow { display: flex; justify-content: space-between; font-size: 13px; }
.drow-k { color: var(--muted); }
.green-text { color: var(--green); font-weight: 600; }

@media (max-width: 768px) {
  .vaga-detail { padding: 16px; }

  .vaga-hero { flex-wrap: wrap; gap: 12px; padding: 16px; }
  .vh-logo { width: 56px; height: 56px; font-size: 20px; border-radius: 14px; }
  .vh-title { font-size: 20px; }
  .vh-right { width: 100%; display: flex; align-items: center; gap: 8px; }
  .vh-pay { font-size: 20px; }
  .vh-meta { font-size: 11px; gap: 8px; }

  .detail-grid { grid-template-columns: 1fr; }
  .detail-aside { position: static; }
}
</style>
