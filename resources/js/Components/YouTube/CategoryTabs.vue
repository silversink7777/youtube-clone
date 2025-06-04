<template>
  <div class="flex space-x-3 mb-6 overflow-x-auto scrollbar-hide">
    <button
      v-for="category in categories"
      :key="category.id"
      @click="selectCategory(category.id)"
      :class="[
        'px-4 py-2 rounded-full text-sm whitespace-nowrap transition-all duration-200 font-medium',
        activeCategory === category.id
          ? 'bg-black text-white'
          : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
      ]"
    >
      {{ category.name }}
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  initialCategory: {
    type: String,
    default: 'all'
  }
})

const emit = defineEmits(['category-changed'])

const activeCategory = ref(props.initialCategory)

const categories = [
  { id: 'all', name: 'すべて' },
  { id: 'music', name: '音楽' },
  { id: 'news', name: 'ニュース' },
  { id: 'mix', name: 'ミックス' },
  { id: 'live', name: 'ライブ' },
  { id: 'podcast', name: 'ポッドキャスト' },
  { id: 'pets', name: 'ペット' },
  { id: 'soccer', name: 'サッカー' },
  { id: 'animation', name: 'アニメーション' },
  { id: 'recent', name: '最近アップロードされた動画' },
  { id: 'watched', name: '視聴済み' }
]

const selectCategory = (categoryId) => {
  activeCategory.value = categoryId
  emit('category-changed', categoryId)
}
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style> 