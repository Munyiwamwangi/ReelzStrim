<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-title class="text-center">
          <span class="gradient-text text-xl font-bold">Profile</span>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <div class="p-4 space-y-6">
        <!-- User Info Card -->
        <div class="glass rounded-2xl p-6 text-center">
          <div class="w-24 h-24 rounded-full mx-auto overflow-hidden border-4 border-purple-500/30">
            <img :src="authStore.userAvatar" :alt="authStore.userName" class="w-full h-full object-cover" />
          </div>
          <h2 class="gradient-text text-xl font-bold mt-4">{{ authStore.userName }}</h2>
          <p class="text-gray-400 text-sm">{{ authStore.userEmail }}</p>

          <div class="flex justify-center gap-6 mt-4">
            <div class="text-center">
              <p class="text-white text-xl font-bold">{{ stats.favorites }}</p>
              <p class="text-gray-500 text-xs">Favorites</p>
            </div>
            <div class="text-center">
              <p class="text-white text-xl font-bold">{{ stats.watched }}</p>
              <p class="text-gray-500 text-xs">Watched</p>
            </div>
            <div class="text-center">
              <p class="text-white text-xl font-bold">{{ stats.hours }}</p>
              <p class="text-gray-500 text-xs">Hours</p>
            </div>
          </div>
        </div>

        <!-- Menu Items -->
        <div class="space-y-2">
          <div class="glass rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-purple-500/10 transition-colors active:bg-purple-500/20" @click="navigateTo('/favorites')">
            <div class="w-10 h-10 rounded-xl bg-pink-500/20 flex items-center justify-center">
              <ion-icon :icon="heart" class="text-pink-500" />
            </div>
            <div class="flex-1">
              <p class="text-white font-medium">My Favorites</p>
              <p class="text-gray-500 text-xs">Dramas you've loved</p>
            </div>
            <ion-icon :icon="chevronForward" class="text-gray-500" />
          </div>

          <div class="glass rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-purple-500/10 transition-colors active:bg-purple-500/20" @click="navigateTo('/history')">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center">
              <ion-icon :icon="time" class="text-blue-500" />
            </div>
            <div class="flex-1">
              <p class="text-white font-medium">Watch History</p>
              <p class="text-gray-500 text-xs">Continue where you left off</p>
            </div>
            <ion-icon :icon="chevronForward" class="text-gray-500" />
          </div>

          <div class="glass rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-purple-500/10 transition-colors active:bg-purple-500/20" @click="showSettingsAlert">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 flex items-center justify-center">
              <ion-icon :icon="settings" class="text-purple-500" />
            </div>
            <div class="flex-1">
              <p class="text-white font-medium">Settings</p>
              <p class="text-gray-500 text-xs">App preferences</p>
            </div>
            <ion-icon :icon="chevronForward" class="text-gray-500" />
          </div>

          <div class="glass rounded-xl p-4 flex items-center gap-3 cursor-pointer hover:bg-red-500/10 transition-colors active:bg-red-500/20" @click="handleLogout">
            <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center">
              <ion-icon :icon="logOut" class="text-red-500" />
            </div>
            <div class="flex-1">
              <p class="text-white font-medium">Sign Out</p>
              <p class="text-gray-500 text-xs">Log out of your account</p>
            </div>
            <ion-icon :icon="chevronForward" class="text-gray-500" />
          </div>
        </div>

        <!-- Continue Watching -->
        <section v-if="continueWatching.length > 0">
          <h3 class="text-white text-lg font-semibold mb-3">Continue Watching</h3>
          <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <div v-for="item in continueWatching" :key="item.id" class="flex-shrink-0 w-36">
              <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer" @click="watchHistoryItem(item)">
                <img
                  :src="item.drama?.poster_url"
                  :alt="item.drama?.title"
                  class="absolute inset-0 w-full h-full object-cover"
                  @error="onImgError"
                />
                <!-- Play overlay -->
                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <ion-icon :icon="play" class="text-white text-2xl" />
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                  <div class="h-full bg-purple-500" :style="{ width: `${item.progress}%` }" />
                </div>
              </div>
              <p class="text-white text-xs mt-2 line-clamp-1">{{ item.drama?.title || 'Drama' }}</p>
              <p class="text-gray-500 text-xs">{{ item.progress }}%</p>
            </div>
          </div>
        </section>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonIcon,
  IonButton,
  useIonRouter,
} from '@ionic/vue'
import {
  heart,
  time,
  settings,
  logOut,
  chevronForward,
  play,
} from 'ionicons/icons'
import { useAuthStore } from '@/stores/auth'
import { useDramasStore } from '@/stores/dramas'

const router = useRouter()
const ionRouter = useIonRouter()
const authStore = useAuthStore()
const dramasStore = useDramasStore()

const stats = ref({ favorites: 0, watched: 0, hours: 0 })
const continueWatching = computed(() => dramasStore.continueWatching)

function onImgError(e) {
  e.target.style.display = 'none'
}

function navigateTo(path) {
  router.push(path)
}

function watchHistoryItem(item) {
  if (item.drama_id && item.episode_id) {
    router.push(`/drama/${item.drama_id}/watch/${item.episode_id}`)
  }
}

function showSettingsAlert() {
  // Settings feature coming soon
  console.log('Settings clicked')
}

async function handleLogout() {
  await authStore.logout()
  router.replace('/auth')
}

onMounted(async () => {
  await Promise.all([
    dramasStore.fetchFavorites(),
    dramasStore.fetchHistory(),
  ])
  stats.value.favorites = dramasStore.favorites.length
  stats.value.watched = dramasStore.watchHistory.length
  stats.value.hours = Math.round(dramasStore.watchHistory.reduce((acc, h) => acc + (h.duration || 0), 0) / 3600)
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>