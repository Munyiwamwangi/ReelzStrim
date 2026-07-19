<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar>
        <ion-title class="text-center">
          <span class="gradient-text text-xl font-bold">DramaWave</span>
        </ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content>
      <div class="p-4 space-y-6">
        <!-- Categories -->
        <section>
          <h2 class="text-white text-lg font-semibold mb-3">Categories</h2>
          <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <button
              v-for="cat in categories"
              :key="cat.id"
              @click="selectedCategory = cat.id"
              :class="[
                'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all',
                selectedCategory === cat.id
                  ? 'gradient-bg text-white'
                  : 'glass text-gray-300 hover:text-white'
              ]"
            >
              {{ cat.name }}
            </button>
          </div>
        </section>

        <!-- Featured Drama -->
        <section v-if="featuredDrama">
          <div class="relative rounded-2xl overflow-hidden h-48">
            <img
              :src="featuredDrama.banner_url || featuredDrama.poster_url"
              :alt="featuredDrama.title"
              class="absolute inset-0 w-full h-full object-cover"
              @error="onImgError"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
            <div class="absolute bottom-0 left-0 right-0 p-4">
              <h3 class="text-white text-xl font-bold">{{ featuredDrama.title }}</h3>
              <p class="text-gray-300 text-sm mt-1 line-clamp-1">{{ featuredDrama.description }}</p>
              <ion-button size="small" class="mt-2 gradient-bg" @click="navigateToFeed">
                <ion-icon :icon="play" class="mr-1" />
                Watch Now
              </ion-button>
            </div>
          </div>
        </section>

        <!-- Trending Now -->
        <section>
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-white text-lg font-semibold">Trending Now</h2>
            <span class="text-purple-400 text-sm">See all</span>
          </div>
          <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
            <div
              v-for="drama in trending"
              :key="drama.id"
              class="flex-shrink-0 w-36"
            >
              <router-link :to="`/drama/${drama.id}`">
                <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                  <img
                    :src="drama.poster_url"
                    :alt="drama.title"
                    class="absolute inset-0 w-full h-full object-cover"
                    loading="lazy"
                    @error="onImgError"
                  />
                  <!-- Play overlay -->
                  <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <ion-icon :icon="play" class="text-white text-2xl" />
                  </div>
                  <div class="absolute top-2 left-2">
                    <span class="px-2 py-0.5 rounded-full text-xs bg-purple-500/80 text-white">
                      #{{ trending.indexOf(drama) + 1 }}
                    </span>
                  </div>
                </div>
                <h3 class="text-white text-sm font-medium mt-2 line-clamp-1">{{ drama.title }}</h3>
                <p class="text-gray-500 text-xs">{{ drama.episode_count || 0 }} eps</p>
              </router-link>
            </div>
          </div>
        </section>

        <!-- Recent Updates -->
        <section>
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-white text-lg font-semibold">Recent Updates</h2>
            <span class="text-purple-400 text-sm">See all</span>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div
              v-for="drama in recent"
              :key="drama.id"
              class="drama-card"
            >
              <router-link :to="`/drama/${drama.id}`" class="block">
                <div class="aspect-[2/3] rounded-xl overflow-hidden relative bg-gray-800 group cursor-pointer">
                  <img
                    :src="drama.poster_url"
                    :alt="drama.title"
                    class="absolute inset-0 w-full h-full object-cover"
                    loading="lazy"
                    @error="onImgError"
                  />
                  <!-- Play overlay -->
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
              </router-link>
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
} from '@ionic/vue'
import { play, star } from 'ionicons/icons'
import { useDramasStore } from '@/stores/dramas'

const router = useRouter()
const dramasStore = useDramasStore()
const selectedCategory = ref(null)

const categories = computed(() => dramasStore.categories)
const trending = computed(() => dramasStore.trending)
const recent = computed(() => dramasStore.recent)
const featuredDrama = computed(() => dramasStore.featured[0] || null)

function onImgError(e) {
  e.target.style.display = 'none'
}

function navigateToFeed() {
  router.push('/feed')
}

onMounted(async () => {
  await Promise.all([
    dramasStore.fetchCategories(),
    dramasStore.fetchTrending(),
    dramasStore.fetchRecent(),
    dramasStore.fetchFeatured(),
  ])
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