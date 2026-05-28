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
        <div class="aside-card apply-card">
          <h4 class="aside-title">Candidatar rápido</h4>
          <p class="aside-sub">Manda suas infos e a empresa te chama</p>
          <div class="apply-form">
            <input class="form-input" type="text" placeholder="Seu nome" value="Maria Silva" />
            <input class="form-input" type="text" placeholder="WhatsApp" />
            <textarea class="form-input" rows="3" placeholder="Conta um pouco sobre você..."></textarea>
            <button class="btn-apply">Enviar candidatura →</button>
          </div>
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
import { ref } from 'vue'
import { useUiStore } from '../../../stores/ui'

const ui = useUiStore()
const vaga = ui.selectedVaga
const activeTab = ref('Descrição')
const tabs = ['Descrição', 'Empresa', 'Similares (8)']
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
.apply-card { background: rgba(255,94,26,0.06); border-color: rgba(255,94,26,0.2); }
.aside-title { font-family: var(--display); font-size: 15px; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.02em; }
.aside-sub { font-size: 12px; color: var(--muted); margin-bottom: 14px; }
.apply-form { display: flex; flex-direction: column; gap: 8px; }
.form-input { width: 100%; padding: 10px 12px; background: var(--card-2); border: 1px solid var(--line); border-radius: 10px; color: var(--cream); font-size: 13px; outline: none; resize: vertical; font-family: inherit; transition: border-color 0.2s; }
.form-input:focus { border-color: var(--orange); }
.form-input::placeholder { color: var(--muted); }
.btn-apply { padding: 12px; border-radius: 10px; background: var(--orange); color: white; font-weight: 700; font-size: 13px; box-shadow: var(--shadow-cta-orange); transition: all 0.15s; }
.btn-apply:hover { background: var(--orange-deep); transform: translateY(-1px); }
.detail-rows { display: flex; flex-direction: column; gap: 10px; }
.drow { display: flex; justify-content: space-between; font-size: 13px; }
.drow-k { color: var(--muted); }
.green-text { color: var(--green); font-weight: 600; }
</style>
