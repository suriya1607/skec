<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-900 via-primary-700 to-primary-500 flex items-center justify-center p-4">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="w-full max-w-md relative z-10">
      <!-- Logo / Brand -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-xl mb-4">
          <span class="text-primary-700 font-extrabold text-2xl">SK</span>
        </div>
        <h1 class="text-white text-2xl font-bold tracking-tight">
          {{ settingsStore.get('app_name', 'Sri Kumaran Education Centre') }}
        </h1>
        <p class="text-primary-200 text-sm mt-1">
          {{ settingsStore.get('app_tagline', 'Your Digital Learning Hub') }}
        </p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <RouterView />
      </div>

      <p class="text-center text-primary-200 text-xs mt-6">
        © {{ new Date().getFullYear() }} Sri Kumaran Education Centre. All rights reserved.
      </p>
    </div>
  </div>
</template>

<script setup>
import { useSettingsStore } from '../stores/settings'
const settingsStore = useSettingsStore()
</script>
