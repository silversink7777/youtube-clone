<template>
  <aside 
    :class="[
      'bg-white h-screen sticky top-16 overflow-y-auto transition-all duration-300',
      isCollapsed ? 'w-16' : 'w-64'
    ]"
  >
    <div class="py-3">
      <!-- Primary navigation -->
      <div class="space-y-1 px-3">
        <SidebarItem
          icon="home"
          label="ホーム"
          :collapsed="isCollapsed"
          :active="true"
        />
        <SidebarItem
          icon="shorts"
          label="ショート"
          :collapsed="isCollapsed"
        />
        <SidebarItem
          icon="subscriptions"
          label="登録チャンネル"
          :collapsed="isCollapsed"
        />
      </div>

      <hr class="my-3" v-if="!isCollapsed">

      <!-- YouTube Music -->
      <div class="space-y-1 px-3" v-if="!isCollapsed">
        <SidebarItem
          icon="music"
          label="YouTube Music"
          :collapsed="isCollapsed"
        />
      </div>

      <hr class="my-3" v-if="!isCollapsed">

      <!-- My Page section -->
      <div class="space-y-1" v-if="!isCollapsed">
        <div class="flex items-center justify-between px-6 py-2">
          <span class="text-sm font-medium text-gray-700">マイページ</span>
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </div>
        <div class="px-3">
          <SidebarItem
            icon="history"
            label="履歴"
            :collapsed="isCollapsed"
          />
          <SidebarItem
            icon="playlist"
            label="再生リスト"
            :collapsed="isCollapsed"
          />
          <SidebarItem
            icon="video"
            label="作成した動画"
            :collapsed="isCollapsed"
          />
          <SidebarItem
            icon="watch-later"
            label="後で見る"
            :collapsed="isCollapsed"
          />
          <SidebarItem
            icon="like"
            label="高く評価した動画"
            :collapsed="isCollapsed"
          />
          <SidebarItem
            icon="offline"
            label="オフライン"
            :collapsed="isCollapsed"
          />
        </div>
      </div>

      <hr class="my-3" v-if="!isCollapsed">

      <!-- Subscribed Channels -->
      <div class="space-y-1" v-if="!isCollapsed">
        <div class="px-6 py-2">
          <span class="text-sm font-medium text-gray-700">登録チャンネル</span>
        </div>
        <div class="px-3">
          <ChannelItem
            name="avex"
            avatar="A"
            color="bg-blue-500"
            :hasNotification="true"
          />
          <ChannelItem
            name="HAKODATE LIVE ..."
            avatar="H"
            color="bg-red-500"
            :hasNotification="true"
          />
        </div>
        
        <div class="text-xs text-gray-500 px-6 py-2">
          https://music.youtube.com
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import SidebarItem from './SidebarItem.vue'
import ChannelItem from './ChannelItem.vue'

defineProps({
  isCollapsed: {
    type: Boolean,
    default: false
  }
})
</script> 