<template>
  <Link :href="`/watch/${video.id}`" class="cursor-pointer group block">
    <div 
      class="relative overflow-hidden rounded-xl"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
    >
      <!-- 動画プレビュー -->
      <div class="w-full h-44 bg-gray-900 flex items-center justify-center">
        <video 
          ref="videoRef"
          :src="video.video_url"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          muted
          loop
          preload="metadata"
          :poster="video.thumbnail"
          @loadeddata="handleVideoLoaded"
          @error="handleVideoError"
          @canplay="handleCanPlay"
        >
          お使いのブラウザは動画の再生をサポートしていません。
        </video>
        
        <!-- ローディング状態 -->
        <div 
          v-if="!videoLoaded && !videoError" 
          class="absolute inset-0 bg-gray-900 flex items-center justify-center"
        >
          <svg class="w-8 h-8 text-white animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        
        <!-- エラー状態のフォールバック -->
        <div 
          v-if="videoError" 
          class="absolute inset-0 bg-gray-800 flex flex-col items-center justify-center text-gray-300"
        >
          <svg class="w-12 h-12 mb-2" fill="currentColor" viewBox="0 0 24 24">
            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
          </svg>
          <span class="text-xs">動画を読み込めません</span>
        </div>

        <!-- フォールバック画像 -->
        <img 
          v-if="videoError && video.thumbnail"
          :src="video.thumbnail"
          :alt="video.title"
          class="absolute inset-0 w-full h-full object-cover"
        >
      </div>
      
      <!-- Play button overlay (ホバー時のみ表示) -->
      <div 
        v-if="!isHovering || videoError"
        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      >
        <div class="bg-black bg-opacity-70 rounded-full p-4">
          <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8 5v14l11-7z"/>
          </svg>
        </div>
      </div>

      <!-- 動画再生中インジケーター -->
      <div 
        v-if="isPlaying && isHovering"
        class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded font-medium flex items-center space-x-1"
      >
        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
        <span>プレビュー</span>
      </div>
      
      <!-- Duration badge -->
      <div class="absolute bottom-2 right-2 bg-black bg-opacity-80 text-white text-xs px-2 py-1 rounded font-medium">
        {{ video.duration }}
      </div>
      
      <!-- Channel badge (if exists) -->
      <div 
        v-if="video.channelBadge"
        class="absolute top-2 right-2 bg-black bg-opacity-80 text-white text-xs px-2 py-1 rounded font-medium"
      >
        {{ video.channelBadge }}
      </div>
      
      <!-- Special badge (if exists) -->
      <div 
        v-if="video.specialBadge"
        :class="[
          'absolute top-2 right-2 text-white text-xs px-2 py-1 rounded font-medium',
          video.specialBadgeColor || 'bg-red-600'
        ]"
      >
        {{ video.specialBadge }}
      </div>
      
      <!-- Hover overlay -->
      <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
    </div>
    
    <!-- Video info -->
    <div class="flex mt-3 space-x-3">
      <div :class="['w-9 h-9 rounded-full flex-shrink-0', video.channelColor || 'bg-gray-400']">
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
      
      <div class="flex-1 min-w-0">
        <h3 class="font-medium text-sm line-clamp-2 text-gray-900 group-hover:text-blue-600 transition-colors leading-5 mb-1">
          {{ video.title }}
        </h3>
        <p class="text-gray-600 text-xs hover:text-gray-800 transition-colors">
          {{ video.channelName }}
        </p>
        <p class="text-gray-600 text-xs mt-1">
          {{ formatViews(video.views) }} 回視聴 • {{ formatTimeAgo(video.publishedAt) }}
        </p>
      </div>
      
      <!-- Menu button -->
      <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
        <button 
          class="p-1 rounded-full hover:bg-gray-100"
          @click.prevent.stop="showMenu = !showMenu"
        >
          <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
          </svg>
        </button>
      </div>
    </div>
  </Link>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  video: {
    type: Object,
    required: true
  }
})

const showMenu = ref(false)
const videoLoaded = ref(false)
const videoError = ref(false)
const isHovering = ref(false)
const isPlaying = ref(false)
const canPlay = ref(false)
const videoRef = ref(null)

// ホバー時の遅延タイマー
let hoverTimer = null

const handleMouseEnter = async () => {
  isHovering.value = true
  
  // 少し遅延を入れてから動画再生（誤操作防止）
  hoverTimer = setTimeout(async () => {
    if (isHovering.value && canPlay.value && videoRef.value && !videoError.value) {
      try {
        await videoRef.value.play()
        isPlaying.value = true
      } catch (error) {
        console.log('動画再生エラー:', error)
        // 自動再生がブロックされた場合は無視
      }
    }
  }, 500) // 500ms後に再生開始
}

const handleMouseLeave = () => {
  isHovering.value = false
  isPlaying.value = false
  
  // タイマーをクリア
  if (hoverTimer) {
    clearTimeout(hoverTimer)
    hoverTimer = null
  }
  
  // 動画を停止して最初に戻す
  if (videoRef.value && !videoError.value) {
    videoRef.value.pause()
    videoRef.value.currentTime = 0
  }
}

const handleVideoLoaded = () => {
  videoLoaded.value = true
  videoError.value = false
}

const handleVideoError = () => {
  videoError.value = true
  videoLoaded.value = false
  canPlay.value = false
}

const handleCanPlay = () => {
  canPlay.value = true
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

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* 動画要素のカスタムスタイル */
video {
  transition: all 0.3s ease;
}

video::-webkit-media-controls {
  display: none;
}

video::-webkit-media-controls-enclosure {
  display: none;
}
</style> 