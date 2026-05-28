<template>
  <div class="event-detail fade-up" v-if="event">
    <button class="back-btn" @click="ui.goTo('eventos')">← Eventos</button>

    <!-- Hero -->
    <div class="event-hero" :style="{ background: `linear-gradient(135deg, ${event.c1}, ${event.c2})` }">
      <div class="eh-pattern"></div>
      <div class="eh-overlay"></div>
      <div class="eh-date">
        <div class="ehd-day">{{ event.day }}</div>
        <div class="ehd-month">{{ event.month }}</div>
      </div>
      <div class="eh-content">
        <div class="eh-tag">
          <span class="tag-dot"></span>
          {{ event.cat }} · {{ event.time }}
        </div>
        <h1 class="eh-title display">{{ event.title }}</h1>
        <div class="eh-meta">
          <span>🕒 {{ event.time }}</span>
          <span>📍 {{ event.place }}</span>
          <span>👥 {{ event.rsvp.going }} confirmados</span>
          <span v-if="event.free" class="free-tag">Entrada gratuita</span>
        </div>
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-main">
        <h3 class="content-title">Sobre o evento</h3>
        <p class="body-text">{{ event.description }}</p>

        <h3 class="content-title" style="margin-top: 24px">Localização</h3>
        <div class="map-placeholder">
          <div class="map-streets"></div>
          <div class="map-pin">
            <div class="pin-dot"></div>
            <div class="pin-label">📍 {{ event.place }}</div>
          </div>
          <div class="map-label">📍 mapa</div>
        </div>

        <h3 class="content-title" style="margin-top: 24px">Quem organiza</h3>
        <div class="organizer-card">
          <div class="org-av">{{ event.organizer.name.slice(0,2).toUpperCase() }}</div>
          <div class="org-info">
            <div class="org-role">organizador · {{ event.organizer.eventsCount }} eventos</div>
            <div class="org-name">{{ event.organizer.name }}</div>
            <div class="org-meta">{{ event.organizer.followers }} seguidores</div>
          </div>
          <button class="ghost-btn">+ Seguir</button>
        </div>

        <h3 class="content-title" style="margin-top: 24px">Quem vai ({{ event.rsvp.going }})</h3>
        <div class="attendees">
          <div class="av-stack">
            <div v-for="(a, i) in attendeeAvatars" :key="i" class="av-sm" :style="{ background: a.color, marginLeft: i > 0 ? '-10px' : '0' }">
              {{ a.initials }}
            </div>
          </div>
          <span class="attendees-meta">{{ event.rsvp.going }} confirmados · {{ event.rsvp.interested }} interessados</span>
        </div>

        <h3 class="content-title" style="margin-top: 24px">Comentários ({{ event.comments.length }})</h3>
        <div class="comments-list">
          <div v-for="c in event.comments" :key="c.author" class="comment-card">
            <div class="cc-av">{{ c.author.slice(0,2) }}</div>
            <div class="cc-body">
              <div class="cc-head">
                <span class="cc-name">{{ c.author }}</span>
                <span class="cc-time">{{ c.time }}</span>
              </div>
              <p class="cc-text">{{ c.text }}</p>
              <div class="cc-actions">
                <button class="cc-action">♥ {{ c.likes }}</button>
                <button class="cc-action">💬 responder</button>
              </div>
            </div>
          </div>
        </div>
        <div class="comment-input-wrap">
          <input class="comment-input" type="text" placeholder="Escreva um comentário..." />
          <button class="send-btn">→</button>
        </div>
      </div>

      <aside class="detail-aside">
        <!-- RSVP -->
        <div class="aside-card rsvp-card">
          <h4 class="aside-title">Você vai?</h4>
          <p class="aside-sub">Confirma presença e avisa a galera</p>
          <div class="rsvp-options">
            <button
              v-for="opt in rsvpOptions"
              :key="opt.value ?? 'none'"
              class="rsvp-opt"
              :class="{ active: event.rsvp.userStatus === opt.value }"
              @click="event.rsvp.userStatus = opt.value"
            >
              {{ opt.icon }} {{ opt.label }}
            </button>
          </div>
          <div class="rsvp-stats">
            <span class="rsvp-stat orange">{{ event.rsvp.going }} confirmados</span>
            <span class="rsvp-stat yellow">{{ event.rsvp.interested }} interessados</span>
          </div>
        </div>

        <!-- Detalhes -->
        <div class="aside-card">
          <h4 class="aside-title">Detalhes</h4>
          <div class="detail-rows">
            <div class="drow"><span class="drow-k">Data</span><span>{{ event.day }}/{{ event.month }}</span></div>
            <div class="drow"><span class="drow-k">Local</span><span>{{ event.place }}</span></div>
            <div class="drow"><span class="drow-k">Categoria</span><span style="text-transform: capitalize">{{ event.cat }}</span></div>
            <div class="drow"><span class="drow-k">Entrada</span><span class="green-text">Gratuita</span></div>
            <div class="drow"><span class="drow-k">Idade mín.</span><span>{{ event.ageMin > 0 ? event.ageMin + ' anos' : 'Livre' }}</span></div>
            <div class="drow"><span class="drow-k">Duração</span><span>{{ event.duration }}</span></div>
          </div>
        </div>

        <div class="aside-card">
          <h4 class="aside-title">Compartilhar</h4>
          <div class="share-btns">
            <button class="ghost-btn">🔗 Compartilhar</button>
            <button class="ghost-btn">📸 Postar no feed</button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useUiStore } from '../../../stores/ui'

const ui = useUiStore()
const event = ui.selectedEvent

const rsvpOptions = [
  { value: 'going' as const, icon: '✓', label: 'Vou' },
  { value: 'interested' as const, icon: '★', label: 'Talvez' },
  { value: null, icon: '✕', label: 'Não vou' },
]

const attendeeAvatars = [
  { initials: 'MV', color: '#FF5E1A' }, { initials: 'CR', color: '#2BD96B' },
  { initials: 'TP', color: '#FFD23F' }, { initials: 'AS', color: '#FF5E1A' },
  { initials: 'BM', color: '#2BD96B' }, { initials: 'JF', color: '#FFD23F' },
]
</script>

<style scoped>
.event-detail { padding: 28px 32px; max-width: 1480px; }
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 4px; }
.back-btn:hover { color: var(--cream); }

.event-hero { position: relative; height: 280px; border-radius: var(--radius-2xl); overflow: hidden; margin-bottom: 24px; }
.eh-pattern { position: absolute; inset: 0; background: repeating-linear-gradient(45deg, rgba(0,0,0,0.1) 0, rgba(0,0,0,0.1) 1px, transparent 0, transparent 50%); background-size: 12px 12px; }
.eh-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.65)); }
.eh-date { position: absolute; top: 16px; left: 20px; display: flex; flex-direction: column; align-items: center; padding: 10px 14px; background: rgba(0,0,0,0.5); border-radius: 12px; backdrop-filter: blur(8px); z-index: 2; }
.ehd-day { font-family: var(--display); font-size: 38px; font-weight: 800; color: white; line-height: 1; }
.ehd-month { font-family: var(--mono); font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--yellow); }
.eh-content { position: absolute; bottom: 24px; left: 24px; right: 24px; z-index: 2; }
.eh-tag { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,94,26,0.3); border-radius: 999px; font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--orange); margin-bottom: 10px; backdrop-filter: blur(8px); }
.tag-dot { width: 6px; height: 6px; background: var(--orange); border-radius: 50%; animation: pulse-dot 1.6s ease-out infinite; color: var(--orange); }
.eh-title { font-size: 38px; font-weight: 800; color: white; margin-bottom: 10px; line-height: 1.1; }
.eh-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: rgba(255,255,255,0.8); }
.free-tag { padding: 3px 10px; border-radius: 999px; background: var(--green); color: var(--black); font-weight: 700; font-size: 11px; }

.detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
.content-title { font-family: var(--display); font-size: 18px; font-weight: 700; margin-bottom: 12px; letter-spacing: -0.02em; }
.body-text { font-size: 14px; color: var(--muted); line-height: 1.65; white-space: pre-line; }

.map-placeholder {
  aspect-ratio: 4/3; background: #1a1f2a; border-radius: var(--radius-xl); position: relative; overflow: hidden;
  border: 1px solid var(--line);
}
.map-streets {
  position: absolute; inset: 0;
  background: repeating-linear-gradient(0deg, rgba(255,94,26,0.06) 0, rgba(255,94,26,0.06) 1px, transparent 0, transparent 40px),
              repeating-linear-gradient(90deg, rgba(255,210,63,0.04) 0, rgba(255,210,63,0.04) 1px, transparent 0, transparent 60px);
}
.map-pin { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; flex-direction: column; align-items: center; gap: 8px; }
.pin-dot { width: 28px; height: 28px; background: var(--orange); border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 0 0 6px rgba(255,94,26,0.2); animation: pulse-dot 1.6s ease-out infinite; color: var(--orange); }
.pin-label { padding: 6px 12px; background: rgba(0,0,0,0.7); border-radius: 999px; font-size: 12px; font-weight: 600; color: white; white-space: nowrap; backdrop-filter: blur(8px); }
.map-label { position: absolute; top: 10px; left: 10px; font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); }

.organizer-card { display: flex; align-items: center; gap: 12px; padding: 16px; background: var(--card-2); border-radius: var(--radius-lg); border: 1px solid var(--line); }
.org-av { width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, var(--orange), var(--yellow)); display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 800; font-size: 18px; color: var(--black); flex-shrink: 0; }
.org-info { flex: 1; }
.org-role { font-family: var(--mono); font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); }
.org-name { font-size: 14px; font-weight: 700; margin: 2px 0; }
.org-meta { font-size: 11px; color: var(--muted); }
.ghost-btn { padding: 8px 16px; border-radius: 10px; border: 1px solid var(--line); background: transparent; color: var(--cream); font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
.ghost-btn:hover { border-color: var(--line-strong); background: var(--card); }

.attendees { display: flex; align-items: center; gap: 12px; }
.av-stack { display: flex; }
.av-sm { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 700; font-size: 11px; color: var(--black); border: 2px solid var(--card); flex-shrink: 0; }
.attendees-meta { font-size: 12px; color: var(--muted); }

.comments-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; }
.comment-card { display: flex; gap: 10px; }
.cc-av { width: 36px; height: 36px; border-radius: 10px; background: var(--card-2); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; font-family: var(--display); font-weight: 700; font-size: 12px; flex-shrink: 0; }
.cc-body { flex: 1; }
.cc-head { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap; }
.cc-name { font-size: 13px; font-weight: 600; }
.cc-time { font-family: var(--mono); font-size: 10px; color: var(--muted); text-transform: uppercase; margin-left: auto; }
.cc-text { font-size: 13px; color: var(--muted); line-height: 1.5; margin-bottom: 6px; }
.cc-actions { display: flex; gap: 12px; }
.cc-action { font-size: 11px; color: var(--muted); cursor: pointer; }
.cc-action:hover { color: var(--cream); }

.comment-input-wrap { display: flex; gap: 8px; }
.comment-input { flex: 1; height: 42px; padding: 0 14px; background: var(--card-2); border: 1px solid var(--line); border-radius: 10px; color: var(--cream); font-size: 13px; outline: none; transition: border-color 0.2s; }
.comment-input:focus { border-color: var(--orange); }
.comment-input::placeholder { color: var(--muted); }
.send-btn { width: 42px; height: 42px; background: var(--orange); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; font-weight: 700; transition: background 0.15s; }
.send-btn:hover { background: var(--orange-deep); }

.detail-aside { display: flex; flex-direction: column; gap: 12px; position: sticky; top: calc(var(--tb-h) + 20px); }
.aside-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-xl); padding: 18px; }
.rsvp-card { background: rgba(255,94,26,0.06); border-color: rgba(255,94,26,0.2); }
.aside-title { font-family: var(--display); font-size: 15px; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.02em; }
.aside-sub { font-size: 12px; color: var(--muted); margin-bottom: 14px; }

.rsvp-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 14px; }
.rsvp-opt { padding: 10px 8px; border-radius: 10px; border: 1px solid var(--line); background: var(--card-2); font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s; text-align: center; }
.rsvp-opt:hover { border-color: var(--line-strong); }
.rsvp-opt.active { background: var(--orange); border-color: var(--orange); color: var(--black); }
.rsvp-stats { display: flex; gap: 12px; padding-top: 10px; border-top: 1px solid var(--line); }
.rsvp-stat { font-family: var(--mono); font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; }
.rsvp-stat.orange { color: var(--orange); }
.rsvp-stat.yellow { color: var(--yellow); }

.detail-rows { display: flex; flex-direction: column; gap: 10px; }
.drow { display: flex; justify-content: space-between; font-size: 13px; }
.drow-k { color: var(--muted); }
.green-text { color: var(--green); font-weight: 600; }

.share-btns { display: flex; flex-direction: column; gap: 8px; }

@media (max-width: 768px) {
  .event-detail { padding: 16px; }

  .event-hero { height: 200px; border-radius: var(--radius-xl); }
  .eh-title { font-size: 22px; }
  .eh-content { bottom: 14px; left: 14px; right: 14px; }
  .eh-meta { font-size: 11px; gap: 8px; }
  .ehd-day { font-size: 26px; }

  .detail-grid { grid-template-columns: 1fr; }
  .detail-aside { position: static; }

  .rsvp-options { grid-template-columns: 1fr 1fr; }
  .map-placeholder { aspect-ratio: 16/9; }
}
</style>
