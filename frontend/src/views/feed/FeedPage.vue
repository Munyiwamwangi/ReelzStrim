<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-title class="text-center">
          <span class="gradient-text text-xl font-bold">RStream</span>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content :scroll-y="false">
      <div
        class="video-feed"
        ref="feedContainer"
        @touchstart="onTouchStart"
        @touchmove="onTouchMove"
        @touchend="onTouchEnd"
      >
        <div
          v-for="(drama, index) in feedDramas"
          :key="drama.id"
          class="video-item"
          :style="{ transform: `translateY(${(index - currentIndex) * 100}%)` }"
        >
          <!-- Video Placeholder / Poster (always visible as background) -->
          <div class="video-placeholder">
            <img
              :src="drama.poster_url"
              :alt="drama.title"
              class="absolute inset-0 w-full h-full object-cover"
              @error="onImgError"
            />
            <!-- Play button overlay on poster -->
            <div 
              v-if="!hasVideoSrc(drama)"
              class="absolute inset-0 flex items-center justify-center bg-black/20 cursor-pointer"
              @click="navigateToDrama(drama.id)"
            >
              <div class="w-16 h-16 rounded-full glass flex items-center justify-center">
                <ion-icon :icon="play" class="text-white text-3xl" />
              </div>
            </div>
          </div>

          <!-- Video Player (on top if available) -->
          <video
            v-if="hasVideoSrc(drama)"
            :ref="el => videoPlayer = index === currentIndex ? el : null"
            class="absolute inset-0 w-full h-full object-cover"
            :src="drama.video_url || drama.trailer_url"
            :poster="drama.poster_url"
            loop
            playsinline
            muted
            @click="togglePlay"
            @play="videoPlaying = true"
            @pause="videoPlaying = false"
          />

          <!-- Gradient Overlay -->
          <div class="video-overlay" />

          <!-- Drama Info -->
          <div class="video-info">
            <div class="flex items-end justify-between">
              <div class="flex-1 mr-4">
                <h2 class="text-white text-xl font-bold mb-1">
                  {{ drama.title }}
                </h2>
                <div class="flex items-center gap-3 text-sm text-gray-300 mb-2">
                  <span class="flex items-center gap-1">
                    <ion-icon :icon="star" class="text-yellow-400" />
                    {{ drama.rating || "N/A" }}
                  </span>
                  <span>{{ drama.year || "" }}</span>
                  <span
                    class="px-2 py-0.5 rounded-full text-xs"
                    :class="
                      drama.category?.color ||
                      'bg-purple-500/30 text-purple-300'
                    "
                  >
                    {{ drama.category?.name || "Drama" }}
                  </span>
                </div>
                <p class="text-gray-400 text-sm line-clamp-2">
                  {{ drama.description }}
                </p>
              </div>

              <!-- Side Actions -->
              <div class="flex flex-col items-center gap-5">
                <button
                  class="flex flex-col items-center gap-1"
                  @click.stop="toggleFavorite(drama.id)"
                >
                  <div
                    class="w-12 h-12 rounded-full glass flex items-center justify-center"
                  >
                    <ion-icon
                      :icon="isFav(drama.id) ? heart : heartOutline"
                      :class="isFav(drama.id) ? 'text-pink-500' : 'text-white'"
                      size="24"
                    />
                  </div>
                  <span class="text-xs text-gray-400">Favorite</span>
                </button>

                <button
                  class="flex flex-col items-center gap-1"
                  @click.stop="shareDrama(drama)"
                >
                  <div
                    class="w-12 h-12 rounded-full glass flex items-center justify-center"
                  >
                    <ion-icon
                      :icon="shareOutline"
                      class="text-white"
                      size="24"
                    />
                  </div>
                  <span class="text-xs text-gray-400">Share</span>
                </button>

                <ion-button
                  fill="clear"
                  @click.stop="navigateToDrama(drama.id)"
                  class="flex flex-col items-center gap-1 p-0 h-auto min-height-0"
                >
                  <div
                    class="w-12 h-12 rounded-full glass flex items-center justify-center"
                  >
                    <ion-icon
                      :icon="informationCircleOutline"
                      class="text-white"
                      size="24"
                    />
                  </div>
                  <span class="text-xs text-gray-400">Info</span>
                </ion-button>
              </div>
            </div>

            <!-- Episode Info -->
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
              <ion-icon :icon="playCircle" />
              <span>{{ drama.episode_count || 0 }} episodes</span>
              <span class="mx-1">•</span>
              <span>{{ drama.duration || "N/A" }}</span>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div
          v-if="loading"
          class="absolute inset-0 flex items-center justify-center bg-black/50 z-50"
        >
          <ion-spinner color="primary" />
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from "vue";
import { useRouter } from "vue-router";
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonIcon,
  IonSpinner,
  IonButton,
} from "@ionic/vue";
import {
  heart,
  heartOutline,
  shareOutline,
  informationCircleOutline,
  star,
  playCircle,
  play,
} from "ionicons/icons";
import { useDramasStore } from "@/stores/dramas";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const dramasStore = useDramasStore();
const authStore = useAuthStore();

const feedContainer = ref(null);
const videoPlayer = ref(null);
const currentIndex = ref(0);
const loading = ref(false);
const touchStartY = ref(0);
const touchDelta = ref(0);
const isSwiping = ref(false);
const videoPlaying = ref(false);

const feedDramas = computed(() => {
  return dramasStore.trending.length > 0
    ? dramasStore.trending
    : dramasStore.featured;
});

const isFav = (id) => dramasStore.isFavorite(id);

function hasVideoSrc(drama) {
  return !!(drama.video_url || drama.trailer_url);
}

function onImgError(e) {
  e.target.style.display = "none";
}

function onTouchStart(e) {
  touchStartY.value = e.touches[0].clientY;
  isSwiping.value = true;
}

function onTouchMove(e) {
  if (!isSwiping.value) return;
  touchDelta.value = e.touches[0].clientY - touchStartY.value;
}

function onTouchEnd() {
  isSwiping.value = false;
  if (Math.abs(touchDelta.value) > 80) {
    if (
      touchDelta.value < 0 &&
      currentIndex.value < feedDramas.value.length - 1
    ) {
      currentIndex.value++;
    } else if (touchDelta.value > 0 && currentIndex.value > 0) {
      currentIndex.value--;
    }
  }
  touchDelta.value = 0;
}

function togglePlay() {
  if (!videoPlayer.value) return;
  if (videoPlayer.value.paused) {
    videoPlayer.value.play();
  } else {
    videoPlayer.value.pause();
  }
}

function navigateToDrama(id) {
  router.push(`/drama/${id}`);
}

function toggleFavorite(id) {
  if (!authStore.isAuthenticated) {
    router.push("/auth");
    return;
  }
  dramasStore.toggleFavorite(id);
}

function shareDrama(drama) {
  if (navigator.share) {
    navigator.share({
      title: drama.title,
      text: drama.description,
      url: `${window.location.origin}/drama/${drama.id}`,
    });
  }
}

watch(currentIndex, async (newIdx, oldIdx) => {
  await nextTick();
  if (videoPlayer.value) {
    videoPlayer.value.currentTime = 0;
    videoPlayer.value.play().catch(() => {});
  }
});

onMounted(async () => {
  loading.value = true;
  const promises = [dramasStore.fetchTrending(), dramasStore.fetchFeatured()];

  if (authStore.isAuthenticated) {
    promises.push(dramasStore.fetchFavorites());
  }

  await Promise.all(promises);
  loading.value = false;

  await nextTick();
  if (videoPlayer.value) {
    videoPlayer.value.play().catch(() => {});
  }
});
</script>