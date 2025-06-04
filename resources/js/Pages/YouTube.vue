<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader 
      @toggle-sidebar="toggleSidebar" 
      @search="handleSearch"
    />

    <div class="flex">
      <!-- Sidebar -->
      <AppSidebar :isCollapsed="sidebarCollapsed" />

      <!-- Main Content -->
      <main :class="['flex-1 transition-all duration-300', sidebarCollapsed ? 'ml-16' : 'ml-64']">
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
          <div v-if="!searchQuery && selectedCategory === 'all'" class="mt-12">
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

// Sample video data
const videos = ref([
  {
    id: 1,
    title: '【今夏移籍】レオ ゴメス（ジュビロ磐田→京都サンガF.C.）悲願のJ1制覇へ繋...',
    thumbnail: 'https://via.placeholder.com/320x180/FF0000/FFFFFF?text=DAZN+Video',
    duration: '6:17',
    channelName: 'DAZN Japan',
    channelInitial: 'D',
    channelColor: 'bg-blue-500',
    channelBadge: 'DAZN',
    specialBadge: '段',
    specialBadgeColor: 'bg-red-600',
    views: 17000,
    publishedAt: '2024-01-20T09:00:00Z',
    category: 'sports'
  },
  {
    id: 2,
    title: 'はじめてのアイス もちたろう',
    thumbnail: 'https://via.placeholder.com/320x180/FFB347/FFFFFF?text=Cute+Video',
    duration: '0:50',
    channelName: 'もちたろう',
    channelInitial: 'も',
    channelColor: 'bg-green-500',
    channelBadge: 'も',
    views: 620000,
    publishedAt: '2023-08-15T14:30:00Z',
    category: 'pets'
  },
  {
    id: 3,
    title: '自分のこだと思い込み母親のように接するゴールデンレトリバーが優しすぎました...',
    thumbnail: 'https://via.placeholder.com/320x180/87CEEB/FFFFFF?text=Golden+Retriever',
    duration: '9:10',
    channelName: '豆柴うに&ゴールデンレトリバーおから UNI&...',
    channelInitial: 'UNI',
    channelColor: 'bg-yellow-500',
    channelBadge: 'UNI',
    views: 2360000,
    publishedAt: '2023-05-10T16:45:00Z',
    category: 'pets'
  },
  {
    id: 4,
    title: 'サンプル動画のタイトルがここに表示されます',
    thumbnail: 'https://via.placeholder.com/320x180/FF6B6B/FFFFFF?text=Sample+Video',
    duration: '12:35',
    channelName: 'チャンネル名',
    channelInitial: 'チ',
    channelColor: 'bg-purple-500',
    views: 100000,
    publishedAt: '2024-01-18T12:00:00Z',
    category: 'entertainment'
  },
  {
    id: 5,
    title: '別のサンプル動画のタイトル',
    thumbnail: 'https://via.placeholder.com/320x180/4ECDC4/FFFFFF?text=Another+Video',
    duration: '8:22',
    channelName: '別のチャンネル',
    channelInitial: '別',
    channelColor: 'bg-pink-500',
    views: 50000,
    publishedAt: '2024-01-13T10:15:00Z',
    category: 'education'
  },
  {
    id: 6,
    title: '長いタイトルの動画サンプルです。これは非常に長いタイトルの例で、複数行にわたって表示される可能性があります。',
    thumbnail: 'https://via.placeholder.com/320x180/FFE66D/000000?text=Long+Title+Video',
    duration: '15:43',
    channelName: 'サンプルチャンネル',
    channelInitial: 'サ',
    channelColor: 'bg-indigo-500',
    views: 1000000,
    publishedAt: '2024-01-17T08:30:00Z',
    category: 'technology'
  }
])

const newsVideos = ref([
  {
    id: 101,
    title: '重要なニュースのタイトルがここに表示されます',
    thumbnail: 'https://via.placeholder.com/400x225/FF4757/FFFFFF?text=TBS+NEWS',
    duration: '3:45',
    channelName: 'TBS NEWS DIG',
    channelInitial: 'TBS',
    channelColor: 'bg-blue-600',
    channelBadge: 'TBS NEWS DIG',
    views: 500000,
    publishedAt: '2024-01-20T07:00:00Z',
    category: 'news'
  },
  {
    id: 102,
    title: 'ウクライナ【クリミア大橋】の一部を爆破 除去作業公開',
    thumbnail: 'https://via.placeholder.com/400x225/2ED573/FFFFFF?text=日テレNEWS',
    duration: '5:22',
    channelName: '日テレNEWS',
    channelInitial: '日',
    channelColor: 'bg-green-600',
    channelBadge: '日テレNEWS',
    views: 300000,
    publishedAt: '2024-01-20T05:30:00Z',
    category: 'news'
  },
  {
    id: 103,
    title: '最新の社会情勢に関するニュース',
    thumbnail: 'https://via.placeholder.com/400x225/FF6B9D/FFFFFF?text=めざましテレビ',
    duration: '4:18',
    channelName: 'めざましテレビ',
    channelInitial: 'め',
    channelColor: 'bg-red-600',
    channelBadge: 'めざましテレビ',
    views: 250000,
    publishedAt: '2024-01-20T04:00:00Z',
    category: 'news'
  }
])

const filteredVideos = computed(() => {
  let filtered = videos.value

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