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
      <a
        v-if="vaga.whatsapp"
        :href="`https://wa.me/55${vaga.whatsapp.replace(/\D/g, '')}?text=${encodeURIComponent('Oi! Vi a vaga de ' + vaga.title + ' e tenho interesse!')}`"
        target="_blank"
        class="btn-wpp"
        @click.stop
        title="Falar no WhatsApp"
      >
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        WhatsApp
      </a>
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

.btn-wpp {
  display: flex; align-items: center; justify-content: center; gap: 5px;
  margin-top: 4px; padding: 6px 12px;
  border-radius: 999px;
  background: rgba(37,211,102,0.12);
  border: 1px solid rgba(37,211,102,0.25);
  color: #25D366; font-weight: 600; font-size: 11px;
  text-decoration: none; transition: all 0.15s;
}
.btn-wpp:hover { background: rgba(37,211,102,0.22); border-color: rgba(37,211,102,0.4); }
</style>
