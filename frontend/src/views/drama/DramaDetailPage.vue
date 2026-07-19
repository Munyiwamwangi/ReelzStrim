<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-buttons slot="start">
          <ion-back-button default-href="/browse" :icon="arrowBack" />
        </ion-buttons>
        <ion-title>
          <span class="gradient-text text-lg font-bold">{{
            drama?.title || "Loading..."
          }}</span>
        </ion-title>
        <ion-buttons slot="end">
          <ion-button @click="toggleFav">
            <ion-icon
              :icon="isFav ? heart : heartOutline"
              :class="isFav ? 'text-pink-500' : 'text-white'"
            />
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <div v-if="loading" class="p-4 space-y-4">
        <div class="w-full h-56 rounded-2xl skeleton" />
        <div class="h-6 w-3/4 rounded skeleton" />
        <div class="h-4 w-1/2 rounded skeleton" />
        <div class="h-20 w-full rounded skeleton" />
      </div>

      <div v-else-if="drama" class="pb-8">
        <!-- Hero Section -->
        <div class="relative h-56">
          <img
            :src="drama.banner_url || drama.poster_url"
            :alt="drama.title"
            class="absolute inset-0 w-full h-full object-cover"
            @error="onImgError"
          />
          <div
            class="absolute inset-0 bg-gradient-to-t from-[#0f0f23] via-transparent to-transparent"
          />
          <div class="absolute bottom-4 left-4 right-4">
            <div class="flex items-end gap-4">
              <div
                class="w-24 h-36 rounded-xl overflow-hidden flex-shrink-0 shadow-lg bg-gray-800 relative group cursor-pointer"
                @click="startWatching"
              >
                <img
                  :src="drama.poster_url"
                  :alt="drama.title"
                  class="w-full h-full object-cover"
                  @error="onImgError"
                />
                <!-- Play overlay on poster -->
                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <ion-icon :icon="play" class="text-white text-4xl" />
                </div>
              </div>
              <div class="flex-1">
                <h1 class="gradient-text text-xl font-bold">{{ drama.title }}</h1>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-yellow-400 text-sm"
                    >★ {{ drama.rating }}</span
                  >
                  <span class="text-gray-400 text-sm">{{ drama.year }}</span>
                  <span class="text-gray-400 text-sm"
                    >{{ drama.episode_count }} eps</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="px-4 space-y-6 mt-4">
          <!-- Action Buttons -->
          <div class="flex gap-3">
            <ion-button expand="block" class="flex-1" @click="startWatching">
              <ion-icon :icon="play" class="mr-1" />
              Start Watching
            </ion-button>
            <ion-button fill="outline" class="flex-1" @click="toggleFav">
              <ion-icon :icon="isFav ? heart : heartOutline" class="mr-1" />
              {{ isFav ? "Favorited" : "Favorite" }}
            </ion-button>
          </div>

          <!-- Description -->
          <section>
            <h3 class="text-white font-semibold mb-2">Synopsis</h3>
            <p class="text-gray-400 text-sm leading-relaxed">
              {{ drama.description }}
            </p>
          </section>

          <!-- Info Grid -->
          <section class="grid grid-cols-2 gap-3">
            <div class="glass rounded-xl p-3">
              <p class="text-gray-500 text-xs">Category</p>
              <p class="text-white text-sm font-medium mt-1">
                {{ drama.category?.name || "Drama" }}
              </p>
            </div>
            <div class="glass rounded-xl p-3">
              <p class="text-gray-500 text-xs">Duration</p>
              <p class="text-white text-sm font-medium mt-1">
                {{ drama.duration || "Varies" }}
              </p>
            </div>
            <div class="glass rounded-xl p-3">
              <p class="text-gray-500 text-xs">Episodes</p>
              <p class="text-white text-sm font-medium mt-1">
                {{ drama.episode_count || 0 }}
              </p>
            </div>
            <div class="glass rounded-xl p-3">
              <p class="text-gray-500 text-xs">Year</p>
              <p class="text-white text-sm font-medium mt-1">
                {{ drama.year || "N/A" }}
              </p>
            </div>
          </section>

          <!-- Cast -->
          <section v-if="drama.cast?.length">
            <h3 class="text-white font-semibold mb-3">Cast</h3>
            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
              <div
                v-for="actor in drama.cast"
                :key="actor.id"
                class="flex-shrink-0 text-center"
              >
                <div class="w-16 h-16 rounded-full skeleton mx-auto" />
                <p class="text-white text-xs mt-2">{{ actor.name }}</p>
                <p class="text-gray-500 text-xs">{{ actor.role }}</p>
              </div>
            </div>
          </section>

          <!-- Episodes List -->
          <section>
            <h3 class="text-white font-semibold mb-3">Episodes</h3>
            <div class="space-y-2">
              <div
                v-for="(ep, index) in episodes"
                :key="ep.id"
                class="glass rounded-xl p-3 flex items-center gap-3 cursor-pointer hover:bg-purple-500/10 transition-colors active:bg-purple-500/20"
                @click="watchEpisode(ep.id)"
              >
                <div
                  class="w-16 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-gray-800 relative group"
                >
                  <img
                    :src="ep.thumbnail_url || drama.poster_url"
                    :alt="ep.title"
                    class="w-full h-full object-cover"
                    @error="onImgError"
                  />
                  <!-- Play overlay on episode thumbnail -->
                  <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <ion-icon :icon="play" class="text-white text-lg" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-white text-sm font-medium">
                    Episode {{ index + 1 }}
                  </p>
                  <p class="text-gray-500 text-xs line-clamp-1">
                    {{ ep.title }}
                  </p>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                  <span>{{ ep.duration || "N/A" }}</span>
                  <ion-icon :icon="playCircle" class="text-purple-400" />
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
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
} from "@ionic/vue";
import {
  arrowBack,
  heart,
  heartOutline,
  play,
  playCircle,
} from "ionicons/icons";
import { useDramasStore } from "@/stores/dramas";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const router = useRouter();
const dramasStore = useDramasStore();
const authStore = useAuthStore();

const loading = ref(true);
const drama = ref(null);
const episodes = ref([]);

const isFav = computed(() => dramasStore.isFavorite(drama.value?.id));

function onImgError(e) {
  e.target.style.display = "none";
}

function watchEpisode(episodeId) {
  if (!authStore.isAuthenticated) {
    router.push("/auth");
    return;
  }
  router.push(`/drama/${route.params.id}/watch/${episodeId}`);
}

function startWatching() {
  if (!authStore.isAuthenticated) {
    router.push("/auth");
    return;
  }
  if (episodes.value.length > 0) {
    router.push(`/drama/${route.params.id}/watch/${episodes.value[0].id}`);
  }
}

function toggleFav() {
  if (!authStore.isAuthenticated) {
    // Redirect to auth if not logged in
    router.push("/auth");
    return;
  }
  if (drama.value) {
    dramasStore.toggleFavorite(drama.value.id);
  }
}

onMounted(async () => {
  const id = route.params.id;
  drama.value = await dramasStore.fetchDrama(id);
  if (drama.value) {
    await dramasStore.fetchEpisodes(id);
    episodes.value = dramasStore.episodes;
  }
  loading.value = false;
});
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