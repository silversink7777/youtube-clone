<template>
  <div class="min-h-screen bg-white">
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
      
      <!-- Sidebar - Hidden on video page for better experience -->
      <AppSidebar :isCollapsed="true" />

      <!-- Main Content -->
      <main class="flex-1" style="margin-left: 80px;">
        <div class="max-w-[1754px] mx-auto flex gap-6 p-6">
          <!-- Primary Column (Video + Info) -->
          <div class="flex-1 max-w-[1280px]">
            <!-- Video Player -->
            <div class="bg-black rounded-lg overflow-hidden mb-4" style="aspect-ratio: 16/9;">
              <video 
                ref="videoPlayer"
                :src="video.video_url"
                class="w-full h-full"
                controls
                autoplay
                @loadedmetadata="onVideoLoaded"
                @error="onVideoError"
              >
                お使いのブラウザは動画の再生をサポートしていません。
              </video>
            </div>

            <!-- Video Title -->
            <h1 class="text-xl font-semibold text-black mb-3 leading-7">
              {{ video.title }}
            </h1>
            
            <!-- Channel Info & Actions Row -->
            <div class="flex items-center justify-between mb-4">
              <!-- Left: Channel Info -->
              <div class="flex items-center space-x-3">
                <!-- Channel avatar -->
                <div :class="['w-10 h-10 rounded-full flex-shrink-0', video.channelColor || 'bg-gray-400']">
                  <img 
                    v-if="video.channelAvatar"
                    :src="video.channelAvatar"
                    :alt="video.channelName"
                    class="w-full h-full rounded-full object-cover"
                  >
                  <div 
                    v-else
                    class="w-full h-full rounded-full flex items-center justify-center text-white text-sm font-medium"
                  >
                    {{ video.channelInitial }}
                  </div>
                </div>
                
                <div class="flex flex-col">
                  <h3 class="font-medium text-black text-sm">{{ video.channelName }}</h3>
                  <p class="text-gray-600 text-xs">
                    {{ formatViews(video.views) }} 回視聴 • {{ formatTimeAgo(video.publishedAt) }}
                  </p>
                </div>

                <!-- Subscribe Button -->
                <button class="ml-6 px-6 py-2 bg-black text-white rounded-full text-sm font-medium hover:bg-gray-800 transition-colors">
                  チャンネル登録
                </button>
              </div>

              <!-- Right: Action buttons -->
              <div class="flex items-center space-x-2">
                <!-- Like/Dislike Button Group -->
                <div class="flex items-center bg-gray-100 rounded-full overflow-hidden">
                  <button class="flex items-center space-x-2 px-4 py-2 hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                    </svg>
                    <span class="text-sm font-medium">高評価</span>
                  </button>
                  <div class="w-px h-6 bg-gray-300"></div>
                  <button class="px-4 py-2 hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13v-9m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                    </svg>
                  </button>
                </div>
                
                <!-- Share Button -->
                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                  </svg>
                  <span class="text-sm font-medium">共有</span>
                </button>
                
                <!-- Clip Button -->
                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V3a1 1 0 011 1v10.586A2 2 0 0116.586 16H7.414A2 2 0 016 14.586V4a1 1 0 011-1z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V9a1 1 0 00-1-1H8a1 1 0 00-1 1v1"></path>
                  </svg>
                  <span class="text-sm font-medium">クリップ</span>
                </button>

                <!-- Save Button -->
                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                  </svg>
                  <span class="text-sm font-medium">保存</span>
                </button>

                <!-- More Options -->
                <button class="flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Description Section -->
            <div v-if="video.description" class="bg-gray-100 rounded-lg p-3 mb-6">
              <div class="text-sm">
                <p class="font-medium text-black mb-1">
                  {{ formatViews(video.views) }} 回視聴 {{ formatTimeAgo(video.publishedAt) }}
                </p>
                <div class="text-gray-800 leading-relaxed whitespace-pre-line">
                  {{ truncatedDescription }}
                  <button 
                    v-if="video.description.length > 150"
                    @click="toggleDescription"
                    class="text-black font-medium ml-1 hover:underline"
                  >
                    {{ showFullDescription ? '簡潔に表示' : '...さらに表示' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8">
              <div class="flex items-center space-x-4 mb-6">
                <h3 class="text-xl font-semibold text-black">コメント</h3>
                <button class="flex items-center space-x-1 text-sm text-gray-600 hover:text-black">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                  </svg>
                  <span>並び替え</span>
                </button>
              </div>
              
              <!-- Add Comment -->
              <div v-if="$page.props.auth.user" class="flex space-x-3 mb-6">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                  {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1">
                  <input 
                    type="text" 
                    placeholder="コメントを追加..."
                    class="w-full bg-transparent border-0 border-b border-gray-300 focus:border-black focus:ring-0 text-sm py-2"
                  >
                </div>
              </div>

              <!-- Comments Placeholder -->
              <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-sm">コメント機能は今後実装予定です</p>
              </div>
            </div>
          </div>

          <!-- Secondary Column (Related Videos) -->
          <div class="w-[426px] flex-shrink-0">
            <div class="space-y-2">
              <!-- Filter Tabs -->
              <div class="flex space-x-2 mb-4">
                <button class="px-3 py-1 bg-black text-white rounded-lg text-sm font-medium">
                  すべて
                </button>
                <button class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-black rounded-lg text-sm font-medium">
                  このチャンネルから
                </button>
              </div>

              <h3 class="font-semibold text-black mb-4">関連動画</h3>
              
              <!-- Related Videos Placeholder -->
              <div class="space-y-3">
                <div v-for="i in 8" :key="i" class="flex space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                  <div class="w-40 h-24 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-black line-clamp-2 mb-1">
                      関連動画のタイトル {{ i }}
                    </h4>
                    <p class="text-xs text-gray-600 mb-1">チャンネル名</p>
                    <p class="text-xs text-gray-600">
                      {{ Math.floor(Math.random() * 1000) }}万 回視聴 • {{ Math.floor(Math.random() * 30) + 1 }}日前
                    </p>
                  </div>
                </div>
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
import { router } from '@inertiajs/vue3'
import AppHeader from '../../Components/YouTube/AppHeader.vue'
import AppSidebar from '../../Components/YouTube/AppSidebar.vue'

const props = defineProps({
  video: {
    type: Object,
    required: true
  }
})

const sidebarCollapsed = ref(true) // Always collapsed on video page
const videoPlayer = ref(null)
const showFullDescription = ref(false)

const toggleSidebar = () => {
  // Sidebar stays collapsed on video page for better viewing experience
  sidebarCollapsed.value = true
}

const handleSearch = (query) => {
  router.get('/', { search: query })
}

const toggleDescription = () => {
  showFullDescription.value = !showFullDescription.value
}

const truncatedDescription = computed(() => {
  if (!props.video.description) return ''
  
  if (showFullDescription.value || props.video.description.length <= 150) {
    return props.video.description
  }
  
  return props.video.description.substring(0, 150)
})

const onVideoLoaded = () => {
  console.log('Video loaded successfully')
}

const onVideoError = (event) => {
  console.error('Video loading error:', event)
}

const formatViews = (views) => {
  if (views >= 10000) {
    return `${(views / 10000).toFixed(1)}万`
  }
  return views.toLocaleString()
}

const formatTimeAgo = (publishedAt) => {
  const now = new Date()
  const published = new Date(publishedAt)
  const diffInHours = Math.floor((now - published) / (1000 * 60 * 60))
  
  if (diffInHours < 24) {
    return `${diffInHours} 時間前`
  }
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 30) {
    return `${diffInDays} 日前`
  }
  
  const diffInMonths = Math.floor(diffInDays / 30)
  if (diffInMonths < 12) {
    return `${diffInMonths} か月前`
  }
  
  const diffInYears = Math.floor(diffInMonths / 12)
  return `${diffInYears} 年前`
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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