import { defineStore } from 'pinia'
import { authAPI } from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.name || 'User',
    userEmail: (state) => state.user?.email || '',
    userAvatar: (state) =>
      state.user?.avatar_url || `https://ui-avatars.com/api/?name=${state.user?.name || 'U'}&background=7c3aed&color=fff`,
  },

  actions: {
    async login(credentials) {
      this.loading = true
      this.error = null
      try {
        const { data } = await authAPI.login(credentials)
        this.token = data.token
        this.user = data.user
        localStorage.setItem('auth_token', data.token)
        return true
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed'
        return false
      } finally {
        this.loading = false
      }
    },

    async register(credentials) {
      this.loading = true
      this.error = null
      try {
        const { data } = await authAPI.register(credentials)
        this.token = data.token
        this.user = data.user
        localStorage.setItem('auth_token', data.token)
        return true
      } catch (err) {
        this.error = err.response?.data?.message || 'Registration failed'
        return false
      } finally {
        this.loading = false
      }
    },

    async fetchUser() {
      if (!this.token) return
      try {
        const { data } = await authAPI.user()
        this.user = data.user || data
      } catch {
        this.logout()
      }
    },

    async updateProfile(profileData) {
      try {
        const { data } = await authAPI.updateProfile(profileData)
        this.user = data.user || data
        return true
      } catch (err) {
        this.error = err.response?.data?.message || 'Update failed'
        return false
      }
    },

    async logout() {
      try {
        await authAPI.logout()
      } catch {
        // ignore
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('auth_token')
      }
    },
  },
})