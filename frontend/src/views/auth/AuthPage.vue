<template>
  <ion-page>
    <ion-content class="auth-content">
      <div class="auth-container">
        <div class="auth-header">
          <div class="logo-container">
            <div class="logo-icon">
              <ion-icon :icon="filmOutline" />
            </div>
            <h1 class="gradient-text text-4xl font-bold">RStream</h1>
          </div>
          <p class="text-gray-400 mt-2">Your ultimate drama streaming experience</p>
        </div>

        <div class="auth-form glass">
          <div class="flex mb-6">
            <button
              :class="['flex-1 py-3 text-center font-semibold transition-all rounded-xl', isLogin ? 'gradient-bg text-white' : 'text-gray-400']"
              @click="isLogin = true"
            >
              Sign In
            </button>
            <button
              :class="['flex-1 py-3 text-center font-semibold transition-all rounded-xl', !isLogin ? 'gradient-bg text-white' : 'text-gray-400']"
              @click="isLogin = false"
            >
              Sign Up
            </button>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div v-if="!isLogin" class="space-y-2">
              <ion-label class="text-sm text-gray-400">Full Name</ion-label>
              <ion-input
                v-model="form.name"
                placeholder="John Doe"
                class="auth-input"
                fill="outline"
                required
              />
            </div>

            <div class="space-y-2">
              <ion-label class="text-sm text-gray-400">Email</ion-label>
              <ion-input
                v-model="form.email"
                type="email"
                placeholder="you@example.com"
                class="auth-input"
                fill="outline"
                required
              />
            </div>

            <div class="space-y-2">
              <ion-label class="text-sm text-gray-400">Password</ion-label>
              <ion-input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                class="auth-input"
                fill="outline"
                required
              />
            </div>

            <div v-if="!isLogin" class="space-y-2">
              <ion-label class="text-sm text-gray-400">Confirm Password</ion-label>
              <ion-input
                v-model="form.password_confirmation"
                type="password"
                placeholder="••••••••"
                class="auth-input"
                fill="outline"
                required
              />
            </div>

            <ion-text color="danger" class="text-sm" v-if="authStore.error">
              {{ authStore.error }}
            </ion-text>

            <ion-button
              expand="block"
              type="submit"
              class="mt-6 gradient-bg"
              :disabled="authStore.loading"
            >
              <ion-spinner v-if="authStore.loading" />
              <span v-else>{{ isLogin ? 'Sign In' : 'Create Account' }}</span>
            </ion-button>
          </form>
        </div>

        <div class="text-center mt-6">
          <p class="text-gray-500 text-sm">
            By continuing, you agree to our Terms of Service and Privacy Policy
          </p>
        </div>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import {
  IonPage,
  IonContent,
  IonInput,
  IonButton,
  IonLabel,
  IonText,
  IonSpinner,
  IonIcon,
} from '@ionic/vue'
import { filmOutline } from 'ionicons/icons'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const isLogin = ref(true)

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

async function handleSubmit() {
  let success
  if (isLogin.value) {
    success = await authStore.login({
      email: form.email,
      password: form.password,
    })
  } else {
    success = await authStore.register({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
    })
  }

  if (success) {
    router.replace('/feed')
  }
}
</script>

<style scoped>
.auth-content {
  --background: #0f0f23;
}

.auth-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 24px;
  max-width: 400px;
  margin: 0 auto;
}

.auth-header {
  text-align: center;
  margin-bottom: 40px;
}

.logo-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, #7c3aed, #ec4899);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  color: white;
  box-shadow: 0 8px 32px rgba(124, 58, 237, 0.3);
}

.auth-form {
  padding: 24px;
  border-radius: 20px;
}

.auth-input {
  --background: rgba(15, 15, 35, 0.6);
  --color: #ffffff;
  --placeholder-color: #6b7280;
  --border-color: rgba(124, 58, 237, 0.2);
  --border-radius: 12px;
  --padding-start: 16px;
  --padding-end: 16px;
  min-height: 48px;
}
</style>