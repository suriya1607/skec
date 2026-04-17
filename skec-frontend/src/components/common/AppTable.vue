<template>
  <div>
    <!-- Table wrapper -->
    <div class="overflow-x-auto rounded-xl border border-gray-100 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
          <tr>
            <th v-for="col in columns" :key="col.key" class="table-th whitespace-nowrap">
              {{ col.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="loading">
            <td :colspan="columns.length" class="py-12 text-center">
              <AppLoader />
            </td>
          </tr>
          <tr v-else-if="!rows.length">
            <td :colspan="columns.length" class="py-12">
              <AppEmptyState :title="emptyTitle" :description="emptyDesc" />
            </td>
          </tr>
          <tr v-else v-for="(row, i) in rows" :key="i" class="table-row">
            <td v-for="col in columns" :key="col.key" class="table-td">
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                {{ row[col.key] ?? '—' }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import AppLoader     from './AppLoader.vue'
import AppEmptyState from './AppEmptyState.vue'

defineProps({
  columns:    { type: Array, required: true },
  rows:       { type: Array, default: () => [] },
  loading:    { type: Boolean, default: false },
  emptyTitle: { type: String, default: 'No results' },
  emptyDesc:  { type: String, default: 'Nothing to show here.' },
})
</script>
