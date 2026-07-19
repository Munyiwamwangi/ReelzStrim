<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/profile" :icon="arrowBack" />
        </ion-buttons>
        <ion-title>
          <span class="gradient-text text-lg font-bold">Watch History</span>
        </ion-title>
        <ion-buttons slot="end">
          <ion-button v-if="history.length > 0" @click="clearHistory" color="danger" size="small">
            Clear All
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <div class="p-4">
        <div v-if="loading" class="space-y-3">
          <div v-for="n in 5" :key="n" class="flex gap-3">
            <div class="w-16 h-24 rounded-xl skeleton" />
            <div class="flex-1 space-y-2">
              <div class="h-4 w-3/4 rounded skeleton" />
              <div class="h-3 w-1/2 rounded skeleton" />
              <div class="h-2 w-full rounded skeleton" />
            </div>
          </div>
        </div>

        <div v-else-if="history.length === 0" class="text-center py-16">
          <ion-icon :icon="timeOutline" class="text-6xl text-gray-600 mb-4" />
          <h3 class="text-white text-lg font-semibold mb-2">No history yet</h3>
          <p class="text-gray-500 text-sm">Start watching dramas to build your history!</p>
          <ion-button class="mt-4 gradient-bg" @click="navigateToFeed">Start Watching</ion-button>
        </div>

        <div v-else class="space-y-2">
          <div
            v-for="item in history"
            :key="item.id"
            class="glass rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:bg-purple-500/10 transition-colors active:bg-purple-500/20"
            @click="watchHistoryItem(item)"
          >
            <div class="w-16 h-24 rounded-lg overflow-hidden flex-shrink-0 bg-gray-800 group relative">
              <img
                :src="item.drama?.poster_url"
                :alt="item.drama?.title"
                class="w-full h-full object-cover"
                @error="onImgError"
              />
              <!-- Play overlay -->
              <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <ion-icon :icon="play" class="text-white text-xl" />
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-white text-sm font-medium line-clamp-1">{{ item.drama?.title || 'Unknown Drama' }}</p>
              <p class="text-gray-500 text-xs mt-1">Episode {{ item.episode?.episode_number || 'N/A' }}</p>
              <div class="mt-2">
                <div class="h-1.5 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full bg-purple-500 rounded-full" :style="{ width: `${item.progress || 0}%` }" />
                </div>
              </div>
              <p class="text-gray-600 text-xs mt-1">{{ item.progress || 0 }}% complete</p>
            </div>
            <ion-icon :icon="chevronForward" class="text-gray-500 flex-shrink-0" />
          </div>
        </div>
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
  IonButtons,
  IonBackButton,
} from '@ionic/vue'
import { arrowBack, timeOutline, chevronForward, play } from 'ionicons/icons'
import { useDramasStore } from '@/stores/dramas'

const router = useRouter()
const dramasStore = useDramasStore()
const loading = ref(true)

const history = computed(() => dramasStore.watchHistory)

function onImgError(e) {
  e.target.style.display = 'none'
}

function navigateToFeed() {
  router.push('/feed')
}

function watchHistoryItem(item) {
  if (item.drama_id && item.episode_id) {
    router.push(`/drama/${item.drama_id}/watch/${item.episode_id}`)
  }
}

async function clearHistory() {
  await dramasStore.clearHistory()
}

onMounted(async () => {
  await dramasStore.fetchHistory()
  loading.value = false
})
</script>