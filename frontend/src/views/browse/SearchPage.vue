<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-title class="text-center">
          <span class="gradient-text text-xl font-bold">Search</span>
        </ion-title>
      </ion-toolbar>
      <ion-toolbar>
      <ion-searchbar
        v-model="query"
        placeholder="Search dramas..."
        debounce="300"
        @ionInput="onSearch"
        animated
        class="px-2"
      />
    </ion-toolbar>

    <!-- Categories pills - scrollable single line -->
    <ion-toolbar class="ion-no-border">
      <div class="flex gap-2 overflow-x-auto pb-2 px-2 scrollbar-hide categories-row">
        <button
          v-for="cat in categories"
          :key="cat.id"
          class="px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all"
          :class="selectedCategory === cat.id ? 'gradient-bg text-white' : 'glass text-gray-300 hover:text-white'"
          @click="selectCategory(cat.id)"
        >
          {{ cat.name }}
        </button>
      </div>
    </ion-toolbar>
    </ion-header>

    <ion-content>
      <div class="p-4">
        <!-- Search Results -->
        <div v-if="query.length > 0" class="space-y-4">
          <p class="text-gray-400 text-sm" v-if="!loading">
            {{ results.length }} result{{ results.length !== 1 ? 's' : '' }} for "{{ query }}"
          </p>

          <div v-if="loading" class="space-y-4">
            <div v-for="n in 5" :key="n" class="flex gap-3">
              <div class="w-20 h-28 rounded-xl skeleton" />
              <div class="flex-1 space-y-2">
                <div class="h-4 w-3/4 rounded skeleton" />
                <div class="h-3 w-1/2 rounded skeleton" />
                <div class="h-3 w-full rounded skeleton" />
              </div>
            </div>
          </div>

          <div v-else-if="results.length === 0" class="text-center py-12">
            <ion-icon :icon="searchOutline" class="text-6xl text-gray-600 mb-4" />
            <p class="text-gray-500">No dramas found</p>
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="drama in results"
              :key="drama.id"
              class="glass rounded-xl p-2 flex items-center gap-3 cursor-pointer hover:bg-purple-500/10 transition-colors active:bg-purple-500/20"
            >
              <router-link :to="`/drama/${drama.id}`" class="flex gap-3 w-full">
                <div class="w-20 h-28 rounded-xl overflow-hidden flex-shrink-0 bg-gray-800 group relative">
                  <img
                    :src="drama.poster_url"
                    :alt="drama.title"
                    class="w-full h-full object-cover"
                    @error="onImgError"
                  />
                  <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <ion-icon :icon="play" class="text-white text-xl" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-white font-medium line-clamp-1">{{ drama.title }}</h3>
                  <div class="flex items-center gap-2 text-xs text-gray-500 mt-1 flex-wrap">
                    <span class="text-yellow-400">★ {{ drama.rating || 'N/A' }}</span>
                    <span>{{ drama.year || '' }}</span>
                    <!-- NEW badge for shows added within 30 days -->
                    <span v-if="isNewDrama(drama)" class="px-1.5 py-0.5 rounded bg-green-500/90 text-white text-xs font-bold">NEW</span>
                    <!-- Rating badge with color based on score -->
                    <span :class="getRatingBadgeClass(drama.rating)" class="px-1.5 py-0.5 rounded text-xs font-bold">
                      {{ drama.rating }}
                    </span>
                    <span :class="drama.category?.color || 'bg-purple-500/30 text-purple-300'" class="px-1.5 py-0.5 rounded text-xs">
                      {{ drama.category?.name || 'Drama' }}
                    </span>
                  </div>
                  <p class="text-gray-500 text-xs mt-2 line-clamp-2">{{ drama.description }}</p>
                  <p class="text-gray-600 text-xs mt-1">{{ drama.episode_count || 0 }} episodes</p>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Browse / Discover -->
        <div v-else-if="hasDiscoverContent">
          <!-- New Releases -->
          <section v-if="newDramas.length > 0" class="mb-6">
            <h3 class="text-white text-lg font-semibold mb-3">🆕 New Releases</h3>
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
              <div v-for="drama in newDramas" :key="drama.id" class="flex-shrink-0 w-32" @click="goToDrama(drama.id)">
                <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                  <img :src="drama.poster_url" :alt="drama.title" class="absolute inset-0 w-full h-full object-cover" loading="lazy" @error="onImgError" />
                  <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <ion-icon :icon="play" class="text-white text-2xl" />
                  </div>
                  <div class="absolute top-1.5 left-1.5">
                    <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-green-500/90 text-white">NEW</span>
                  </div>
                </div>
                <h3 class="text-white text-xs font-medium mt-1.5 line-clamp-1">{{ drama.title }}</h3>
                <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                  <ion-icon :icon="star" class="text-yellow-400 text-[10px]" /> <span>{{ drama.rating }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Highly Rated -->
          <section v-if="highlyRated.length > 0" class="mb-6">
            <h3 class="text-white text-lg font-semibold mb-3">🏆 Highly Rated</h3>
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
              <div v-for="drama in highlyRated" :key="drama.id" class="flex-shrink-0 w-32" @click="goToDrama(drama.id)">
                <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                  <img :src="drama.poster_url" :alt="drama.title" class="absolute inset-0 w-full h-full object-cover" loading="lazy" @error="onImgError" />
                  <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <ion-icon :icon="play" class="text-white text-2xl" />
                  </div>
                  <div class="absolute top-1.5 right-1.5">
                    <span :class="getRatingBadgeClass(drama.rating)" class="px-1.5 py-0.5 rounded text-xs font-bold">
                      ⭐ {{ drama.rating }}
                    </span>
                  </div>
                </div>
                <h3 class="text-white text-xs font-medium mt-1.5 line-clamp-1">{{ drama.title }}</h3>
                <p class="text-gray-500 text-[10px] mt-0.5">{{ drama.category?.name || 'Drama' }}</p>
              </div>
            </div>
          </section>
        </div>

        <!-- Popular Tags (fallback when no discover content) -->
        <div v-else>
          <h3 class="text-white text-lg font-semibold mb-4">Popular Searches</h3>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in popularTags"
              :key="tag"
              class="px-3 py-1.5 rounded-full text-sm glass text-gray-300 cursor-pointer hover:text-white transition-colors"
              @click="query = tag; onSearch()"
            >
              {{ tag }}
            </span>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref } from 'vue'
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonContent,
  IonSearchbar,
  IonIcon,
} from '@ionic/vue'
import { searchOutline, play } from 'ionicons/icons'
import { useDramasStore } from '@/stores/dramas'

const dramasStore = useDramasStore()
const query = ref('')
const loading = ref(false)
const results = ref([])

const popularTags = ['Romance', 'Action', 'Comedy', 'Thriller', 'Historical', 'Fantasy', 'Mystery', 'School']
const ONE_MONTH = 30 * 24 * 60 * 60 * 1000

function onImgError(e) {
  e.target.style.display = 'none'
}

function isNewDrama(drama) {
  if (!drama.created_at) return false
  const created = new Date(drama.created_at).getTime()
  return Date.now() - created < ONE_MONTH
}

function getRatingBadgeClass(rating) {
  const score = parseFloat(rating || 0)
  if (score >= 9.0) return 'bg-yellow-500/90 text-black'
  if (score >= 8.0) return 'bg-purple-500/80 text-white'
  if (score >= 7.0) return 'bg-blue-500/80 text-white'
  return 'bg-gray-500/80 text-white'
}

let searchTimeout = null
function onSearch() {
  clearTimeout(searchTimeout)
  if (!query.value.trim()) {
    results.value = []
    return
  }
  // Auto-trigger search when query is set from popular tags
  loading.value = true
  searchTimeout = setTimeout(async () => {
    await dramasStore.search(query.value)
    results.value = dramasStore.searchResults
    loading.value = false
  }, 300)
}

function selectCategory(catId) {
  if (selectedCategory.value === catId) {
    selectedCategory.value = null
  } else {
    selectedCategory.value = catId
  }
  if (query.value.trim()) {
    onSearch()
  }
}

function goToDrama(id) {
  router.push(`/drama/${id}`)
}

import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useDramasStore } from '@/stores/dramas'

const router = useRouter()
const dramasStore = useDramasStore()
const categories = ref([])
const newDramas = ref([])
const highlyRated = ref([])
const selectedCategory = ref(null)
const hasDiscoverContent = ref(false)

onMounted(async () => {
  loading.value = true
  await Promise.all([
    dramasStore.fetchCategories(),
    dramasStore.fetchRecent(),
    dramasStore.fetchHighlyRated(),
  ])
  categories.value = dramasStore.categories

  const recent = [...dramasStore.recent].sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
  newDramas.value = recent.filter(d => isNewDrama(d)).slice(0, 10)

  highlyRated.value = [...dramasStore.recent]
    .filter(d => parseFloat(d.rating || 0) >= 7.5)
    .sort((a, b) => parseFloat(b.rating || 0) - parseFloat(a.rating || 0))
    .slice(0, 10)

  hasDiscoverContent.value = newDramas.value.length > 0 || highlyRated.value.length > 0
  loading.value = false
})
</script>
