<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader 
      @toggle-sidebar="toggleSidebar" 
      @search="handleSearch"
    />

    <div class="flex">
      <!-- Overlay for mobile -->
      <div 
        v-if="!sidebarCollapsed" 
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
        @click="sidebarCollapsed = true"
      ></div>
      
      <!-- Sidebar -->
      <AppSidebar :isCollapsed="sidebarCollapsed" />

      <!-- Main Content -->
      <main 
        :class="['flex-1 transition-all duration-300', sidebarCollapsed ? 'ml-20' : 'ml-80']"
        :style="{ marginLeft: sidebarCollapsed ? '80px' : '320px' }"
      >
        <!-- Debug info -->
        <div v-if="false" class="p-2 bg-blue-100 text-xs">
          Sidebar collapsed: {{ sidebarCollapsed }}
        </div>
        <div class="p-6">
          <!-- Welcome message for authenticated users -->
          <div v-if="$page.props.auth.user" class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
              こんにちは、{{ $page.props.auth.user.name }}さん
            </h1>
            <p class="text-gray-600 mt-1">おすすめの動画をお楽しみください</p>
          </div>

          <!-- Search results header -->
          <div v-if="searchQuery" class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
              「{{ searchQuery }}」の検索結果
            </h2>
            <p class="text-gray-600 mt-1">{{ filteredVideos.length }}件の動画が見つかりました</p>
          </div>

          <!-- Category tabs -->
          <CategoryTabs @category-changed="handleCategoryChange" />

          <!-- Video Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <VideoCard
              v-for="video in filteredVideos"
              :key="video.id"
              :video="video"
            />
          </div>

          <!-- No videos message -->
          <div v-if="filteredVideos.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">動画が見つかりません</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ searchQuery ? '検索条件を変更してお試しください' : '選択されたカテゴリに動画がありません' }}
            </p>
          </div>

          <!-- Top News Section -->
          <div v-if="!searchQuery && selectedCategory === 'all' && newsVideos.length > 0" class="mt-12">
            <h2 class="text-xl font-semibold mb-6 text-gray-900">トップニュース</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <VideoCard
                v-for="news in newsVideos"
                :key="news.id"
                :video="news"
              />
            </div>
          </div>

          <!-- Guest user call-to-action -->
          <div v-if="!$page.props.auth.user" class="mt-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-white">
            <div class="max-w-2xl mx-auto text-center">
              <h3 class="text-2xl font-bold mb-4">YouTubeをもっと楽しもう</h3>
              <p class="text-lg mb-6 opacity-90">
                アカウントを作成して、お気に入りの動画を保存したり、コメントを投稿したりしましょう
              </p>
              <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <Link 
                  href="/register"
                  class="px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors"
                >
                  無料でアカウント作成
                </Link>
                <Link 
                  href="/login"
                  class="px-6 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-blue-600 transition-colors"
                >
                  ログイン
                </Link>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppHeader from '../Components/YouTube/AppHeader.vue'
import AppSidebar from '../Components/YouTube/AppSidebar.vue'
import CategoryTabs from '../Components/YouTube/CategoryTabs.vue'
import VideoCard from '../Components/YouTube/VideoCard.vue'

// Props from backend
const props = defineProps({
  videos: {
    type: Array,
    default: () => []
  },
  newsVideos: {
    type: Array,
    default: () => []
  }
})

const sidebarCollapsed = ref(false)
const selectedCategory = ref('all')
const searchQuery = ref('')

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const handleCategoryChange = (categoryId) => {
  selectedCategory.value = categoryId
  searchQuery.value = '' // Clear search when changing category
}

const handleSearch = (query) => {
  searchQuery.value = query
  selectedCategory.value = 'all' // Reset category when searching
}

const filteredVideos = computed(() => {
  let filtered = props.videos

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(video => 
      video.title.toLowerCase().includes(query) ||
      video.channelName.toLowerCase().includes(query)
    )
  }

  // Filter by category
  if (selectedCategory.value !== 'all' && !searchQuery.value) {
    filtered = filtered.filter(video => video.category === selectedCategory.value)
  }

  return filtered
})
</script>

<style scoped>
/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style> 