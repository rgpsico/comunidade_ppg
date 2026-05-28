import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '../services/api'

interface AuthUser {
  id: number
  name: string
  handle: string | null
  email: string
  whatsapp: string | null
  bio: string | null
  ativo: boolean
  comunidade_id: number | null
  roles: { name: string }[]
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<AuthUser | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  const initials = computed(() => {
    if (!user.value) return '?'
    return user.value.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
  })

  async function login(email: string, password: string) {
    loading.value = true
    error.value = null
    try {
      const res = await api.post<{ user: AuthUser; token: string }>('/auth/login', { email, password })
      token.value = res.token
      user.value = res.user
      localStorage.setItem('auth_token', res.token)
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Erro ao entrar'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function register(name: string, email: string, password: string, password_confirmation: string) {
    loading.value = true
    error.value = null
    try {
      const res = await api.post<{ user: AuthUser; token: string }>('/auth/register', {
        name, email, password, password_confirmation,
      })
      token.value = res.token
      user.value = res.user
      localStorage.setItem('auth_token', res.token)
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Erro ao cadastrar'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/auth/logout', {})
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
    }
  }

  async function fetchMe() {
    if (!token.value) return
    try {
      user.value = await api.get<AuthUser>('/auth/me')
    } catch {
      token.value = null
      localStorage.removeItem('auth_token')
    }
  }

  return { user, token, loading, error, isAuthenticated, initials, login, register, logout, fetchMe }
})
