<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <StudentHeader />
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import StudentHeader from '../components/layout/StudentHeader.vue'
</script>