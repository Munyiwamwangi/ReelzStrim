<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-title class="text-center">
          <span class="gradient-text text-xl font-bold">RStream</span>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :scroll-y="true">
      <div v-if="loading" class="flex items-center justify-center h-64">
        <ion-spinner color="primary" />
      </div>

      <template v-if="!loading">
        <!-- TOP: Continue Watching or Top Pick -->
        <section class="px-4 pt-4 pb-3">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-white text-lg font-bold">
              {{ continueWatching.length > 0 ? '▶️ Continue Watching' : '⭐ Top Pick For You' }}
            </h2>
            <span v-if="continueWatching.length > 0" class="text-purple-400 text-xs font-medium">See all</span>
          </div>
          <div class="relative rounded-2xl overflow-hidden h-44 cursor-pointer group" @click="navigateToDrama(topPick.id)">
            <img :src="topPick.banner_url || topPick.poster_url" :alt="topPick.title" class="absolute inset-0 w-full h-full object-cover" @error="onImgError" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent" />
            <div class="absolute bottom-0 left-0 right-0 p-4">
              <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="topPick.category?.color || 'bg-purple-500/30 text-purple-300'">{{ topPick.category?.name || 'Drama' }}</span>
                <span class="flex items-center gap-1 text-xs text-yellow-400"><ion-icon :icon="star" /> {{ topPick.rating }}</span>
              </div>
              <h3 class="text-white text-lg font-bold">{{ topPick.title }}</h3>
              <p class="text-gray-300 text-sm mt-1 line-clamp-1">{{ topPick.description }}</p>
              <div class="flex items-center gap-3 mt-2">
                <ion-button size="small" class="gradient-bg text-xs h-8" @click.stop="playTopPick">
                  <ion-icon :icon="play" class="mr-1" />
                  {{ continueWatching.length > 0 ? 'Continue' : 'Watch Now' }}
                </ion-button>
                <span class="text-gray-500 text-xs">{{ topPick.episode_count || 0 }} episodes</span>
              </div>
            </div>
          </div>
        </section>

        <!-- TRENDING -->
        <section class="px-4 pb-3">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-white text-lg font-bold">🔥 Trending</h2>
            <button class="text-purple-400 text-xs font-medium" @click="router.push('/browse')">See all</button>
          </div>
          <div class="horizontal-scroll flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <div v-for="drama in trendingDramas" :key="drama.id" class="flex-shrink-0 w-32" @click="navigateToDrama(drama.id)">
              <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                <img :src="drama.poster_url" :alt="drama.title" class="absolute inset-0 w-full h-full object-cover" loading="lazy" @error="onImgError" />
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <ion-icon :icon="play" class="text-white text-2xl" />
                </div>
                <div class="absolute top-1.5 left-1.5">
                  <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-purple-600/90 text-white">#{{ trendingDramas.indexOf(drama) + 1 }}</span>
                </div>
              </div>
              <h3 class="text-white text-xs font-medium mt-1.5 line-clamp-1">{{ drama.title }}</h3>
              <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                <ion-icon :icon="star" class="text-yellow-400 text-[10px]" /> <span>{{ drama.rating }}</span>
              </div>
            </div>
          </div>
        </section>

        <!-- HIGHLY RATED -->
        <section class="px-4 pb-3">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-white text-lg font-bold">🏆 Highly Rated</h2>
            <button class="text-purple-400 text-xs font-medium" @click="router.push('/browse')">See all</button>
          </div>
          <div class="horizontal-scroll flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <div v-for="drama in highlyRated" :key="drama.id" class="flex-shrink-0 w-32" @click="navigateToDrama(drama.id)">
              <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                <img :src="drama.poster_url" :alt="drama.title" class="absolute inset-0 w-full h-full object-cover" loading="lazy" @error="onImgError" />
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <ion-icon :icon="play" class="text-white text-2xl" />
                </div>
                <div class="absolute top-1.5 right-1.5">
                  <span class="px-1.5 py-0.5 rounded text-xs font-bold bg-yellow-500/90 text-black">⭐ {{ drama.rating }}</span>
                </div>
              </div>
              <h3 class="text-white text-xs font-medium mt-1.5 line-clamp-1">{{ drama.title }}</h3>
              <p class="text-gray-500 text-[10px] mt-0.5">{{ drama.category?.name || 'Drama' }}</p>
            </div>
          </div>
        </section>

        <!-- MOST WATCHED -->
        <section class="px-4 pb-3">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-white text-lg font-bold">👀 Most Watched</h2>
            <button class="text-purple-400 text-xs font-medium" @click="router.push('/browse')">See all</button>
          </div>
          <div class="horizontal-scroll flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <div v-for="drama in mostWatched" :key="drama.id" class="flex-shrink-0 w-32" @click="navigateToDrama(drama.id)">
              <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                <img :src="drama.poster_url" :alt="drama.title" class="absolute inset-0 w-full h-full object-cover" loading="lazy" @error="onImgError" />
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <ion-icon :icon="play" class="text-white text-2xl" />
                </div>
                <div class="absolute bottom-1.5 left-1.5 right-1.5">
                  <div class="flex items-center gap-1 text-[10px] text-white/80 bg-black/50 rounded px-1.5 py-0.5 backdrop-blur-sm">
                    <ion-icon :icon="playCircle" class="text-[10px]" /> <span>{{ drama.episode_count || 0 }} eps</span>
                  </div>
                </div>
              </div>
              <h3 class="text-white text-xs font-medium mt-1.5 line-clamp-1">{{ drama.title }}</h3>
              <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                <ion-icon :icon="star" class="text-yellow-400 text-[10px]" /> <span>{{ drama.rating }}</span>
              </div>
            </div>
          </div>
        </section>

        <div class="h-24" />
      </template>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import { useRouter } from "vue-router"
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonIcon, IonSpinner, IonButton,
} from "@ionic/vue"
import { star, playCircle, play } from "ionicons/icons"
import { useDramasStore } from "@/stores/dramas"
import { useAuthStore } from "@/stores/auth"

const router = useRouter()
const dramasStore = useDramasStore()
const authStore = useAuthStore()
const loading = ref(true)

const trendingDramas = computed(() => dramasStore.trending.slice(0, 10))
const featuredDramas = computed(() => dramasStore.featured)
const recentDramas = computed(() => dramasStore.recent)
const watchHistory = computed(() => dramasStore.watchHistory)
const favorites = computed(() => dramasStore.favorites)

const continueWatching = computed(() => watchHistory.value.filter(h => h.progress < 100).slice(0, 5))

const topPick = computed(() => {
  if (continueWatching.value.length > 0) return continueWatching.value[0]
  const all = [...featuredDramas.value, ...trendingDramas.value]
  const seen = new Set()
  const unique = all.filter(d => { if (seen.has(d.id)) return false; seen.add(d.id); return true })
  unique.sort((a, b) => parseFloat(b.rating || 0) - parseFloat(a.rating || 0))
  return unique[0] || null
})

const highlyRated = computed(() => dramasStore.highlyRated || [])
const mostWatched = computed(() => dramasStore.mostWatched || [])

function onImgError(e) { e.target.style.display = "none" }
function navigateToDrama(id) { if (id) router.push(`/drama/${id}`) }

async function playTopPick() {
  if (!topPick.value) return
  const drama = topPick.value
  // If continue watching, navigate to the last episode they were on
  if (continueWatching.value.length > 0) {
    const lastEntry = continueWatching.value[0]
    if (lastEntry.episode_id) {
      router.push(`/drama/${drama.id}/watch/${lastEntry.episode_id}`)
      return
    }
  }
  // Otherwise fetch episodes and play the first one
  await dramasStore.fetchEpisodes(drama.id)
  const episodes = dramasStore.episodes
  if (episodes && episodes.length > 0) {
    router.push(`/drama/${drama.id}/watch/${episodes[0].id}`)
  } else {
    // Fallback: go to drama detail page
    router.push(`/drama/${drama.id}`)
  }
}

onMounted(async () => {
  loading.value = true
  const promises = [
    dramasStore.fetchTrending(),
    dramasStore.fetchFeatured(),
    dramasStore.fetchRecent(),
    dramasStore.fetchCategories(),
    dramasStore.fetchHighlyRated(),
    dramasStore.fetchMostWatched(),
  ]
  if (authStore.isAuthenticated) {
    promises.push(dramasStore.fetchFavorites())
    promises.push(dramasStore.fetchHistory())
    promises.push(dramasStore.fetchRecommendations())
  }
  await Promise.all(promises)
  loading.value = false
})
</script>

<style scoped>
.horizontal-scroll {
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
}
.scrollbar-hide::-webkit-scrollbar { display: none }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none }
</style>