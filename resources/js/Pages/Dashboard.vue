<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppHeader from '../Components/YouTube/AppHeader.vue'
import AppSidebar from '../Components/YouTube/AppSidebar.vue'

const sidebarCollapsed = ref(false)

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const handleSearch = (query) => {
  // Handle search functionality
  console.log('Search:', query)
}

// Sample user stats
const userStats = {
  totalViews: 15420,
  subscribers: 1250,
  videosUploaded: 23,
  totalWatchTime: 4850
}

// Sample user videos
const userVideos = ref([
  {
    id: 1,
    title: '私の最初のYouTube動画',
    thumbnail: 'https://via.placeholder.com/320x180/FF6B6B/FFFFFF?text=My+First+Video',
    duration: '5:30',
    views: 1200,
    uploadedAt: '2024-01-15T10:00:00Z',
    status: 'published'
  },
  {
    id: 2,
    title: '料理レシピ - 簡単パスタの作り方',
    thumbnail: 'https://via.placeholder.com/320x180/4ECDC4/FFFFFF?text=Cooking+Video',
    duration: '8:45',
    views: 3500,
    uploadedAt: '2024-01-10T14:30:00Z',
    status: 'published'
  },
  {
    id: 3,
    title: '下書き動画',
    thumbnail: 'https://via.placeholder.com/320x180/95A5A6/FFFFFF?text=Draft+Video',
    duration: '3:15',
    views: 0,
    uploadedAt: '2024-01-20T09:00:00Z',
    status: 'draft'
  }
])

const formatNumber = (num) => {
  if (num >= 10000) {
    return `${(num / 10000).toFixed(1)}万`
  }
  return num.toLocaleString()
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 24) {
    return `${diffInHours} 時間前`
  }
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 30) {
    return `${diffInDays} 日前`
  }
  
  const diffInMonths = Math.floor(diffInDays / 30)
  return `${diffInMonths} か月前`
}
</script>

<template>
  <Head title="ダッシュボード - YouTube" />

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
          <!-- Header section -->
          <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
              ダッシュボード
            </h1>
            <p class="text-gray-600">
              こんにちは、{{ $page.props.auth.user.name }}さん。あなたのチャンネルの概要です。
            </p>
          </div>

          <!-- Stats cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">総再生回数</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ formatNumber(userStats.totalViews) }}</dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">チャンネル登録者数</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ formatNumber(userStats.subscribers) }}</dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">投稿動画数</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ userStats.videosUploaded }}</dd>
                  </dl>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">総視聴時間</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ formatNumber(userStats.totalWatchTime) }}分</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick actions -->
          <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">クイックアクション</h2>
            <div class="flex flex-wrap gap-4">
              <button class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                新しい動画をアップロード
              </button>
              <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                ライブ配信を開始
              </button>
              <Link 
                href="/user/profile" 
                class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                プロフィール設定
              </Link>
            </div>
          </div>

          <!-- Recent videos -->
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-semibold text-gray-900">最近の動画</h2>
              <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">すべて表示</button>
            </div>

            <div class="space-y-4">
              <div 
                v-for="video in userVideos" 
                :key="video.id"
                class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
              >
                <div class="relative">
                  <img 
                    :src="video.thumbnail"
                    :alt="video.title"
                    class="w-24 h-14 object-cover rounded"
                  >
                  <div class="absolute bottom-1 right-1 bg-black bg-opacity-80 text-white text-xs px-1 rounded">
                    {{ video.duration }}
                  </div>
                </div>
                
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-medium text-gray-900 truncate">{{ video.title }}</h3>
                  <p class="text-xs text-gray-500 mt-1">
                    {{ formatNumber(video.views) }} 回視聴 • {{ formatDate(video.uploadedAt) }}
                  </p>
                  <div class="flex items-center mt-2">
                    <span 
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        video.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                      ]"
                    >
                      {{ video.status === 'published' ? '公開中' : '下書き' }}
                    </span>
                  </div>
                </div>
                
                <div class="flex items-center space-x-2">
                  <button class="p-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                  </button>
                  <button class="p-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <div v-if="userVideos.length === 0" class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">動画がありません</h3>
              <p class="mt-1 text-sm text-gray-500">最初の動画をアップロードしてチャンネルを開始しましょう。</p>
                </div>
            </div>
        </div>
      </main>
    </div>
  </div>
</template>
