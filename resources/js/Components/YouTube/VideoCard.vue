<template>
  <div class="cursor-pointer group">
    <div class="relative overflow-hidden rounded-xl">
      <img 
        :src="video.thumbnail"
        :alt="video.title"
        class="w-full h-44 object-cover transition-transform duration-300 group-hover:scale-105"
      >
      
      <!-- Duration badge -->
      <div class="absolute bottom-2 right-2 bg-black bg-opacity-80 text-white text-xs px-2 py-1 rounded font-medium">
        {{ video.duration }}
      </div>
      
      <!-- Channel badge (if exists) -->
      <div 
        v-if="video.channelBadge"
        class="absolute top-2 left-2 bg-black bg-opacity-80 text-white text-xs px-2 py-1 rounded font-medium"
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
        <button class="p-1 rounded-full hover:bg-gray-100">
          <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  video: {
    type: Object,
    required: true
  }
})

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
</style> 