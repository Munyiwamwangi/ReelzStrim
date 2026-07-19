import { createRouter, createWebHistory } from "@ionic/vue-router";
import { useAuthStore } from "@/stores/auth";

const routes = [
  {
    path: "/",
    redirect: "/feed",
  },
  {
    path: "/auth",
    name: "Auth",
    component: () => import("@/views/auth/AuthPage.vue"),
    meta: { public: true }, // Public route - no auth required
  },
  {
    path: "/feed",
    name: "Feed",
    component: () => import("@/views/feed/FeedPage.vue"),
    meta: { public: true },
  },
  {
    path: "/home",
    redirect: "/feed",
  },
  {
    path: "/browse",
    name: "Browse",
    component: () => import("@/views/browse/BrowsePage.vue"),
    meta: { public: true }, // Public route - no auth required
  },
  {
    path: "/drama/:id",
    name: "DramaDetail",
    component: () => import("@/views/drama/DramaDetailPage.vue"),
    meta: { public: true }, // Public route - no auth required
  },
  {
    path: "/drama/:id/watch/:episodeId",
    name: "WatchEpisode",
    component: () => import("@/views/drama/WatchEpisodePage.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/profile",
    name: "Profile",
    component: () => import("@/views/profile/ProfilePage.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/favorites",
    name: "Favorites",
    component: () => import("@/views/profile/FavoritesPage.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/history",
    name: "History",
    component: () => import("@/views/profile/HistoryPage.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/search",
    name: "Search",
    component: () => import("@/views/browse/SearchPage.vue"),
    meta: { public: true }, // Public route - no auth required
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next("/auth");
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next("/feed");
  } else {
    next();
  }
});

export default router;
