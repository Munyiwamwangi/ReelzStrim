import axios from 'axios'

// Determine the API base URL based on environment
const getBaseURL = () => {
  // Check if running as a Capacitor app (Android/iOS)
  const capacitor = typeof window !== 'undefined' && window?.Capacitor
  
  if (capacitor) {
    // Android emulator uses 10.0.2.2 to access host machine
    // Physical device uses your machine's actual IP address
    return 'http://10.0.2.2:8000/api'
  }
  
  // Web development - use Vite proxy or environment variable
  return import.meta.env.VITE_API_URL || '/api'
}

const api = axios.create({
  baseURL: getBaseURL(),
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      window.location.href = '/auth'
    }
    return Promise.reject(error)
  }
)

export default api

// Auth
export const authAPI = {
  register: (data) => api.post('/auth/register', data),
  login: (data) => api.post('/auth/login', data),
  logout: () => api.post('/auth/logout'),
  user: () => api.get('/auth/user'),
  updateProfile: (data) => api.put('/auth/profile', data),
}

// Dramas
export const dramasAPI = {
  list: (params) => api.get('/dramas', { params }),
  show: (id) => api.get(`/dramas/${id}`),
  episodes: (id) => api.get(`/dramas/${id}/episodes`),
  episode: (id, episodeId) => api.get(`/dramas/${id}/episodes/${episodeId}`),
  trending: () => api.get('/dramas/trending'),
  recent: () => api.get('/dramas/recent'),
  featured: () => api.get('/dramas/featured'),
  categories: () => api.get('/dramas/categories'),
  search: (query) => api.get('/dramas/search', { params: { q: query } }),
  highlyRated: () => api.get('/dramas/highly-rated'),
  mostWatched: () => api.get('/dramas/most-watched'),
  recommendations: () => api.get('/dramas/recommendations'),
}

// Favorites
export const favoritesAPI = {
  list: () => api.get('/favorites'),
  add: (dramaId) => api.post('/favorites', { drama_id: dramaId }),
  remove: (dramaId) => api.delete(`/favorites/${dramaId}`),
  check: (dramaId) => api.get(`/favorites/check/${dramaId}`),
}

// Watch History
export const historyAPI = {
  list: () => api.get('/history'),
  update: (episodeId, progress, dramaId) =>
    api.post('/history', { episode_id: episodeId, progress, drama_id: dramaId }),
  clear: () => api.delete('/history'),
}
