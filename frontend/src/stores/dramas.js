import { defineStore } from 'pinia'
import { dramasAPI, favoritesAPI, historyAPI } from '@/services/api'

export const useDramasStore = defineStore('dramas', {
  state: () => ({
    dramas: [],
    trending: [],
    featured: [],
    recent: [],
    currentDrama: null,
    episodes: [],
    currentEpisode: null,
    categories: [],
    searchResults: [],
    favorites: [],
    watchHistory: [],
    loading: false,
    error: null,
  }),

  getters: {
    isFavorite: (state) => {
      return (dramaId) => state.favorites.some((f) => f.id === dramaId)
    },
    continueWatching: (state) => {
      return state.watchHistory.filter((h) => h.progress < 100).slice(0, 10)
    },
  },

  actions: {
    async fetchTrending() {
      try {
        const { data } = await dramasAPI.trending()
        this.trending = data.dramas || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch trending'
      }
    },

    async fetchFeatured() {
      try {
        const { data } = await dramasAPI.featured()
        this.featured = data.dramas || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch featured'
      }
    },

    async fetchRecent() {
      try {
        const { data } = await dramasAPI.recent()
        this.recent = data.dramas || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch recent'
      }
    },

    async fetchCategories() {
      try {
        const { data } = await dramasAPI.categories()
        this.categories = data.categories || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch categories'
      }
    },

    async fetchDramas(params = {}) {
      this.loading = true
      try {
        const { data } = await dramasAPI.list(params)
        this.dramas = data.dramas || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch dramas'
      } finally {
        this.loading = false
      }
    },

    async fetchDrama(id) {
      this.loading = true
      try {
        const { data } = await dramasAPI.show(id)
        this.currentDrama = data.drama || data.data || data
        return this.currentDrama
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch drama'
        return null
      } finally {
        this.loading = false
      }
    },

    async fetchEpisodes(dramaId) {
      try {
        const { data } = await dramasAPI.episodes(dramaId)
        this.episodes = data.episodes || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch episodes'
      }
    },

    async fetchEpisode(dramaId, episodeId) {
      try {
        const { data } = await dramasAPI.episode(dramaId, episodeId)
        this.currentEpisode = data.episode || data.data || data
        return this.currentEpisode
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch episode'
        return null
      }
    },

    async search(query) {
      this.loading = true
      try {
        const { data } = await dramasAPI.search(query)
        this.searchResults = data.dramas || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Search failed'
      } finally {
        this.loading = false
      }
    },

    async fetchFavorites() {
      try {
        const { data } = await favoritesAPI.list()
        this.favorites = data.favorites || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch favorites'
      }
    },

    async toggleFavorite(dramaId) {
      const isFav = this.isFavorite(dramaId)
      try {
        if (isFav) {
          await favoritesAPI.remove(dramaId)
          this.favorites = this.favorites.filter((f) => f.id !== dramaId)
        } else {
          const { data } = await favoritesAPI.add(dramaId)
          this.favorites.push(data.favorite || data)
        }
        return true
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to toggle favorite'
        return false
      }
    },

    async fetchHistory() {
      try {
        const { data } = await historyAPI.list()
        this.watchHistory = data.history || data.data || data
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to fetch history'
      }
    },

    async updateProgress(episodeId, progress, dramaId) {
      try {
        await historyAPI.update(episodeId, progress, dramaId)
      } catch {
        // silently fail
      }
    },

    async clearHistory() {
      try {
        await historyAPI.clear()
        this.watchHistory = []
      } catch {
        // silently fail
      }
    },
  },
})
