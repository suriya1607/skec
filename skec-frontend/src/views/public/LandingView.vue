<template>
  <div class="min-h-screen bg-white" style="font-family: 'Outfit', 'Inter', sans-serif;">

    <!-- ── Sticky Navbar ───────────────────────────────────────── -->
    <nav class="fixed top-0 inset-x-0 z-50 bg-white shadow-md border-b border-gray-100 transition-all duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 sm:h-24 flex items-center justify-between gap-4">
        <!-- Logo / Brand (No institute name text in header, logo clear & bigger) -->
        <RouterLink to="/" class="flex items-center gap-3 flex-shrink-0">
          <img
            v-if="settings.app_logo"
            :src="settings.app_logo"
            :alt="settings.app_name || 'SKEC'"
            class="h-12 sm:h-16 w-auto max-h-16 object-contain"
          />
          <div
            v-else
            class="w-12 h-12 sm:w-14 sm:h-14 bg-primary-900 rounded-xl flex items-center justify-center shadow-sm"
          >
            <span class="text-white font-black text-lg sm:text-xl">SK</span>
          </div>
        </RouterLink>

        <!-- Desktop nav links (spanning entire space between logo and student login, extra BIG) -->
        <div class="hidden md:flex items-center justify-evenly flex-1 mx-3 lg:mx-6">
          <a
            v-for="link in navLinks"
            :key="link.label"
            :href="link.href"
            class="px-3.5 py-2.5 text-lg lg:text-xl font-black text-blue-950 hover:text-primary-700 hover:bg-primary-50/60 rounded-xl transition-colors"
          >{{ link.label }}</a>
          <RouterLink
            to="/free-notes"
            class="px-4.5 py-2.5 text-lg lg:text-xl font-black bg-green-600 border-2 border-green-700 text-white hover:bg-green-700 rounded-xl transition-all shadow-md shadow-green-600/20 active:scale-95"
          >Free Notes</RouterLink>
          <RouterLink
            to="/contact"
            class="px-3.5 py-2.5 text-lg lg:text-xl font-black text-blue-950 hover:text-primary-700 hover:bg-primary-50/60 rounded-xl transition-colors"
          >Contact</RouterLink>
          <RouterLink
            to="/register"
            class="px-3.5 py-2.5 text-lg lg:text-xl font-black text-blue-950 hover:text-primary-700 hover:bg-primary-50/60 rounded-xl transition-colors"
          >Register</RouterLink>
        </div>

        <!-- CTA + mobile menu -->
        <div class="flex items-center gap-2">
          <!-- Student Login ONLY is Red (BIGGER size) -->
          <RouterLink
            to="/login"
            class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl text-base sm:text-lg lg:text-xl font-black bg-red-600 text-white hover:bg-red-700 transition shadow-md shadow-red-500/25 hover:shadow-lg hover:shadow-red-500/35 transform hover:-translate-y-0.5 active:translate-y-0"
          >
            Student Login
          </RouterLink>
          <button
            class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition"
            @click="mobileNavOpen = !mobileNavOpen"
          >
            <XMarkIcon v-if="mobileNavOpen" class="w-7 h-7 text-gray-800" />
            <Bars3Icon v-else              class="w-7 h-7 text-gray-800" />
          </button>
        </div>
      </div>

      <!-- Mobile nav -->
      <Transition name="menu">
        <div v-if="mobileNavOpen" class="md:hidden bg-white border-t border-gray-100 px-4 py-4 space-y-2 shadow-lg">
          <a
            v-for="link in navLinks"
            :key="link.label"
            :href="link.href"
            @click="mobileNavOpen = false"
            class="block px-4 py-3 rounded-xl text-lg font-black text-blue-950 hover:bg-primary-50 hover:text-primary-700"
          >{{ link.label }}</a>
          <RouterLink
            to="/free-notes"
            @click="mobileNavOpen = false"
            class="block px-4 py-3 rounded-xl text-lg font-black bg-green-600 border border-green-700 text-white text-center shadow-sm"
          >Free Notes</RouterLink>
          <RouterLink
            to="/contact"
            @click="mobileNavOpen = false"
            class="block px-4 py-3 rounded-xl text-lg font-black text-blue-950 hover:bg-primary-50 hover:text-primary-700"
          >Contact</RouterLink>
          <RouterLink
            to="/register"
            @click="mobileNavOpen = false"
            class="block px-4 py-3 rounded-xl text-lg font-black text-blue-950 hover:bg-primary-50 hover:text-primary-700"
          >Register</RouterLink>
          <div class="pt-2 border-t border-gray-100">
            <RouterLink
              to="/login"
              @click="mobileNavOpen = false"
              class="block w-full text-center px-5 py-3 rounded-xl text-lg font-black bg-red-600 text-white hover:bg-red-700 transition shadow-md shadow-red-500/25"
            >
              Student Login
            </RouterLink>
          </div>
        </div>
      </Transition>
    </nav>


    <!-- ── HERO (Banner OR Default) ───────────────────────── -->
    <section class="relative overflow-hidden bg-gray-950 pt-16 sm:pt-20">

      <!-- If banner image exists: image drives its own height (natural aspect ratio, zero black bars, zero crop) -->
      <template v-if="settings.hero_image">
        <!-- Invisible img sets the section height (renders after pt-16/pt-20 padding) -->
        <img
          :src="settings.hero_image"
          class="w-full block"
          alt=""
          aria-hidden="true"
        />
        <!-- Visible image starts exactly at bottom of navbar (top-16 sm:top-20), zero gap -->
        <div class="absolute inset-x-0 bottom-0 top-16 sm:top-20">
          <img
            :src="settings.hero_image"
            class="w-full h-full object-cover"
            alt="Banner"
          />
          <div class="absolute inset-0 bg-black/45" />
        </div>
      </template>

      <!-- Else use DEFAULT design -->
      <template v-else>
        <div class="absolute inset-0 bg-gradient-to-br from-primary-950 via-primary-800 to-primary-600">
          <!-- Background pattern -->
          <div class="absolute inset-0 opacity-10" aria-hidden="true">
            <svg width="100%" height="100%">
              <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
              </pattern>
              <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
          </div>
          <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl" />
          <div class="absolute bottom-1/4 -right-20 w-80 h-80 bg-primary-300/20 rounded-full blur-3xl" />
        </div>

        <!-- Default fallback height when no banner image -->
        <div class="min-h-[500px] lg:min-h-[650px]" />
      </template>

      <!-- COMMON CONTENT — aligned to top of hero section -->
      <div class="absolute inset-0 z-10 flex items-start pt-20 sm:pt-24 lg:pt-28">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 text-white">
          <div class="max-w-3xl">

            <!-- Institute Name -->
            <h1 class="text-3xl sm:text-4xl lg:text-4xl font-black text-yellow-400 tracking-wide uppercase mb-6 leading-tight [text-shadow:_0_4px_12px_rgba(239,68,68,0.95),_0_-4px_12px_rgba(59,130,246,0.85),_4px_0_12px_rgba(34,197,94,0.75)]">
              {{ settings.app_name || 'Sri Kumaran Education Centre' }}
            </h1>

            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-xs sm:text-sm font-medium mb-5 shadow-sm">
              <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse" />
              {{ settings.hero_badge || 'Admissions Open — Batch 2026' }}
            </div>

            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight mb-5 tracking-tight drop-shadow-md">
              {{ settings.hero_title || 'Shaping Futures Through' }}
              <span class="block text-primary-200">
                {{ settings.hero_subtitle || 'Excellence in Education' }}
              </span>
            </h2>

            <p class="text-base sm:text-lg text-white/90 mb-8 max-w-xl leading-relaxed drop-shadow-sm font-medium">
              {{ settings.hero_description || 'Sri Kumaran Education Centre — nurturing academic excellence, character, and lifelong learning for every student.' }}
            </p>

            <div class="flex flex-wrap gap-4">
              <RouterLink
                to="/login"
                class="px-6 py-3.5 rounded-xl bg-white text-primary-900 font-bold text-sm hover:bg-primary-50 transition shadow-lg hover:scale-105 active:scale-95"
              >
                Access Learning Portal →
              </RouterLink>
              <a
                href="#about"
                class="px-6 py-3.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/30 text-white font-semibold text-sm hover:bg-white/20 transition shadow-md"
              >
                Learn More
              </a>
            </div>

          </div>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/40 animate-bounce z-10" @click="scrollToNext">
        <ChevronDownIcon class="w-6 h-6" />
      </div>

    </section>

    <!-- ── Stats Bar ─────────────────────────────────────────────── -->
    <section id="ranking" class="bg-white border-b border-gray-100 scroll-mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
          <div v-for="stat in statItems" :key="stat.label" class="text-center">
            <div class="text-3xl sm:text-4xl font-extrabold text-primary-900 mb-1">{{ stat.value }}</div>
            <div class="text-xs sm:text-sm text-gray-500 font-medium">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Rank Holder Carousel ─────────────────────────────────────── -->
    <section
      v-if="sliderImages.length"
      class="rank-carousel-section"
      @mouseenter="pauseSlider"
      @mouseleave="resumeSlider"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="text-center mb-10 sm:mb-14">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">Our Toppers</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
            {{ settings.rank_title || 'Top Rank Holders' }}
          </h2>
          <p class="text-gray-500 max-w-lg mx-auto text-sm sm:text-base">
            Celebrating the brilliant minds who brought pride to our institution
          </p>
        </div>

        <!-- Carousel Container -->
        <div class="relative group/carousel">

          <!-- Left Arrow -->
          <button
            v-if="sliderImages.length > slidesPerView"
            @click="slidePrev"
            class="rank-carousel-arrow rank-carousel-arrow--left"
            aria-label="Previous"
          >
            <ChevronLeftIcon class="w-5 h-5" />
          </button>

          <!-- Right Arrow -->
          <button
            v-if="sliderImages.length > slidesPerView"
            @click="slideNext"
            class="rank-carousel-arrow rank-carousel-arrow--right"
            aria-label="Next"
          >
            <ChevronRightIcon class="w-5 h-5" />
          </button>

          <!-- Cards Track -->
          <div class="overflow-hidden rounded-2xl" ref="carouselTrack">
            <div
              class="flex transition-transform duration-700 ease-out"
              :style="{ transform: `translateX(-${sliderOffset}%)` }"
            >
              <div
                v-for="(student, i) in sliderImages"
                :key="i"
                class="rank-card-wrapper"
                :style="{ flex: `0 0 ${100 / slidesPerView}%` }"
              >
                <div
                  class="rank-card"
                  :class="{ 'rank-card--active': i === currentSlide }"
                  @click="goToSlide(i)"
                >
                  <!-- Image Area -->
                  <div class="rank-card__image-wrap">
                    <img :src="student.url" :alt="student.caption || 'Topper'" class="rank-card__image" />
                    <div class="rank-card__image-overlay"></div>

                    <!-- Rank Badge -->
                    <div class="rank-card__badge" :class="getRankBadgeClass(student)">
                      <span class="rank-card__badge-icon">{{ getRankEmoji(student) }}</span>
                      <span>{{ getRankLabel(student) }}</span>
                    </div>

                    <!-- Score Pill -->
                    <div v-if="getScore(student)" class="rank-card__score">
                      {{ getScore(student) }}
                    </div>
                  </div>

                  <!-- Content -->
                  <div class="rank-card__body">
                    <h3 class="rank-card__name">{{ student.caption || 'Student' }}</h3>
                    <p v-if="getBatch(student)" class="rank-card__batch">{{ getBatch(student) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Dot Navigation -->
        <div v-if="sliderImages.length > slidesPerView" class="flex justify-center gap-2 mt-8">
          <button
            v-for="(_, i) in totalDots"
            :key="i"
            @click="goToSlide(i)"
            class="rank-dot"
            :class="{ 'rank-dot--active': i === currentSlide }"
            :aria-label="`Go to slide ${i + 1}`"
          />
        </div>

      </div>
    </section>

    <!-- ── About ─────────────────────────────────────────────────── -->
    <section id="about" class="py-16 sm:py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
          <div>
            <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">About Us</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-5 leading-tight">
              {{ settings.about_title || 'A Legacy of Academic Excellence' }}
            </h2>
            <p class="text-gray-600 leading-relaxed mb-6 text-base sm:text-lg">
              {{ settings.about_description || 'Sri Kumaran Education Centre has been a beacon of quality education.' }}
            </p>
            <div class="space-y-3">
              <div v-for="point in aboutPoints" :key="point" class="flex items-start gap-3">
                <CheckCircleIcon class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" />
                <span class="text-gray-700 text-sm sm:text-base">{{ point }}</span>
              </div>
            </div>
          </div>

          <!-- About image or feature cards -->
          <div>
            <img
              v-if="settings.about_image"
              :src="settings.about_image"
              alt="About SKEC"
              class="w-full rounded-2xl shadow-lg object-cover max-h-80 sm:max-h-96"
            />
            <div v-else class="grid grid-cols-2 gap-4">
              <div
                v-for="card in aboutCards"
                :key="card.title"
                class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow"
              >
                <div :class="['w-10 h-10 rounded-xl flex items-center justify-center mb-4', card.bg]">
                  <component :is="card.icon" :class="['w-5 h-5', card.color]" />
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">{{ card.title }}</h3>
                <p class="text-gray-500 text-xs leading-relaxed">{{ card.desc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Current Batch ─────────────────────────────────────────── -->
    <section id="batch" class="py-16 sm:py-24 bg-white scroll-mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-12">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">Enrollment</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
            {{ settings.batch_title || 'Current Batch 2025' }}
          </h2>
          <p class="text-gray-500 max-w-xl mx-auto text-sm sm:text-base">
            {{ settings.batch_description || 'Join our growing community of learners.' }}
          </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 mb-8 sm:mb-10">
          <div
            v-for="batch in batchItems"
            :key="batch.name"
            class="relative overflow-hidden rounded-2xl border p-5 sm:p-6 hover:shadow-lg transition-all duration-200"
            :class="batch.featured
              ? 'bg-gradient-to-br from-primary-700 to-primary-900 text-white border-primary-700 shadow-md'
              : 'bg-white border-gray-100'"
          >
            <div v-if="batch.featured" class="absolute top-4 right-4">
              <span class="bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full">Popular</span>
            </div>
            <AcademicCapIcon
              class="w-8 h-8 mb-4"
              :class="batch.featured ? 'text-primary-200' : 'text-primary-600'"
            />
            <h3 class="font-bold text-lg mb-1" :class="batch.featured ? 'text-white' : 'text-gray-900'">
              {{ batch.name }}
            </h3>
            <p class="text-sm mb-4 leading-relaxed" :class="batch.featured ? 'text-white/75' : 'text-gray-500'">
              {{ batch.desc }}
            </p>
            <div class="text-xs font-semibold" :class="batch.featured ? 'text-primary-200' : 'text-primary-700'">
              {{ batch.seats }} students enrolled
            </div>
          </div>
        </div>

        <!-- Openings panel -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 sm:p-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
            <div>
              <h3 class="font-bold text-amber-900 text-base sm:text-lg mb-1">
                {{ settings.openings_title || 'Current Openings & Enrollment' }}
              </h3>
              <p class="text-amber-700 text-sm">
                {{ settings.openings_description || 'Secure your seat for the upcoming academic year.' }}
              </p>
            </div>
            <RouterLink
              to="/contact"
              class="flex-shrink-0 px-5 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-bold hover:bg-amber-700 transition shadow-sm inline-block"
            >
              Enquire Now →
            </RouterLink>
          </div>
          <div class="grid sm:grid-cols-3 gap-3 sm:gap-4">
            <div
              v-for="opening in openings"
              :key="opening.title"
              class="bg-white rounded-xl p-4 border border-amber-100"
            >
              <div class="font-semibold text-gray-900 text-sm mb-0.5">{{ opening.title }}</div>
              <div class="text-xs text-gray-500">{{ opening.detail }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Achievements ───────────────────────────────────────────── -->
    <section id="achievements" class="py-20 sm:py-28 bg-slate-50 border-y border-slate-200/60">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">Our Track Record</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-3">
            {{ settings.achievements_title || 'Milestones & Achievements' }}
          </h2>
          <p class="text-gray-600 max-w-xl mx-auto text-sm sm:text-base">
            {{ settings.achievements_description || 'Decades of excellence reflected in our students.' }}
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
          <div
            v-for="(ach, i) in achievements"
            :key="i"
            class="relative overflow-hidden rounded-2xl p-6 sm:p-7 border transition-all duration-300 hover:shadow-xl hover:-translate-y-1 group flex flex-col justify-between"
            :class="getAchievementStyle(i).card"
          >
            <!-- Decorative glow effect -->
            <div class="absolute -right-6 -top-6 w-28 h-28 rounded-full opacity-40 blur-2xl transition-opacity group-hover:opacity-70" :class="getAchievementStyle(i).glow"></div>

            <div>
              <div class="flex items-center justify-between mb-4">
                <span class="text-3xl sm:text-4xl font-black tracking-tight" :class="getAchievementStyle(i).metric">
                  {{ ach.metric }}
                </span>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shadow-sm" :class="getAchievementStyle(i).iconWrap">
                  <component :is="getAchievementIcon(i)" class="w-6 h-6" />
                </div>
              </div>
              <h3 class="font-bold text-lg sm:text-xl mb-2" :class="getAchievementStyle(i).title">
                {{ ach.title }}
              </h3>
              <p class="text-sm leading-relaxed" :class="getAchievementStyle(i).desc">
                {{ ach.description }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Gallery ─────────────────────────────────────────────────── -->
    <section id="gallery" v-if="galleryEnabled && galleryImages.length" class="py-16 sm:py-24 bg-gray-50 scroll-mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">Gallery</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
            {{ settings.gallery_title || 'Our Campus & Events' }}
          </h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
          <div
            v-for="(img, i) in galleryImages"
            :key="i"
            class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col cursor-pointer group hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
            @click="openLightbox(i)"
          >
            <div class="relative aspect-square bg-gray-100 overflow-hidden">
              <img
                :src="img.url"
                :alt="img.caption || `Photo ${i + 1}`"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
            </div>
            <div v-if="img.caption" class="p-3.5 sm:p-4 text-center bg-white flex-grow flex items-center justify-center border-t border-gray-100">
              <p class="font-bold text-gray-900 text-xs sm:text-sm leading-snug line-clamp-2 group-hover:text-primary-600 transition-colors">
                {{ img.caption }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Gallery lightbox -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="lightboxIndex !== null"
          class="fixed inset-0 z-[100] bg-black/90 flex flex-col items-center justify-center p-4"
          @click.self="lightboxIndex = null"
        >
          <button
            @click="lightboxIndex = null"
            class="absolute top-4 right-4 text-white/70 hover:text-white p-2 z-10"
          >
            <XMarkIcon class="w-6 h-6" />
          </button>
          <button
            v-if="lightboxIndex > 0"
            @click="lightboxIndex--"
            class="absolute left-4 text-white/70 hover:text-white p-2 z-10"
          >
            <ChevronLeftIcon class="w-7 h-7" />
          </button>
          <div class="max-w-4xl max-h-[85vh] flex flex-col items-center justify-center gap-3">
            <img
              v-if="galleryImages[lightboxIndex]"
              :src="galleryImages[lightboxIndex].url"
              class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl"
            />
            <div
              v-if="galleryImages[lightboxIndex]?.caption"
              class="text-white text-center font-bold text-sm sm:text-base bg-black/60 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/10 max-w-xl"
            >
              {{ galleryImages[lightboxIndex].caption }}
            </div>
          </div>
          <button
            v-if="lightboxIndex < galleryImages.length - 1"
            @click="lightboxIndex++"
            class="absolute right-4 text-white/70 hover:text-white p-2 z-10"
          >
            <ChevronRightIcon class="w-7 h-7" />
          </button>
        </div>
      </Transition>
    </Teleport>

    <!-- ── Testimonials ───────────────────────────────────────────── -->
    <section class="py-16 sm:py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 sm:mb-12">
          <span class="text-xs font-bold uppercase tracking-widest text-primary-600 mb-3 block">What Students Say</span>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
            {{ settings.testimonials_title || 'Voices from Our Community' }}
          </h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
          <div
            v-for="(t, i) in testimonials"
            :key="i"
            class="bg-gray-50 rounded-2xl p-5 sm:p-6 border border-gray-100 hover:shadow-md transition-shadow"
          >
            <div class="flex gap-1 mb-3">
              <StarIcon
                v-for="s in 5"
                :key="s"
                class="w-4 h-4"
                :class="s <= (t.rating || 5) ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200'"
              />
            </div>
            <p class="text-gray-700 text-sm leading-relaxed mb-5 italic">"{{ t.quote || t.review }}"</p>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                <span class="text-primary-700 font-bold text-xs">{{ t.name?.charAt(0) }}</span>
              </div>
              <div>
                <div class="font-semibold text-gray-900 text-sm">{{ t.name }}</div>
                <div class="text-gray-400 text-xs">{{ t.batch }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── CTA ──────────────────────────────────────────────────── -->
    <section class="py-14 sm:py-20 bg-gray-50 border-t border-gray-100">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
          {{ settings.cta_title || 'Ready to Begin Your Journey?' }}
        </h2>
        <p class="text-gray-500 text-sm sm:text-base mb-8">
          {{ settings.cta_description || 'Join hundreds of students achieving their academic goals.' }}
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
          <RouterLink
            to="/contact"
            class="px-6 py-3.5 rounded-xl bg-white text-primary-900 font-bold text-sm hover:bg-primary-50 transition shadow-lg"
          >
            Contact Us →
          </RouterLink>
          <RouterLink
            to="/login"
            class="px-6 py-3.5 border-2 border-primary-700 text-primary-700 rounded-xl font-bold text-sm hover:bg-primary-50 transition"
          >
            {{ settings.cta_secondary_label || 'Student Login' }}
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- ── Footer ────────────────────────────────────────────────── -->
    <footer class="bg-primary-700 text-white py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8 pb-8 border-b border-white/10">
          <!-- Brand -->
          <div>
            <div class="flex items-center gap-3 mb-2">
              <img v-if="settings.app_logo" :src="settings.app_logo" class="h-8 w-auto opacity-80" alt="Logo" />
              <div v-else class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xs">SK</span>
              </div>
            </div>
            <p class="font-bold text-sm text-white">
              {{ settings.app_name || 'Sri Kumaran Education Centre' }}
            </p>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="font-semibold text-sm mb-3">Contact</h4>
            <ul class="space-y-2 text-xs">
              <li>
                <a :href="'mailto:' + (settings.contact_email || 'admin@srikumaran.in')"
                   class="text-white/60 hover:text-white/80 transition">
                  {{ settings.contact_email || 'admin@srikumaran.in' }}
                </a>
              </li>
              <li v-if="settings.contact_phone" class="text-white/60">
                {{ settings.contact_phone }}
              </li>
            </ul>
          </div>

          <!-- Address -->
          <div>
            <h4 class="font-semibold text-sm mb-3">Address</h4>
            <p v-if="settings.address" class="text-white/60 text-xs leading-relaxed">
              {{ settings.address }}
            </p>
            <p v-else class="text-white/60 text-xs leading-relaxed">
              Near Church, Ariyankuppam<br />Puducherry - 605007
            </p>
          </div>

          <!-- Legal -->
          <div>
            <h4 class="font-semibold text-sm mb-3">Legal</h4>
            <ul class="space-y-2 text-xs">
              <li>
                <RouterLink to="/privacy-policy" class="text-white/60 hover:text-white/80 transition">
                  Privacy Policy
                </RouterLink>
              </li>
              <li>
                <RouterLink to="/terms-and-conditions" class="text-white/60 hover:text-white/80 transition">
                  Terms & Conditions
                </RouterLink>
              </li>
            </ul>
          </div>
        </div>

        <!-- Bottom section -->
        <div class="text-center">
          <p class="text-white/40 text-xs">
            © {{ new Date().getFullYear() }} {{ settings.app_name || 'Sri Kumaran Education Centre' }}. All rights reserved.
          </p>
        </div>
      </div>
    </footer>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { settingsApi } from '../../api/settings'
import { reviewsApi } from '../../api/student/reviews'
import {
  ChevronDownIcon, ChevronLeftIcon, ChevronRightIcon,
  CheckCircleIcon, AcademicCapIcon, StarIcon,
  Bars3Icon, XMarkIcon,
  BookOpenIcon, TrophyIcon, UsersIcon, ComputerDesktopIcon
} from '@heroicons/vue/24/outline'
import * as HeroIcons from '@heroicons/vue/24/outline'

// ── Scroll + nav state ─────────────────────────────────────────────────
const scrolled      = ref(false)
const mobileNavOpen = ref(false)
const settings      = ref({})

const navLinks = [
  { label: 'About',        href: '#about' },
  { label: 'Batch',        href: '#batch' },
  { label: 'Achievements', href: '#achievements' },
  { label: 'Gallery',      href: '#gallery' },
]

function onScroll() { scrolled.value = window.scrollY > 40 }

// ── Rank Holder Carousel ───────────────────────────────────────────────
const currentSlide   = ref(0)
const slidesPerView  = ref(5)
const carouselTrack  = ref(null)
let sliderTimer      = null
let sliderPaused     = false

const sliderImages = computed(() => {
  const raw = settings.value.slider_images
  if (!raw) return []
  try { return JSON.parse(raw) } catch { return [] }
})

const totalDots = computed(() => sliderImages.value.length)

const sliderOffset = computed(() => {
  const total = sliderImages.value.length
  if (total <= slidesPerView.value) return 0
  const perSlide = 100 / slidesPerView.value
  const maxOffset = (total - slidesPerView.value) * perSlide
  return Math.min(currentSlide.value * perSlide, maxOffset)
})

function slideNext() {
  const max = sliderImages.value.length - 1
  currentSlide.value = currentSlide.value >= max ? 0 : currentSlide.value + 1
}
function slidePrev() {
  const max = sliderImages.value.length - 1
  currentSlide.value = currentSlide.value <= 0 ? max : currentSlide.value - 1
}
function goToSlide(i) { currentSlide.value = i }

function updateSlidesPerView() {
  const w = window.innerWidth
  slidesPerView.value = w < 640 ? 1 : w < 1024 ? 2 : 4
}

function startSliderTimer() {
  stopSliderTimer()
  const autoplay  = settings.value.slider_autoplay
  const interval  = parseInt(settings.value.slider_interval) || 4000
  const shouldPlay = autoplay === true || autoplay === 'true' || autoplay === undefined
  if (shouldPlay && sliderImages.value.length > slidesPerView.value) {
    sliderTimer = setInterval(() => { if (!sliderPaused) slideNext() }, interval)
  }
}
function stopSliderTimer() {
  if (sliderTimer) { clearInterval(sliderTimer); sliderTimer = null }
}
function pauseSlider()  { sliderPaused = true }
function resumeSlider() { sliderPaused = false }

// ── Rank helpers ───────────────────────────────────────────────────────
function getRank(student) {
  const match = (student.subcaption || '').match(/rank\s*(\d+)/i)
  return match ? parseInt(match[1]) : null
}
function getRankEmoji(student) {
  const r = getRank(student)
  if (r === 1) return '🥇'
  if (r === 2) return '🥈'
  if (r === 3) return '🥉'
  return '🏆'
}
function getRankLabel(student) {
  const r = getRank(student)
  if (r === 1) return '1st Rank'
  if (r === 2) return '2nd Rank'
  if (r === 3) return '3rd Rank'
  return 'Topper'
}
function getRankBadgeClass(student) {
  const r = getRank(student)
  if (r === 1) return 'rank-badge--gold'
  if (r === 2) return 'rank-badge--silver'
  if (r === 3) return 'rank-badge--bronze'
  return 'rank-badge--default'
}
function getScore(student) {
  return (student.subcaption || '').match(/\d+\/\d+/)?.[0] || ''
}
function getBatch(student) {
  return (student.subcaption || '')
    .replace(/rank\s*\d+/gi, '')
    .replace(/\d+\/\d+/g, '')
    .trim()
}

// ── Gallery lightbox ───────────────────────────────────────────────────
const lightboxIndex = ref(null)
function openLightbox(i) { lightboxIndex.value = i }

// ── Computed content from settings ────────────────────────────────────
const statItems = computed(() => [
  { value: settings.value.stat_students  || '500+', label: 'Students Enrolled' },
  { value: settings.value.stat_years     || '10+',  label: 'Years of Excellence' },
  { value: settings.value.stat_pass_rate || '98%',  label: 'Pass Rate' },
  { value: settings.value.stat_rank_1    || '50+',  label: 'District Rank 1s' },
])

const aboutPoints = computed(() => {
  const raw = settings.value.about_points
  if (raw) return raw.split('|').map(s => s.trim()).filter(Boolean)
  return [
    'Expert faculty with years of teaching experience',
    'Personalised attention for every student',
    'Digital learning materials and resources',
    'Regular assessments and performance tracking',
  ]
})

const aboutCards = computed(() => {
  const raw = settings.value.about_cards

  if (raw) {
    try {
      const cards = JSON.parse(raw)

      return cards.map(card => ({
        ...card,
        icon: HeroIcons[card.icon] || HeroIcons.UsersIcon
      }))
    } catch (e) {
      console.error(e)
    }
  }
  return [  
    { title: 'Expert Faculty',   desc: 'Experienced educators committed to success',     icon: UsersIcon,           bg: 'bg-blue-50',   color: 'text-blue-600'   },
    { title: 'Smart Learning',   desc: 'Digital notes accessible anytime, anywhere',     icon: ComputerDesktopIcon, bg: 'bg-purple-50', color: 'text-purple-600' },
    { title: 'Top Results',      desc: 'Consistently producing district-level toppers', icon: TrophyIcon,           bg: 'bg-amber-50',  color: 'text-amber-600'  },
    { title: 'Study Materials',  desc: 'Comprehensive notes for all subjects',           icon: BookOpenIcon,         bg: 'bg-green-50',  color: 'text-green-600'  },
  ]
})
const batchItems = computed(() => {
  const raw = settings.value.batch_items
  if (raw) { try { return JSON.parse(raw) } catch {} }
  return [
    { name: 'Class 6–8',   desc: 'Foundation years — building core concepts',        seats: '40', featured: false },
    { name: 'Class 9–10',  desc: 'Board exam preparation with concept mastery',     seats: '60', featured: true  },
    { name: 'Class 11–12', desc: 'Higher secondary science & commerce coaching',    seats: '35', featured: false },
  ]
})

const openings = computed(() => {
  const raw = settings.value.openings_items
  if (raw) { try { return JSON.parse(raw) } catch {} }
  return [
    { title: 'New Admissions',   detail: 'June – July 2025 enrollment open' },
    { title: 'Scholarship Test', detail: 'Merit-based discounts available' },
    { title: 'Trial Classes',    detail: 'Free demo session for new students' },
  ]
})

const achievements = computed(() => {
  const raw = settings.value.achievements_items
  if (raw) { try { return JSON.parse(raw) } catch {} }
  return [
    { metric: '98%',  title: 'Board Exam Pass Rate',  description: 'Achieving above 85% in board exams.' },
    { metric: '50+',  title: 'District Rank Holders', description: 'Over 50 toppers produced.' },
    { metric: '500+', title: 'Students Mentored',     description: 'Graduates in successful careers.' },
    { metric: '10+',  title: 'Years of Service',      description: 'A decade of academic excellence.' },
    { metric: '95%',  title: 'Parent Satisfaction',   description: 'Parents trust us with their children.' },
    { metric: '15+',  title: 'Subject Specialists',   description: 'Dedicated experts in every subject.' },
  ]
})

const achievementStyles = [
  {
    card: 'bg-gradient-to-br from-blue-100/90 via-blue-50 to-sky-100/70 border-blue-200 shadow-sm hover:border-blue-300',
    metric: 'text-blue-700',
    title: 'text-blue-950',
    desc: 'text-blue-800/90',
    iconWrap: 'bg-blue-600 text-white',
    glow: 'bg-blue-400',
  },
  {
    card: 'bg-gradient-to-br from-amber-100/90 via-amber-50 to-orange-100/70 border-amber-200 shadow-sm hover:border-amber-300',
    metric: 'text-amber-700',
    title: 'text-amber-950',
    desc: 'text-amber-800/90',
    iconWrap: 'bg-amber-600 text-white',
    glow: 'bg-amber-400',
  },
  {
    card: 'bg-gradient-to-br from-emerald-100/90 via-emerald-50 to-teal-100/70 border-emerald-200 shadow-sm hover:border-emerald-300',
    metric: 'text-emerald-700',
    title: 'text-emerald-950',
    desc: 'text-emerald-800/90',
    iconWrap: 'bg-emerald-600 text-white',
    glow: 'bg-emerald-400',
  },
  {
    card: 'bg-gradient-to-br from-purple-100/90 via-purple-50 to-violet-100/70 border-purple-200 shadow-sm hover:border-purple-300',
    metric: 'text-purple-700',
    title: 'text-purple-950',
    desc: 'text-purple-800/90',
    iconWrap: 'bg-purple-600 text-white',
    glow: 'bg-purple-400',
  },
  {
    card: 'bg-gradient-to-br from-rose-100/90 via-rose-50 to-pink-100/70 border-rose-200 shadow-sm hover:border-rose-300',
    metric: 'text-rose-700',
    title: 'text-rose-950',
    desc: 'text-rose-800/90',
    iconWrap: 'bg-rose-600 text-white',
    glow: 'bg-rose-400',
  },
  {
    card: 'bg-gradient-to-br from-cyan-100/90 via-cyan-50 to-teal-100/70 border-cyan-200 shadow-sm hover:border-cyan-300',
    metric: 'text-cyan-700',
    title: 'text-cyan-950',
    desc: 'text-cyan-800/90',
    iconWrap: 'bg-cyan-600 text-white',
    glow: 'bg-cyan-400',
  },
]

function getAchievementStyle(index) {
  return achievementStyles[index % achievementStyles.length]
}

const achievementIcons = [
  TrophyIcon,
  AcademicCapIcon,
  UsersIcon,
  StarIcon,
  CheckCircleIcon,
  BookOpenIcon,
]

function getAchievementIcon(index) {
  return achievementIcons[index % achievementIcons.length]
}

const galleryEnabled = computed(() => {
  const v = settings.value.gallery_enabled
  return v === true || v === 'true'
})

const galleryImages = computed(() => {
  const raw = settings.value.gallery_images
  if (raw) { try { return JSON.parse(raw) } catch {} }
  return []
})

const liveReviews = ref([])

const testimonials = computed(() => {
  // If live approved reviews exist, use them
  if (liveReviews.value.length) {
    return liveReviews.value.map(r => ({
      rating: r.rating,
      quote:  r.review,
      name:   r.name,
      batch:  r.batch,
    }))
  }
  // Fallback: settings-based manual testimonials
  const raw = settings.value.testimonials_items
  if (raw) { try { return JSON.parse(raw) } catch {} }
  return [
    { rating: 5, quote: 'SKEC completely transformed how I approach studies. I went from average to district topper!', name: 'Priya S.', batch: 'Class 10, Batch 2024' },
    { rating: 5, quote: 'The teachers here genuinely care about every student. The digital notes are incredibly helpful.', name: 'Arjun M.', batch: 'Class 12, Batch 2023' },
    { rating: 5, quote: 'Best coaching centre in the district. My son scored 98% in boards thanks to SKEC.', name: 'Meena R.', batch: 'Parent' },
  ]
})

function scrollToNext() {
  const el = document.getElementById('ranking')
  if (el) el.scrollIntoView({ behavior: 'smooth' })
}

// ── Lifecycle ──────────────────────────────────────────────────────────
onMounted(async () => {
  window.addEventListener('scroll', onScroll)
  window.addEventListener('resize', updateSlidesPerView)
  updateSlidesPerView()
  try {
    const res = await settingsApi.getPublic()
    settings.value = res.data.data || {}
  } catch {}
  // Load live approved reviews
  try {
    const revRes = await reviewsApi.getPublic()
    liveReviews.value = revRes.data.data || []
  } catch {}
  startSliderTimer()
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', updateSlidesPerView)
  stopSliderTimer()
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');

/* ── Rank Carousel Section ──────────────────────────────────── */
.rank-carousel-section {
  padding: 5rem 0;
  background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
  overflow: hidden;
}

/* ── Navigation Arrows ──────────────────────────────────────── */
.rank-carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 20;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  color: #1e293b;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.25s ease;
  opacity: 0;
}
.group\/carousel:hover .rank-carousel-arrow { opacity: 1; }
.rank-carousel-arrow:hover {
  background: #fff;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  transform: translateY(-50%) scale(1.08);
}
.rank-carousel-arrow--left  { left: -8px; }
.rank-carousel-arrow--right { right: -8px; }
@media (max-width: 768px) {
  .rank-carousel-arrow { opacity: 1; width: 36px; height: 36px; }
  .rank-carousel-arrow--left  { left: 4px; }
  .rank-carousel-arrow--right { right: 4px; }
}

/* ── Card Wrapper & Card ────────────────────────────────────── */
.rank-card-wrapper { padding: 8px; }

.rank-card {
  background: #fff;
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.06);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  cursor: pointer;
}
.rank-card:hover, .rank-card--active {
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
  border-color: rgba(99, 102, 241, 0.15);
}

/* ── Card Image ─────────────────────────────────────────────── */
.rank-card__image-wrap {
  position: relative;
  overflow: hidden;
  aspect-ratio: 4 / 3;
}
.rank-card__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}
.rank-card:hover .rank-card__image { transform: scale(1.05); }
.rank-card__image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 50%, rgba(0, 0, 0, 0.35) 100%);
  pointer-events: none;
}

/* ── Rank Badge ─────────────────────────────────────────────── */
.rank-card__badge {
  position: absolute;
  top: 12px;
  left: 12px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 12px;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: 0.02em;
  backdrop-filter: blur(4px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}
.rank-card__badge-icon { font-size: 0.85rem; }
.rank-badge--gold   { background: linear-gradient(135deg, #f59e0b, #d97706); }
.rank-badge--silver { background: linear-gradient(135deg, #94a3b8, #64748b); }
.rank-badge--bronze { background: linear-gradient(135deg, #f97316, #ea580c); }
.rank-badge--default { background: linear-gradient(135deg, #6366f1, #4f46e5); }

/* ── Score Pill ──────────────────────────────────────────────── */
.rank-card__score {
  position: absolute;
  bottom: 12px;
  right: 12px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(4px);
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 800;
  color: #1e293b;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
}

/* ── Card Body ──────────────────────────────────────────────── */
.rank-card__body { padding: 1rem 1.25rem 1.25rem; text-align: center; }
.rank-card__name {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 0.25rem;
}
.rank-card__batch {
  font-size: 0.8rem;
  font-weight: 700;
  color: #475569;
}

/* ── Dot Navigation ─────────────────────────────────────────── */
.rank-dot {
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #cbd5e1;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}
.rank-dot--active {
  width: 28px;
  background: linear-gradient(90deg, #6366f1, #4f46e5);
}
.rank-dot:hover:not(.rank-dot--active) { background: #94a3b8; }

/* ── Existing transitions ───────────────────────────────────── */
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.4s ease; }
.slide-up-enter-from  { opacity: 0; transform: translateY(10px); }
.slide-up-leave-to    { opacity: 0; transform: translateY(-10px); }

.menu-enter-active, .menu-leave-active { transition: all 0.2s ease; }
.menu-enter-from, .menu-leave-to { opacity: 0; transform: translateY(-8px); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>