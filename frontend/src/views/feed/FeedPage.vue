<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-title class="text-center">
          <span class="gradient-text text-xl font-bold">RStream</span>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content ref="ionContent" @ionScroll="onScroll">
      <!-- Trending Section - 3 items visible, 4th peeking -->
      <section class="trending-section">
        <div class="section-header px-4 pt-2 pb-3">
          <h2 class="text-white text-lg font-bold">🔥 Trending</h2>
          <span class="text-purple-400 text-xs">Swipe up</span>
        </div>

        <div
          class="video-feed"
          ref="feedContainer"
          @touchstart="onTouchStart"
          @touchmove="onTouchMove"
          @touchend="onTouchEnd"
        >
          <div
            v-for="(drama, index) in trendingDramas"
            :key="drama.id"
            class="video-item"
            :class="{ active: index === currentIndex, peek: index === currentIndex + 1, peek2: index === currentIndex + 2 }"
            :style="{ transform: `translateY(${(index - currentIndex) * 33.33}%)` }"
          >
            <!-- Poster -->
            <div class="video-placeholder">
              <img
                :src="drama.poster_url"
                :alt="drama.title"
                class="absolute inset-0 w-full h-full object-cover"
                @error="onImgError"
              />
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

            <!-- Video -->
            <video
              v-if="hasVideoSrc(drama)"
              :ref="el => { if (index === currentIndex) videoPlayerRef = el }"
              class="absolute inset-0 w-full h-full object-cover"
              :src="drama.video_url || drama.trailer_url"
              :poster="drama.poster_url"
              loop
              playsinline
              muted
              @click="togglePlay"
            />

            <!-- Overlay -->
            <div class="video-overlay" />

            <!-- Info -->
            <div class="video-info">
              <div class="flex items-end justify-between">
                <div class="flex-1 mr-4">
                  <h2 class="text-white text-xl font-bold mb-1">{{ drama.title }}</h2>
                  <div class="flex items-center gap-3 text-sm text-gray-300 mb-2">
                    <span class="flex items-center gap-1">
                      <ion-icon :icon="star" class="text-yellow-400" />
                      {{ drama.rating || "N/A" }}
                    </span>
                    <span>{{ drama.year || "" }}</span>
                    <span
                      class="px-2 py-0.5 rounded-full text-xs"
                      :class="drama.category?.color || 'bg-purple-500/30 text-purple-300'"
                    >
                      {{ drama.category?.name || "Drama" }}
                    </span>
                  </div>
                  <p class="text-gray-400 text-sm line-clamp-2">{{ drama.description }}</p>
                </div>

                <div class="flex flex-col items-center gap-5">
                  <button class="flex flex-col items-center gap-1" @click.stop="toggleFavorite(drama.id)">
                    <div class="w-12 h-12 rounded-full glass flex items-center justify-center">
                      <ion-icon
                        :icon="isFav(drama.id) ? heart : heartOutline"
                        :class="isFav(drama.id) ? 'text-pink-500' : 'text-white'"
                        size="24"
                      />
                    </div>
                    <span class="text-xs text-gray-400">Favorite</span>
                  </button>
                  <button class="flex flex-col items-center gap-1" @click.stop="shareDrama(drama)">
                    <div class="w-12 h-12 rounded-full glass flex items-center justify-center">
                      <ion-icon :icon="shareOutline" class="text-white" size="24" />
                    </div>
                    <span class="text-xs text-gray-400">Share</span>
                  </button>
                  <ion-button fill="clear" @click.stop="navigateToDrama(drama.id)" class="flex flex-col items-center gap-1 p-0 h-auto min-height-0">
                    <div class="w-12 h-12 rounded-full glass flex items-center justify-center">
                      <ion-icon :icon="informationCircleOutline" class="text-white" size="24" />
                    </div>
                    <span class="text-xs text-gray-400">Info</span>
                  </ion-button>
                </div>
              </div>
              <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                <ion-icon :icon="playCircle" />
                <span>{{ drama.episode_count || 0 }} episodes</span>
                <span class="mx-1">•</span>
                <span>{{ drama.duration || "N/A" }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- New Releases Section -->
      <section class="px-4 py-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-white text-lg font-bold">✨ New Releases</h2>
          <button class="text-purple-400 text-xs font-medium" @click="router.push('/browse')">See all</button>
        </div>
        <div class="horizontal-scroll flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
          <div
            v-for="drama in recentDramas"
            :key="drama.id"
            class="flex-shrink-0 w-36"
            @click="navigateToDrama(drama.id)"
          >
            <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
              <img
                :src="drama.poster_url"
                :alt="drama.title"
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
                @error="onImgError"
              />
              <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <ion-icon :icon="play" class="text-white text-2xl" />
              </div>
              <div class="absolute bottom-2 left-2 right-2">
                <span class="block px-2 py-1 rounded-lg text-xs bg-black/60 text-white text-center backdrop-blur-sm">
                  Ep {{ drama.latest_episode || 1 }}
                </span>
              </div>
            </div>
            <h3 class="text-white text-sm font-medium mt-2 line-clamp-1">{{ drama.title }}</h3>
            <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
              <ion-icon :icon="star" class="text-yellow-400 text-xs" />
              <span>{{ drama.rating || 'N/A' }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Category Sections -->
      <section v-for="catGroups in categorySections" :key="catGroups.name" class="px-4 pb-5">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-white text-lg font-bold">{{ getCategoryHeading(catGroups.name) }}</h2>
          <button class="text-purple-400 text-xs font-medium" @click="router.push('/browse')">See all</button>
        </div>
        <div class="horizontal-scroll flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
          <div
            v-for="drama in catGroups.dramas"
            :key="drama.id"
            class="flex-shrink-0 w-36"
            @click="navigateToDrama(drama.id)"
          >
            <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
              <img
                :src="drama.poster_url"
                :alt="drama.title"
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
                @error="onImgError"
              />
              <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <ion-icon :icon="play" class="text-white text-2xl" />
              </div>
            </div>
            <h3 class="text-white text-sm font-medium mt-2 line-clamp-1">{{ drama.title }}</h3>
            <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
              <ion-icon :icon="star" class="text-yellow-400 text-xs" />
              <span>{{ drama.rating || 'N/A' }}</span>
            </div>
          </div>
          <div v-if="catGroups.dramas.length === 0" class="flex-shrink-0 w-36 h-52 rounded-xl bg-white/5 flex items-center justify-center">
            <p class="text-gray-600 text-xs text-center px-2">Coming soon</p>
          </div>
        </div>
      </section>

      <!-- Bottom spacer -->
      <div class="h-20" />

      <!-- Loading State -->
      <div
        v-if="loading"
        class="fixed inset-0 flex items-center justify-center bg-black/80 z-50"
      >
        <ion-spinner color="primary" />
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from "vue";
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
const videoPlayerRef = ref(null);
const ionContent = ref(null);
const currentIndex = ref(0);
const loading = ref(false);
const touchStartY = ref(0);
const touchDelta = ref(0);
const isSwiping = ref(false);

const trendingDramas = computed(() => dramasStore.trending.slice(0, 5));
const recentDramas = computed(() => dramasStore.recent);
const featuredDramas = computed(() => dramasStore.featured);
const categories = computed(() => dramasStore.categories);

const isFav = (id) => dramasStore.isFavorite(id);

function hasVideoSrc(drama) {
  return !!(drama.video_url || drama.trailer_url);
}

// Category emoji map
const categoryEmoji = {
  'Romance': '💔',
  'Revenge': '🗡️',
  'Action': '💥',
  'Thriller': '🔪',
  'Comedy': '😂',
  'Drama': '🎭',
  'Horror': '👻',
  'Mystery': '🔍',
  'Fantasy': '🪄',
  'Sci-Fi': '🚀',
  'Crime': '🔫',
  'Adventure': '🗺️',
  'Animation': '🎬',
  'Documentary': '📽️',
  'Family': '👨‍👩‍👧‍👦',
  'History': '📜',
  'Music': '🎵',
  'Rom-Com': '💕',
  'Suspense': '😰',
  'War': '⚔️',
  'Western': '🤠',
  'Sports': '🏆',
  'Supernatural': '👹',
  'Teen': '🧑‍🎤',
  'Dystopian': '💀',
  'Psychological': '🧠',
  'Dark': '🌑',
};

function getCategoryHeading(name) {
  const emoji = categoryEmoji[name] || '🎬';
  return `${emoji} ${name}`;
}

// Group featured/recent dramas by category for sections
const categorySections = computed(() => {
  const sections = [];
  const featured = featuredDramas.value;
  const recent = recentDramas.value;
  const all = [...featured, ...recent];
  
  // Deduplicate by id
  const seen = new Set();
  const unique = all.filter(d => {
    if (seen.has(d.id)) return false;
    seen.add(d.id);
    return true;
  });

  // Build category groups
  const groups = {};
  unique.forEach(d => {
    const catName = d.category?.name || "Other";
    if (!groups[catName]) groups[catName] = [];
    if (groups[catName].length < 6) groups[catName].push(d);
  });

  // Sort: prefer categories with more items
  Object.keys(groups).forEach(name => {
    if (name !== "Other" && groups[name].length > 0) {
      sections.push({ name, dramas: groups[name] });
    }
  });

  return sections;
});

function onScroll(ev) {
  // no-op for now
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
  if (Math.abs(touchDelta.value) > 60) {
    if (
      touchDelta.value < 0 &&
      currentIndex.value < trendingDramas.value.length - 1
    ) {
      currentIndex.value++;
    } else if (touchDelta.value > 0 && currentIndex.value > 0) {
      currentIndex.value--;
    }
  }
  touchDelta.value = 0;
}

function togglePlay() {
  if (!videoPlayerRef.value) return;
  if (videoPlayerRef.value.paused) {
    videoPlayerRef.value.play();
  } else {
    videoPlayerRef.value.pause();
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

watch(currentIndex, async () => {
  await nextTick();
  if (videoPlayerRef.value) {
    videoPlayerRef.value.currentTime = 0;
    videoPlayerRef.value.play().catch(() => {});
  }
});

onMounted(async () => {
  loading.value = true;
  const promises = [
    dramasStore.fetchTrending(),
    dramasStore.fetchFeatured(),
    dramasStore.fetchRecent(),
    dramasStore.fetchCategories(),
  ];

  if (authStore.isAuthenticated) {
    promises.push(dramasStore.fetchFavorites());
  }

  await Promise.all(promises);
  loading.value = false;

  await nextTick();
  if (videoPlayerRef.value) {
    videoPlayerRef.value.play().catch(() => {});
  }
});
</script>

<style scoped>
.trending-section {
  height: 100vh;
  position: relative;
  overflow: hidden;
}

.section-header {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.video-feed {
  position: relative;
  height: 100%;
  width: 100%;
}

.video-item {
  position: absolute;
  inset: 0;
  height: 33.33%;
  width: 100%;
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
}

.video-item.peek {
  opacity: 0.25;
}

.video-item.peek2 {
  opacity: 0.1;
}

.video-item.active {
  opacity: 1;
  z-index: 2;
}

.video-placeholder {
  position: absolute;
  inset: 0;
  background: #1a1a2e;
}

.video-placeholder img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.video-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.85) 0%,
    rgba(0, 0, 0, 0.4) 40%,
    rgba(0, 0, 0, 0.1) 70%,
    transparent 100%
  );
  pointer-events: none;
}

.video-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 20px 16px 40px;
  z-index: 10;
}

.horizontal-scroll {
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
