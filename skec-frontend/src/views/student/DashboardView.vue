<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">My Notes</h1>
      <p class="text-gray-500 text-sm mt-1">Browse and access your study materials</p>
    </div>

    <!-- Category filter tabs -->
    <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2 mb-4">
      <button
        v-for="cat in ['All', ...categories]"
        :key="typeof cat === 'string' ? cat : cat.id"
        @click="selectCategory(cat)"
        :class="[
          'flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-all border',
          isSelected(cat)
            ? 'bg-primary-700 text-white border-primary-700'
            : 'bg-white text-gray-600 border-gray-200 hover:border-primary-400',
        ]"
      >
        <span v-if="typeof cat === 'string'">All</span>
        <span v-else class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full" :style="{ background: cat.color }" />
          {{ cat.name }}
          <span class="text-xs opacity-70">({{ cat.notes_count ?? '' }})</span>
        </span>
      </button>
    </div>

    <!-- Subject filter tabs -->
    <div v-if="subjects.length" class="flex gap-2 overflow-x-auto scrollbar-hide pb-2 mb-6">
      <button
        v-for="sub in ['All', ...subjects]"
        :key="typeof sub === 'string' ? sub : sub.id"
        @click="selectSubject(sub)"
        :class="[
          'flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-medium transition-all border',
          isSubjectSelected(sub)
            ? 'bg-indigo-600 text-white border-indigo-600'
            : 'bg-white text-gray-500 border-gray-200 hover:border-indigo-400',
        ]"
      >
        <span v-if="typeof sub === 'string'">All Subjects</span>
        <span v-else class="flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full" :style="{ background: sub.color || '#6366f1' }" />
          {{ sub.name }}
        </span>
      </button>
    </div>

    <!-- Search -->
    <div class="mb-6">
      <AppInput v-model="search" placeholder="Search notes…" @input="debouncedFetch" />
    </div>

    <!-- Loading skeletons -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="i in 6" :key="i" class="card animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-3" />
        <div class="h-3 bg-gray-100 rounded w-full mb-2" />
        <div class="h-3 bg-gray-100 rounded w-5/6 mb-4" />
        <div class="h-8 bg-gray-100 rounded w-1/3" />
      </div>
    </div>

    <!-- Notes grid -->
    <div v-else-if="notes.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="note in notes"
        :key="note.id"
        class="card hover:shadow-md transition-shadow group cursor-pointer"
        @click="openNote(note)"
      >
        <!-- Category badge -->
        <div class="flex items-center justify-between mb-3">
          <span v-if="note.category" class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-1 rounded-full" :style="{ background: note.category.color + '20', color: note.category.color }">
            <span class="w-1.5 h-1.5 rounded-full" :style="{ background: note.category.color }" />
            {{ note.category.name }}
          </span>
          <span v-else class="text-xs text-gray-400">General</span>
          <DocumentTextIcon class="w-5 h-5 text-gray-300 group-hover:text-primary-400 transition-colors" />
        </div>

        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ note.title }}</h3>
        <p v-if="note.description" class="text-sm text-gray-500 mb-4 line-clamp-2">{{ note.description }}</p>

        <div class="flex items-center justify-between mt-auto">
          <div class="flex items-center gap-3 text-xs text-gray-400">
            <span v-if="note.total_pages">{{ note.total_pages }} pages</span>
            <span>{{ note.file_size_formatted }}</span>
          </div>
          <AppButton size="xs" variant="primary" @click.stop="openNote(note)">Open</AppButton>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <AppEmptyState
      v-else
      :icon="DocumentTextIcon"
      title="No notes available"
      description="No study materials have been published yet. Check back later."
    />

    <AppPagination :meta="meta" @change="fetchNotes" class="mt-6" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter }   from 'vue-router'
import { notesApi }    from '../../api/student/notes'
import { debounce }    from '../../utils/helpers'
import { DocumentTextIcon } from '@heroicons/vue/24/outline'
import AppInput      from '../../components/common/AppInput.vue'
import AppButton     from '../../components/common/AppButton.vue'
import AppEmptyState from '../../components/common/AppEmptyState.vue'
import AppPagination from '../../components/common/AppPagination.vue'

const router     = useRouter()
const loading    = ref(false)
const notes      = ref([])
const categories = ref([])
const subjects   = ref([])
const meta       = ref(null)
const search     = ref('')
const selectedCategoryId = ref(null)
const selectedSubjectId  = ref(null)

function selectCategory(cat) {
  selectedCategoryId.value = typeof cat === 'string' ? null : cat.id
  fetchNotes(1)
}

function isSelected(cat) {
  if (typeof cat === 'string') return selectedCategoryId.value === null
  return selectedCategoryId.value === cat.id
}

function selectSubject(sub) {
  selectedSubjectId.value = typeof sub === 'string' ? null : sub.id
  fetchNotes(1)
}

function isSubjectSelected(sub) {
  if (typeof sub === 'string') return selectedSubjectId.value === null
  return selectedSubjectId.value === sub.id
}

async function fetchNotes(p = 1) {
  loading.value = true
  try {
    const res = await notesApi.list({
      page: p,
      search: search.value,
      category_id: selectedCategoryId.value,
      subject_id: selectedSubjectId.value,
    })
    notes.value = res.data.data
    meta.value  = res.data.meta
  } finally { loading.value = false }
}

const debouncedFetch = debounce(() => fetchNotes(1), 300)

function openNote(note) {
  router.push(`/notes/${note.id}/view`)
}

onMounted(async () => {
  fetchNotes()
  const [catRes, subRes] = await Promise.all([
    notesApi.categories(),
    notesApi.subjects(),
  ])
  categories.value = catRes.data.data
  subjects.value   = subRes.data.data
})
</script>
