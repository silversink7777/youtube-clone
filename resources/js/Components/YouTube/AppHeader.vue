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
        
        <Link href="/" class="flex items-center space-x-1">
          <div class="w-9 h-9 bg-red-600 rounded flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          </div>
          <span class="text-xl font-medium text-black hidden sm:block">YouTube</span>
          <span class="text-xs text-gray-500 hidden sm:block ml-1">JP</span>
        </Link>
      </div>

      <!-- Center section: Search -->
      <div class="flex-1 max-w-2xl mx-4 hidden md:block">
        <div class="flex">
          <div class="flex-1 relative">
            <input 
              type="text" 
              placeholder="検索" 
              v-model="searchQuery"
              @keyup.enter="performSearch"
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
          <button 
            @click="performSearch"
            class="px-6 py-2 bg-gray-50 border border-l-0 border-gray-300 rounded-r-full hover:bg-gray-100 transition-colors"
          >
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
        
        <!-- Create button (only for authenticated users) -->
        <div v-if="$page.props.auth.user" class="flex items-center space-x-1">
          <Link 
            :href="route('video.create')"
            class="p-2 rounded-full hover:bg-gray-100 transition-colors"
            title="動画をアップロード"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
          </Link>
          <span class="text-sm hidden lg:block">作成</span>
        </div>
        
        <!-- Notifications (only for authenticated users) -->
        <button 
          v-if="$page.props.auth.user"
          class="p-2 rounded-full hover:bg-gray-100 transition-colors relative"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5zm0 0V3"></path>
          </svg>
          <div class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></div>
        </button>
        
        <!-- User menu / Login button -->
        <div v-if="$page.props.auth.user" class="relative">
          <button 
            @click="toggleUserMenu"
            class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-100 transition-colors"
          >
            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
              {{ userInitial }}
            </div>
          </button>
          
          <!-- User dropdown menu -->
          <div 
            v-show="showUserMenu"
            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
          >
            <div class="px-4 py-3 border-b border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-semibold">
                  {{ userInitial }}
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ $page.props.auth.user.name }}</p>
                  <p class="text-xs text-gray-500">{{ $page.props.auth.user.email }}</p>
                </div>
              </div>
            </div>
            
            <div class="py-1">
              <Link 
                href="/user/profile" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                プロフィール
              </Link>
              
              <Link 
                href="/dashboard" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                ダッシュボード
              </Link>
              
              <Link 
                :href="route('video.manage')" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                動画管理
              </Link>
              
              <div class="border-t border-gray-200 my-1"></div>
              
              <form @submit.prevent="logout">
                <button 
                  type="submit"
                  class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  ログアウト
                </button>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Login/Register buttons for guests -->
        <div v-else class="flex items-center space-x-2">
          <Link 
            href="/login"
            class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors"
          >
            ログイン
          </Link>
          <Link 
            href="/register"
            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-full hover:bg-blue-700 transition-colors"
          >
            登録
          </Link>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const searchQuery = ref('')
const showUserMenu = ref(false)

const emit = defineEmits(['toggle-sidebar', 'search'])

const toggleSidebar = () => {
  emit('toggle-sidebar')
}

const clearSearch = () => {
  searchQuery.value = ''
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    emit('search', searchQuery.value.trim())
  }
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const userInitial = computed(() => {
  const user = window.page?.props?.auth?.user
  return user?.name ? user.name.charAt(0).toUpperCase() : 'U'
})

const logout = () => {
  router.post('/logout')
}

// Close user menu when clicking outside
const handleClickOutside = (event) => {
  const userMenu = event.target.closest('.relative')
  if (!userMenu) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script> 