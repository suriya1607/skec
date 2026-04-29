<template>
  <div
    class="min-h-[100dvh] bg-gradient-to-br from-primary-900 via-primary-700 to-primary-500 flex items-center justify-center p-4 relative overflow-hidden"
  >
    <!-- Background pattern -->
    <div
      class="absolute inset-0 opacity-10"
      style="background-image: url('data:image/svg+xml,%3Csvg width=%2760%27 height=%2760%27 viewBox=%270 0 60 60%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cg fill=%27none%27 fill-rule=%27evenodd%27%3E%3Cg fill=%27%23ffffff%27 fill-opacity=%271%27%3E%3Cpath d=%27M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%27/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"
    />
    <!-- Glow blobs -->
    <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2" />
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-primary-300/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2" />

    <div class="w-full max-w-md relative z-10">
      <!-- Logo / Brand -->
      <div class="text-center mb-6 sm:mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-2xl shadow-xl mb-3 sm:mb-4 overflow-hidden">
          <img
            v-if="settingsStore.get('app_logo')"
            :src="settingsStore.get('app_logo')"
            :alt="settingsStore.get('app_name', 'SKEC')"
            class="w-full h-full object-contain p-1.5"
          />
          <span v-else class="text-primary-700 font-extrabold text-xl sm:text-2xl">SK</span>
        </div>
        <h1 class="text-white text-xl sm:text-2xl font-bold tracking-tight">
          {{ settingsStore.get('app_name', 'Sri Kumaran Education Centre') }}
        </h1>
        <p class="text-primary-200 text-xs sm:text-sm mt-1">
          {{ settingsStore.get('app_tagline', 'Your Digital Learning Hub') }}
        </p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8">
        <RouterView />
      </div>

      <p class="text-center text-primary-200 text-xs mt-5">
        © {{ new Date().getFullYear() }} Sri Kumaran Education Centre. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { useSettingsStore } from '../stores/settings'
const settingsStore = useSettingsStore()
</script>