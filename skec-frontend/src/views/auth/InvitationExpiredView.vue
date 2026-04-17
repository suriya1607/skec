<template>
  <div class="text-center">
    <ExclamationCircleIcon class="w-12 h-12 text-red-400 mx-auto mb-4" />
    <h2 class="text-xl font-bold text-gray-900 mb-2">Invitation Expired or Invalid</h2>
    <p class="text-sm text-gray-500 mb-6">
      This invitation link is no longer valid.<br>
      Please contact your administrator for a new invitation.
    </p>
    <RouterLink to="/login" class="text-primary-600 text-sm font-medium hover:underline">
      Back to Login
    </RouterLink>
  </div>
</template>

<script setup>
import { ExclamationCircleIcon } from '@heroicons/vue/24/outline'
</script>
