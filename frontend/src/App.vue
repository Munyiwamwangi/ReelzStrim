<template>
  <ion-app>
    <ion-router-outlet />
    <ion-tabs>
      <ion-tab-bar slot="bottom" class="glass" v-if="showTabs">
        <ion-tab-button tab="feed" href="/feed">
          <ion-icon :icon="home" />
          <ion-label>Feed</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="browse" href="/browse">
          <ion-icon :icon="compass" />
          <ion-label>Browse</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="search" href="/search">
          <ion-icon :icon="search" />
          <ion-label>Search</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="favorites" href="/favorites">
          <ion-icon :icon="heart" />
          <ion-label>Favorites</ion-label>
        </ion-tab-button>

        <ion-tab-button tab="profile" href="/profile">
          <ion-icon :icon="person" />
          <ion-label>Profile</ion-label>
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  </ion-app>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
  IonApp,
  IonRouterOutlet,
  IonTabs,
  IonTabBar,
  IonTabButton,
  IonIcon,
  IonLabel,
} from "@ionic/vue";
import { home, compass, search, heart, person } from "ionicons/icons";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const showTabs = computed(() => {
  const hiddenRoutes = ["Auth", "WatchEpisode", "DramaDetail"];
  return !hiddenRoutes.includes(route.name);
});

// Initialize auth on app mount
onMounted(async () => {
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser();
  }
});
</script>