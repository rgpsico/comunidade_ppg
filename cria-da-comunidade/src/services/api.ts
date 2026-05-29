const BASE = import.meta.env.VITE_API_BASE ?? 'http://127.0.0.1:8000/api'

function getToken(): string | null {
  return localStorage.getItem('auth_token')
}

function headers(): Record<string, string> {
  const h: Record<string, string> = { Accept: 'application/json', 'Content-Type': 'application/json' }
  const token = getToken()
  if (token) h['Authorization'] = `Bearer ${token}`
  return h
}

async function request<T>(method: string, path: string, body?: unknown): Promise<T> {
  const res = await fetch(`${BASE}${path}`, {
    method,
    headers: headers(),
    body: body ? JSON.stringify(body) : undefined,
  })
  if (!res.ok) {
    const err = await res.json().catch(() => ({ message: `HTTP ${res.status}` }))
    throw new Error(err.message ?? `HTTP ${res.status}`)
  }
  return res.status === 204 ? (null as T) : res.json()
}

// Envia FormData (multipart) — não define Content-Type, o browser seta com boundary
async function requestForm<T>(path: string, body: FormData): Promise<T> {
  const token = getToken()
  const h: Record<string, string> = { Accept: 'application/json' }
  if (token) h['Authorization'] = `Bearer ${token}`
  const res = await fetch(`${BASE}${path}`, { method: 'POST', headers: h, body })
  if (!res.ok) {
    const err = await res.json().catch(() => ({ message: `HTTP ${res.status}` }))
    throw new Error(err.message ?? `HTTP ${res.status}`)
  }
  return res.json()
}

export interface Paginated<T> { data: T[]; total: number; current_page: number; last_page: number }

export const api = {
  get: <T>(path: string) => request<T>('GET', path),
  post: <T>(path: string, body: unknown) => request<T>('POST', path, body),
  put: <T>(path: string, body: unknown) => request<T>('PUT', path, body),
  delete: <T>(path: string) => request<T>('DELETE', path),
  postForm: <T>(path: string, body: FormData) => requestForm<T>(path, body),
}
