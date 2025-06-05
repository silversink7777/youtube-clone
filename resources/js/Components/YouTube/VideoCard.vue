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
          :src="getVideoUrl"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          muted
          loop
          :preload="isExternalUrl ? 'metadata' : 'auto'"
          :crossorigin="isExternalUrl ? 'anonymous' : null"
          :poster="video.thumbnail"
          @loadeddata="handleVideoLoaded"
          @error="handleVideoError"
          @canplay="handleCanPlay"
          @loadstart="handleLoadStart"
        >
          お使いのブラウザは動画の再生をサポートしていません。
        </video>
        
        <!-- ローディング状態 -->
        <div 
          v-if="isLoading && !videoError" 
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
          <span class="text-xs">{{ errorMessage }}</span>
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
import { ref, nextTick, computed } from 'vue'
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
const isLoading = ref(true)
const videoRef = ref(null)
const errorMessage = ref('動画を読み込めません')
const useDirectUrl = ref(false) // プロキシが失敗した場合の直接URL使用フラグ

// 外部URLかどうかを判定
const isExternalUrl = computed(() => {
  return props.video.video_url && (
    props.video.video_url.startsWith('http://') || 
    props.video.video_url.startsWith('https://')
  )
})

// 動画のURLを取得（外部URLの場合はプロキシ経由、失敗時は直接URL）
const getVideoUrl = computed(() => {
  if (isExternalUrl.value && !useDirectUrl.value) {
    return `/video-proxy?url=${encodeURIComponent(props.video.video_url)}`
  }
  return props.video.video_url
})

// ホバー時の遅延タイマー
let hoverTimer = null

const handleLoadStart = () => {
  isLoading.value = true
  videoError.value = false
}

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
  isLoading.value = false
}

const handleVideoError = async (event) => {
  console.error('動画読み込みエラー:', event)
  console.log('動画URL:', getVideoUrl.value)
  console.log('外部URL:', isExternalUrl.value)
  console.log('動画データ:', props.video)
  
  // 外部URLでプロキシを使用していて、まだ直接URLを試していない場合
  if (isExternalUrl.value && !useDirectUrl.value) {
    console.log('プロキシでの読み込みに失敗。直接URLを試行します...')
    useDirectUrl.value = true
    
    // 少し待ってから動画要素のsrcを更新
    await nextTick()
    if (videoRef.value) {
      videoRef.value.load() // 動画を再読み込み
    }
    return
  }
  
  // エラー処理
  videoError.value = true
  videoLoaded.value = false
  canPlay.value = false
  isLoading.value = false
  
  // エラーの詳細を取得
  if (event && event.target && event.target.error) {
    const error = event.target.error
    console.error('動画読み込みエラー詳細:', error)
    console.error('エラーコード:', error.code)
    console.error('エラーメッセージ:', error.message)
    
    switch (error.code) {
      case error.MEDIA_ERR_ABORTED:
        errorMessage.value = '動画の読み込みが中断されました'
        break
      case error.MEDIA_ERR_NETWORK:
        errorMessage.value = 'ネットワークエラー'
        break
      case error.MEDIA_ERR_DECODE:
        errorMessage.value = '動画形式がサポートされていません'
        break
      case error.MEDIA_ERR_SRC_NOT_SUPPORTED:
        errorMessage.value = isExternalUrl.value ? '外部動画にアクセスできません' : 'ローカル動画にアクセスできません'
        break
      default:
        errorMessage.value = '動画を読み込めません'
    }
  } else {
    errorMessage.value = isExternalUrl.value ? '外部動画にアクセスできません' : 'ローカル動画にアクセスできません'
  }
}

const handleCanPlay = () => {
  canPlay.value = true
  isLoading.value = false
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