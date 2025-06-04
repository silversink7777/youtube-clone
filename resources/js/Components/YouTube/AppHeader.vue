<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="flex items-center justify-between px-4 py-2">
      <!-- Left section: Hamburger menu & Logo -->
      <div class="flex items-center space-x-4 min-w-0">
        <button 
          @click="toggleSidebar"
          class="p-2 rounded-full hover:bg-gray-100 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        
        <div class="flex items-center space-x-1">
          <div class="w-9 h-9 bg-red-600 rounded flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          </div>
          <span class="text-xl font-medium text-black hidden sm:block">YouTube</span>
          <span class="text-xs text-gray-500 hidden sm:block ml-1">JP</span>
        </div>
      </div>

      <!-- Center section: Search -->
      <div class="flex-1 max-w-2xl mx-4 hidden md:block">
        <div class="flex">
          <div class="flex-1 relative">
            <input 
              type="text" 
              placeholder="検索" 
              v-model="searchQuery"
              class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-l-full focus:outline-none focus:border-blue-500 text-sm"
            >
            <button 
              v-if="searchQuery" 
              @click="clearSearch"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <button class="px-6 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-full hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </button>
          <button class="ml-2 p-2 rounded-full hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Right section: Actions -->
      <div class="flex items-center space-x-2">
        <!-- Mobile search -->
        <button class="p-2 rounded-full hover:bg-gray-100 md:hidden transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </button>
        
        <!-- Create button -->
        <div class="flex items-center space-x-1">
          <button class="p-2 rounded-full hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
          </button>
          <span class="text-sm hidden lg:block">作成</span>
        </div>
        
        <!-- Notifications -->
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors relative">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5zm0 0V3"></path>
          </svg>
          <div class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></div>
        </button>
        
        <!-- User avatar -->
        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white font-semibold text-sm cursor-pointer hover:opacity-90 transition-opacity">
          ア
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'

const searchQuery = ref('')

const emit = defineEmits(['toggle-sidebar'])

const toggleSidebar = () => {
  emit('toggle-sidebar')
}

const clearSearch = () => {
  searchQuery.value = ''
}
</script> 