<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader @toggle-sidebar="toggleSidebar" />

    <div class="flex">
      <!-- Sidebar -->
      <AppSidebar :isCollapsed="sidebarCollapsed" />

      <!-- Main Content -->
      <main :class="['flex-1 transition-all duration-300', sidebarCollapsed ? 'ml-16' : 'ml-64']">
        <div class="p-6">
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

          <!-- Top News Section -->
          <div class="mt-12">
            <h2 class="text-xl font-semibold mb-6 text-gray-900">トップニュース</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <VideoCard
                v-for="news in newsVideos"
                :key="news.id"
                :video="news"
              />
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppHeader from '../Components/YouTube/AppHeader.vue'
import AppSidebar from '../Components/YouTube/AppSidebar.vue'
import CategoryTabs from '../Components/YouTube/CategoryTabs.vue'
import VideoCard from '../Components/YouTube/VideoCard.vue'

const sidebarCollapsed = ref(false)
const selectedCategory = ref('all')

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const handleCategoryChange = (categoryId) => {
  selectedCategory.value = categoryId
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
  if (selectedCategory.value === 'all') {
    return videos.value
  }
  return videos.value.filter(video => video.category === selectedCategory.value)
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