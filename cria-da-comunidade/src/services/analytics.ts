const BASE = import.meta.env.VITE_API_BASE ?? 'http://127.0.0.1:8000/api'

function getSessionId(): string {
  let id = localStorage.getItem('_asid')
  if (!id) {
    id = (crypto.randomUUID?.() ?? Math.random().toString(36).slice(2) + Date.now().toString(36)).slice(0, 64)
    localStorage.setItem('_asid', id)
  }
  return id
}

function send(payload: Record<string, unknown>) {
  const token = localStorage.getItem('auth_token')
  fetch(`${BASE}/analytics/track`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    body: JSON.stringify({ ...payload, session_id: getSessionId() }),
    keepalive: true,
  }).catch(() => { /* silencioso */ })
}

export function trackPageView(screen: string) {
  send({ type: 'page_view', screen })
}

export function trackClick(
  entityType: 'profissional' | 'evento' | 'projeto' | 'vaga',
  entityId: string | number,
  entityName: string,
  screen: string,
) {
  send({ type: 'click', screen, entity_type: entityType, entity_id: entityId, entity_name: entityName })
}
