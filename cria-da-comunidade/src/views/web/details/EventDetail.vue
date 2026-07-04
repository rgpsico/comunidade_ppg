<template>
  <div class="event-detail fade-up" v-if="event">
    <div class="top-bar">
      <button class="back-btn" @click="ui.goTo('eventos')">← Eventos</button>
      <div class="share-wrap" ref="shareWrap">
        <button class="share-btn" @click="doShare">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          Compartilhar
        </button>
        <div class="share-dropdown" v-if="showShare">
          <div class="share-dropdown-title">Compartilhar evento</div>
          <a :href="shareLinks.whatsapp" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            WhatsApp
          </a>
          <a :href="shareLinks.twitter" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            X (Twitter)
          </a>
          <a :href="shareLinks.linkedin" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#0077B5"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            LinkedIn
          </a>
          <a :href="shareLinks.facebook" target="_blank" @click="showShare = false" class="share-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </a>
          <div class="share-divider"></div>
          <button @click="copyLink" class="share-item share-copy" :class="{ copied }">
            <svg v-if="!copied" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
            <svg v-else width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ copied ? 'Link copiado!' : 'Copiar link' }}
          </button>
        </div>
      </div>
    </div>

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
            <a :href="shareLinks.whatsapp" target="_blank" class="ghost-btn">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="color:#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
              WhatsApp
            </a>
            <a :href="shareLinks.facebook" target="_blank" class="ghost-btn">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="color:#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              Facebook
            </a>
            <button @click="copyLink" class="ghost-btn" :class="{ 'copied-btn': copied }">
              {{ copied ? '✓ Copiado!' : '🔗 Copiar link' }}
            </button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useUiStore } from '../../../stores/ui'

const ui = useUiStore()
const event = computed(() => ui.selectedEvent)

// Share
const shareWrap = ref<HTMLElement | null>(null)
const showShare = ref(false)
const copied = ref(false)

const shareUrl = computed(() =>
  `${window.location.origin}/eventos/${event.value?.id ?? ''}`
)

const shareLinks = computed(() => {
  if (!event.value) return { whatsapp: '#', twitter: '#', linkedin: '#', facebook: '#' }
  const e = event.value
  const url = encodeURIComponent(shareUrl.value)
  const text = encodeURIComponent(
    `🎉 ${e.title}\n📅 ${e.day}/${e.month} às ${e.time} · 📍 ${e.place}\n\nVeja mais em: ${shareUrl.value}`
  )
  const title = encodeURIComponent(e.title)
  return {
    whatsapp: `https://wa.me/?text=${text}`,
    twitter: `https://x.com/intent/tweet?text=${title}&url=${url}`,
    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${url}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${url}`,
  }
})

async function doShare() {
  if (!event.value) return
  if (navigator.share) {
    try {
      await navigator.share({
        title: event.value.title,
        text: `${event.value.day}/${event.value.month} às ${event.value.time} · ${event.value.place}`,
        url: shareUrl.value,
      })
    } catch { /* diálogo cancelado */ }
  } else {
    showShare.value = !showShare.value
  }
}

async function copyLink() {
  try {
    await navigator.clipboard.writeText(shareUrl.value)
  } catch {
    const el = document.createElement('textarea')
    el.value = shareUrl.value
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
  }
  copied.value = true
  setTimeout(() => { copied.value = false; showShare.value = false }, 2000)
}

function onDocClick(e: MouseEvent) {
  if (shareWrap.value && !shareWrap.value.contains(e.target as Node)) {
    showShare.value = false
  }
}
onMounted(() => document.addEventListener('click', onDocClick))
onUnmounted(() => document.removeEventListener('click', onDocClick))

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
.top-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.back-btn { font-size: 13px; color: var(--muted); cursor: pointer; display: inline-flex; align-items: center; gap: 4px; background: none; border: none; padding: 0; }
.back-btn:hover { color: var(--cream); }
.share-wrap { position: relative; }
.share-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 999px;
  border: 1px solid var(--line); background: var(--card);
  font-size: 12px; font-weight: 600; color: var(--cream);
  cursor: pointer; transition: all 0.15s;
}
.share-btn:hover { border-color: var(--line-strong); background: var(--card-2); }
.share-dropdown {
  position: absolute; right: 0; top: calc(100% + 8px);
  background: var(--card-2); border: 1px solid var(--line);
  border-radius: 14px; padding: 8px; min-width: 200px;
  z-index: 200; box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}
.share-dropdown-title {
  font-size: 11px; font-weight: 700; color: var(--muted);
  text-transform: uppercase; letter-spacing: 0.06em; padding: 4px 8px 8px;
}
.share-item {
  display: flex; align-items: center; gap: 10px; width: 100%;
  padding: 8px 10px; border-radius: 8px; font-size: 13px; font-weight: 500;
  color: var(--cream); text-decoration: none; cursor: pointer;
  background: none; border: none; transition: background 0.15s;
}
.share-item:hover { background: rgba(255,255,255,0.06); }
.share-divider { height: 1px; background: var(--line); margin: 6px 0; }
.share-copy.copied { color: var(--green); }
.share-btns { display: flex; flex-direction: column; gap: 8px; }
.share-btns .ghost-btn {
  display: flex; align-items: center; gap: 8px;
  justify-content: center; text-decoration: none;
}
.copied-btn { color: var(--green) !important; border-color: var(--green) !important; }

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
