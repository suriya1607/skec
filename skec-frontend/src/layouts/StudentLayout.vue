<template>
  <div class="min-h-screen bg-gray-50">
    <StudentHeader />
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import StudentHeader from '../components/layout/StudentHeader.vue'
</script>
