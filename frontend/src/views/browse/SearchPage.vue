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
                  <!-- Play overlay -->
                  <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <ion-icon :icon="play" class="text-white text-xl" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-white font-medium line-clamp-1">{{ drama.title }}</h3>
                  <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                    <span class="text-yellow-400">★ {{ drama.rating || 'N/A' }}</span>
                    <span>{{ drama.year || '' }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-purple-500/30 text-purple-300 text-xs">{{ drama.category?.name || 'Drama' }}</span>
                  </div>
                  <p class="text-gray-500 text-xs mt-2 line-clamp-2">{{ drama.description }}</p>
                  <p class="text-gray-600 text-xs mt-1">{{ drama.episode_count || 0 }} episodes</p>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Trending Suggestions -->
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

function onImgError(e) {
  e.target.style.display = 'none'
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
</script>