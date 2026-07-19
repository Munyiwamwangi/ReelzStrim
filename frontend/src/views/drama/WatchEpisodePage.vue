<template>
  <ion-page>
    <ion-content :scroll-y="false" class="watch-content">
      <!-- Video Player -->
      <div class="video-container" ref="videoContainer">
        <video
          ref="videoPlayer"
          class="w-full h-full object-contain"
          :src="episode?.video_url"
          controls
          playsinline
          @timeupdate="onTimeUpdate"
          @loadedmetadata="onLoaded"
        />
        
        <!-- Loading Overlay -->
        <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-black z-10">
          <div class="text-center">
            <ion-spinner color="primary" class="mb-4" />
            <p class="text-gray-400 text-sm">Loading episode...</p>
          </div>
        </div>

        <!-- Top Bar -->
        <div class="absolute top-0 left-0 right-0 z-20 p-4 safe-area-top">
          <div class="flex items-center justify-between">
            <ion-button fill="clear" @click="goBack" class="m-0">
              <ion-icon :icon="arrowBack" class="text-white text-2xl" />
            </ion-button>
            <div class="text-right">
              <p class="text-white text-sm font-medium">{{ drama?.title }}</p>
              <p class="text-gray-400 text-xs">Episode {{ episodeNumber }}</p>
            </div>
          </div>
        </div>

        <!-- Bottom Controls -->
        <div class="absolute bottom-0 left-0 right-0 z-20 p-4 safe-area-bottom">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <ion-button fill="clear" @click="previousEpisode" :disabled="!hasPrevious" class="m-0">
                <ion-icon :icon="chevronBack" class="text-white text-xl" />
              </ion-button>
              <span class="text-white text-sm">{{ currentTimeDisplay }} / {{ durationDisplay }}</span>
              <ion-button fill="clear" @click="nextEpisode" :disabled="!hasNext" class="m-0">
                <ion-icon :icon="chevronForward" class="text-white text-xl" />
              </ion-button>
            </div>
            <div class="flex items-center gap-2">
              <ion-button fill="clear" @click="toggleFavorite">
                <ion-icon
                  :icon="isFav ? heart : heartOutline"
                  :class="isFav ? 'text-pink-500' : 'text-white'"
                  class="text-xl"
                />
              </ion-button>
            </div>
          </div>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  IonPage,
  IonContent,
  IonIcon,
  IonButton,
  IonSpinner,
} from '@ionic/vue'
import {
  arrowBack,
  chevronBack,
  chevronForward,
  heart,
  heartOutline,
} from 'ionicons/icons'
import { useDramasStore } from "@/stores/dramas";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const router = useRouter();
const dramasStore = useDramasStore();
const authStore = useAuthStore();

const videoContainer = ref(null);
const videoPlayer = ref(null);
const loading = ref(true);
const currentTime = ref(0);
const duration = ref(0);

const drama = computed(() => dramasStore.currentDrama);
const episodes = computed(() => dramasStore.episodes);
const episode = computed(() => dramasStore.currentEpisode);
const episodeNumber = computed(() => {
  const idx = episodes.value.findIndex((e) => e.id === episode.value?.id);
  return idx >= 0 ? idx + 1 : 1;
});
const hasPrevious = computed(() => episodeNumber.value > 1);
const hasNext = computed(() => episodeNumber.value < episodes.value.length);
const isFav = computed(() => dramasStore.isFavorite(drama.value?.id));

const currentTimeDisplay = computed(() => formatTime(currentTime.value))
const durationDisplay = computed(() => formatTime(duration.value))

function formatTime(seconds) {
  const m = Math.floor(seconds / 60)
  const s = Math.floor(seconds % 60)
  return `${m}:${s.toString().padStart(2, '0')}`
}

function onTimeUpdate() {
  if (videoPlayer.value) {
    currentTime.value = videoPlayer.value.currentTime
  }
}

function onLoaded() {
  if (videoPlayer.value) {
    duration.value = videoPlayer.value.duration
  }
}

function goBack() {
  router.back()
}

function toggleFavorite() {
  if (!authStore.isAuthenticated) {
    router.push("/auth");
    return;
  }
  if (drama.value) {
    dramasStore.toggleFavorite(drama.value.id);
  }
}

async function previousEpisode() {
  const idx = episodes.value.findIndex((e) => e.id === episode.value?.id)
  if (idx > 0) {
    const prev = episodes.value[idx - 1]
    router.replace(`/drama/${route.params.id}/watch/${prev.id}`)
  }
}

async function nextEpisode() {
  const idx = episodes.value.findIndex((e) => e.id === episode.value?.id)
  if (idx < episodes.value.length - 1) {
    const next = episodes.value[idx + 1]
    router.replace(`/drama/${route.params.id}/watch/${next.id}`)
  }
}

// Save progress periodically (only for authenticated users)
let progressInterval = null
watch(episode, (ep) => {
  if (ep && authStore.isAuthenticated) {
    progressInterval = setInterval(() => {
      if (videoPlayer.value && !videoPlayer.value.paused) {
        const progress = Math.round((videoPlayer.value.currentTime / videoPlayer.value.duration) * 100)
        dramasStore.updateProgress(ep.id, progress, route.params.id)
      }
    }, 10000)
  }
})

onMounted(async () => {
  const { id, episodeId } = route.params
  await dramasStore.fetchDrama(id)
  await dramasStore.fetchEpisodes(id)
  await dramasStore.fetchEpisode(id, episodeId)
  loading.value = false
})

onUnmounted(() => {
  if (progressInterval) clearInterval(progressInterval)
})
</script>

<style scoped>
.watch-content {
  --background: #000;
}

.video-container {
  position: relative;
  width: 100%;
  height: 100%;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.safe-area-top {
  padding-top: env(safe-area-inset-top);
}

.safe-area-bottom {
  padding-bottom: env(safe-area-inset-bottom);
}

video::-webkit-media-controls {
  z-index: 5;
}
</style>