<template>
  <nav class="flex items-center gap-1 text-sm" aria-label="Breadcrumb">
    <template v-for="(crumb, i) in crumbs" :key="crumb.path">
      <span v-if="i > 0" class="text-gray-300 mx-1">/</span>
      <RouterLink
        v-if="i < crumbs.length - 1"
        :to="crumb.path"
        class="text-gray-500 hover:text-primary-600 transition-colors"
      >{{ crumb.label }}</RouterLink>
      <span v-else class="text-gray-800 font-medium">{{ crumb.label }}</span>
    </template>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const crumbs = computed(() => {
  const segments = route.path.split('/').filter(Boolean)
  const result = []
  let path = ''
  for (const seg of segments) {
    path += '/' + seg
    result.push({
      label: seg.charAt(0).toUpperCase() + seg.slice(1).replace(/-/g, ' '),
      path,
    })
  }
  return result
})
</script>
