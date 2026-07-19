<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/profile" :icon="arrowBack" />
        </ion-buttons>
        <ion-title>
          <span class="gradient-text text-lg font-bold">My Favorites</span>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <div class="p-4">
        <div v-if="loading" class="grid grid-cols-2 gap-3">
          <div v-for="n in 6" :key="n" class="aspect-[2/3] rounded-xl skeleton" />
        </div>

        <div v-else-if="favorites.length === 0" class="text-center py-16">
          <ion-icon :icon="heart" class="text-6xl text-pink-500 mb-4" />
          <h3 class="text-white text-lg font-semibold mb-2">No favorites yet</h3>
          <p class="text-gray-500 text-sm">Start adding dramas to your favorites!</p>
          <ion-button class="mt-4 gradient-bg" @click="navigateToBrowse">Browse Dramas</ion-button>
        </div>

        <div v-else class="grid grid-cols-2 gap-3">
          <div v-for="drama in favorites" :key="drama.id" class="drama-card">
            <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer" @click="navigateToDrama(drama.id)">
              <img
                :src="drama.poster_url"
                :alt="drama.title"
                class="absolute inset-0 w-full h-full object-cover"
                @error="onImgError"
              />
              <!-- Play overlay -->
              <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <ion-icon :icon="play" class="text-white text-2xl" />
              </div>
              <button
                class="absolute top-2 right-2 w-8 h-8 rounded-full glass flex items-center justify-center z-10"
                @click.stop="removeFavorite(drama.id)"
              >
                <ion-icon :icon="heart" class="text-pink-500 text-sm" />
              </button>
              <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent">
                <p class="text-white text-xs font-medium line-clamp-1">{{ drama.title }}</p>
                <div class="flex items-center gap-1 text-xs text-gray-300">
                  <span>★ {{ drama.rating || 'N/A' }}</span>
                </div>
              </div>
            </div>
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
import { arrowBack, heart, play } from 'ionicons/icons'
import { useDramasStore } from '@/stores/dramas'

const router = useRouter()
const dramasStore = useDramasStore()
const loading = ref(true)

const favorites = computed(() => dramasStore.favorites)

function onImgError(e) {
  e.target.style.display = 'none'
}

function navigateToDrama(id) {
  router.push(`/drama/${id}`)
}

function navigateToBrowse() {
  router.push('/browse')
}

function removeFavorite(id) {
  dramasStore.toggleFavorite(id)
}

onMounted(async () => {
  await dramasStore.fetchFavorites()
  loading.value = false
})
</script>