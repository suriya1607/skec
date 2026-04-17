<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center text-white text-center p-8">
    <div>
      <p class="text-8xl font-black text-yellow-400 mb-4">500</p>
      <h1 class="text-2xl font-bold mb-2">Server Error</h1>
      <p class="text-white/50 text-sm mb-8">Something went wrong. Please try again later.</p>
      <button @click="$router.back()" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
        Go Back
      </button>
    </div>
  </div>
</template>
