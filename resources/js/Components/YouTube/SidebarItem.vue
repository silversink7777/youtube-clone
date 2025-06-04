<template>
  <a 
    href="#" 
    :class="[
      'flex items-center px-3 py-2 rounded-lg transition-colors duration-200 group',
      active ? 'bg-gray-100 font-medium' : 'hover:bg-gray-100',
      collapsed ? 'justify-center' : 'space-x-3'
    ]"
    :title="collapsed ? label : null"
  >
    <div class="flex-shrink-0">
      <component :is="iconComponent" class="w-5 h-5" />
    </div>
    <span 
      v-if="!collapsed" 
      :class="[
        'text-sm transition-colors',
        active ? 'text-black font-medium' : 'text-gray-700'
      ]"
    >
      {{ label }}
    </span>
  </a>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  icon: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  },
  collapsed: {
    type: Boolean,
    default: false
  }
})

const iconComponent = computed(() => {
  const iconMap = {
    home: 'HomeIcon',
    shorts: 'ShortsIcon',
    subscriptions: 'SubscriptionsIcon',
    music: 'MusicIcon',
    history: 'HistoryIcon',
    playlist: 'PlaylistIcon',
    video: 'VideoIcon',
    'watch-later': 'WatchLaterIcon',
    like: 'LikeIcon',
    offline: 'OfflineIcon'
  }
  
  return iconMap[props.icon] || 'HomeIcon'
})
</script>

<script>
// Icon components
const HomeIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
    </svg>
  `
}

const ShortsIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M17.77 10.32l-1.2-.5L18 9.06c1.84-.96 2.53-3.23 1.56-5.06s-3.24-2.53-5.07-1.56L6 6.94c-1.29.68-2.07 2.04-2 3.49.07 1.42.93 2.67 2.22 3.25.03.01 1.2.5 1.2.5L6 14.93c-1.83.97-2.53 3.24-1.56 5.07.97 1.83 3.24 2.53 5.07 1.56l8.5-4.5c1.29-.68 2.06-2.04 1.99-3.49-.07-1.42-.94-2.68-2.23-3.25zM10 14.65v-5.3L15 12l-5 2.65z"/>
    </svg>
  `
}

const SubscriptionsIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M20 8H4V6h16v2zm-2-6H6v2h12V2zm4 10v8c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2v-8c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2zm-6 4l-6-3.27v6.53L16 16z"/>
    </svg>
  `
}

const MusicIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
    </svg>
  `
}

const HistoryIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
    </svg>
  `
}

const PlaylistIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M14 10H2v2h12v-2zm0-4H2v2h12V6zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zM2 16h8v-2H2v2z"/>
    </svg>
  `
}

const VideoIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
    </svg>
  `
}

const WatchLaterIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
    </svg>
  `
}

const LikeIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
    </svg>
  `
}

const OfflineIcon = {
  template: `
    <svg fill="currentColor" viewBox="0 0 24 24">
      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
    </svg>
  `
}

export default {
  components: {
    HomeIcon,
    ShortsIcon,
    SubscriptionsIcon,
    MusicIcon,
    HistoryIcon,
    PlaylistIcon,
    VideoIcon,
    WatchLaterIcon,
    LikeIcon,
    OfflineIcon
  }
}
</script> 