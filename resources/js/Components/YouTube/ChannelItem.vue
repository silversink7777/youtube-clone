<template>
  <a 
    href="#" 
    class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 group"
  >
    <div :class="['w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-medium', color]">
      {{ avatar }}
    </div>
    <span class="text-sm text-gray-700 flex-1 truncate">{{ name }}</span>
    <div 
      v-if="hasNotification" 
      class="w-2 h-2 bg-red-500 rounded-full flex-shrink-0"
    />
  </a>
</template>

<script setup>
defineProps({
  name: {
    type: String,
    required: true
  },
  avatar: {
    type: String,
    required: true
  },
  color: {
    type: String,
    default: 'bg-gray-500'
  },
  hasNotification: {
    type: Boolean,
    default: false
  }
})
</script> 