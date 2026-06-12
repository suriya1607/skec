<template>
  <div class="flex flex-col h-screen bg-gray-50" style="font-family: 'Outfit', 'Inter', sans-serif;">

    <!-- ── Compact Header ─────────────────────────────────────── -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top bar -->
        <div class="h-14 flex items-center justify-between gap-4">
          <RouterLink to="/" class="flex items-center gap-1.5 text-primary-700 hover:text-primary-900 transition text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
          </RouterLink>

          <!-- Title + total count -->
          <div class="flex items-center gap-3 flex-1 justify-center">
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                FREE
              </span>
              <h1 class="font-bold text-gray-900 text-base hidden sm:block">Free Study Notes</h1>
            </div>
            <span v-if="meta" class="text-xs text-gray-400 hidden sm:inline">
              {{ meta.total }} note{{ meta.total !== 1 ? 's' : '' }}
            </span>
          </div>

          <!-- Login CTA -->
          <RouterLink
            to="/login"
            class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold bg-primary-700 text-white hover:bg-primary-800 transition shadow-sm"
          >
            Student Login
          </RouterLink>
        </div>

        <!-- Search bar inside header -->
        <div class="pb-3">
          <div class="bg-gray-50 border border-gray-200 rounded-xl flex items-center gap-2 px-3 py-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="searchInput"
              type="text"
              placeholder="Search notes…"
              class="flex-1 text-sm text-gray-700 placeholder-gray-400 border-none outline-none bg-transparent"
              @input="debouncedSearch"
            />
            <button v-if="searchInput" @click="clearSearch" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- ── Notes Grid ──────────────────────────────────────── -->
    <main class="flex-1 overflow-y-auto max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-12">

      <!-- Loading skeleton -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="i in 12" :key="i" class="bg-white rounded-2xl border border-gray-100 p-5 animate-pulse">
          <div class="h-3 bg-gray-100 rounded w-1/3 mb-4" />
          <div class="h-5 bg-gray-200 rounded w-3/4 mb-2" />
          <div class="h-3 bg-gray-100 rounded w-full mb-1" />
          <div class="h-3 bg-gray-100 rounded w-5/6 mb-6" />
          <div class="h-9 bg-gray-100 rounded-xl w-full" />
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!notes.length" class="text-center py-24">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
          </svg>
        </div>
        <p class="text-gray-500 font-medium">
          {{ searchInput ? 'No notes match your search.' : 'No free notes published yet.' }}
        </p>
        <p class="text-gray-400 text-sm mt-1">Check back later for new study materials.</p>
      </div>

      <!-- Notes cards -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="note in notes"
          :key="note.id"
          class="group bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg hover:border-primary-100 transition-all duration-200 flex flex-col"
        >
          <!-- Category badges -->
          <div class="flex items-center justify-between mb-3">
            <div class="flex flex-wrap gap-1">
              <span
                v-for="cat in note.categories"
                :key="cat.id"
                class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full"
                :style="{ background: (cat.color || '#6b7280') + '18', color: cat.color || '#6b7280' }"
              >
                <span class="w-1.5 h-1.5 rounded-full" :style="{ background: cat.color || '#6b7280' }" />
                {{ cat.name }}
              </span>
            </div>
            <span v-if="note.subject" class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">
              {{ note.subject.name }}
            </span>
          </div>

          <!-- Title & description -->
          <h3 class="font-bold text-gray-900 mb-1.5 leading-snug group-hover:text-primary-700 transition-colors line-clamp-2">
            {{ note.title }}
          </h3>
          <p v-if="note.description" class="text-sm text-gray-500 mb-4 line-clamp-2 leading-relaxed flex-1">
            {{ note.description }}
          </p>
          <div v-else class="flex-1" />

          <!-- Meta -->
          <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
            <span v-if="note.total_pages" class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              {{ note.total_pages }} pages
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
              </svg>
              {{ note.file_size_formatted }}
            </span>
          </div>

          <!-- Open button -->
          <button
            @click="openNote(note)"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary-700 text-white text-sm font-semibold hover:bg-primary-800 active:scale-95 transition-all duration-150 shadow-sm"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
            </svg>
            Open in Browser
          </button>
        </div>
      </div>

      <!-- ── Pagination ─────────────────────────────────────── -->
      <div v-if="meta && meta.last_page > 1" class="mt-10 flex items-center justify-center gap-1.5">
        <!-- Prev -->
        <button
          :disabled="meta.current_page === 1"
          class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :class="meta.current_page === 1 ? 'text-gray-400' : 'text-gray-700 hover:bg-gray-100'"
          @click="goToPage(meta.current_page - 1)"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Prev
        </button>

        <!-- Page numbers -->
        <template v-for="page in visiblePages" :key="page">
          <span v-if="page === '...'" class="px-2 py-2 text-sm text-gray-400 select-none">…</span>
          <button
            v-else
            class="min-w-[36px] h-9 rounded-lg text-sm font-semibold transition-colors"
            :class="page === meta.current_page
              ? 'bg-primary-700 text-white shadow-sm'
              : 'text-gray-700 hover:bg-gray-100'"
            @click="goToPage(page)"
          >
            {{ page }}
          </button>
        </template>

        <!-- Next -->
        <button
          :disabled="meta.current_page === meta.last_page"
          class="flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
          :class="meta.current_page === meta.last_page ? 'text-gray-400' : 'text-gray-700 hover:bg-gray-100'"
          @click="goToPage(meta.current_page + 1)"
        >
          Next
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- Page info -->
      <p v-if="meta && meta.last_page > 1" class="text-center text-xs text-gray-400 mt-3">
        Page {{ meta.current_page }} of {{ meta.last_page }} · {{ meta.total }} total notes
      </p>
    </main>

    <!-- ── CTA footer strip ────────────────────────────────────── -->
    <div class="bg-primary-700 text-white py-3">
      <div class="max-w-3xl mx-auto px-4 flex items-center justify-between">
        <!-- <p class="font-bold text-lg mb-1">Want access to all study materials?</p> -->
        <span class="text-sm">Join SKEC and get full access to our complete library of notes and resources.</span>
        <RouterLink
          to="/contact"
          class="inline-block px-6 py-3 rounded-xl bg-white text-primary-900 font-bold text-sm hover:bg-primary-50 transition shadow-lg"
        >
          Enquire Now →
        </RouterLink>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { freeNotesApi } from '../../api/freeNotes'

const loading     = ref(true)
const notes       = ref([])
const meta        = ref(null)
const searchInput = ref('')
let   debounceTimer = null

// ── Data fetching ─────────────────────────────────────────────────────────────
async function fetchNotes(page = 1) {
  loading.value = true
  try {
    const res = await freeNotesApi.list({
      page,
      per_page: 12,
      search: searchInput.value || undefined,
    })
    notes.value = res.data.data || []
    meta.value  = res.data.meta || null
  } catch {
    notes.value = []
    meta.value  = null
  } finally {
    loading.value = false
  }
}

function goToPage(page) {
  if (!page || page < 1 || page > meta.value?.last_page) return
  window.scrollTo({ top: 0, behavior: 'smooth' })
  fetchNotes(page)
}

// ── Search ────────────────────────────────────────────────────────────────────
function debouncedSearch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchNotes(1), 350)
}

function clearSearch() {
  searchInput.value = ''
  fetchNotes(1)
}

// ── Pagination page numbers (with ellipsis) ───────────────────────────────────
const visiblePages = computed(() => {
  if (!meta.value) return []
  const { current_page: cur, last_page: last } = meta.value
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)

  const pages = []
  if (cur <= 4) {
    pages.push(1, 2, 3, 4, 5, '...', last)
  } else if (cur >= last - 3) {
    pages.push(1, '...', last - 4, last - 3, last - 2, last - 1, last)
  } else {
    pages.push(1, '...', cur - 1, cur, cur + 1, '...', last)
  }
  return pages
})

// ── Note viewer ───────────────────────────────────────────────────────────────
function openNote(note) {
  window.open(freeNotesApi.streamUrl(note.id), '_blank')
}

onMounted(() => fetchNotes(1))
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');
</style>
