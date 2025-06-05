<template>
    <AppLayout title="動画管理">
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-7xl mx-auto px-4">
                <!-- ヘッダー -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                                <svg class="w-8 h-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M10,16.5L16,12L10,7.5V16.5Z"/>
                                </svg>
                                動画管理
                            </h1>
                            <p class="text-gray-600 mt-1">アップロードした動画を管理・編集できます</p>
                        </div>
                        <Link :href="route('video.create')" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            新しい動画をアップロード
                        </Link>
                    </div>
                </div>

                <!-- 統計情報 -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">総動画数</p>
                                <p class="text-2xl font-bold text-gray-900">{{ videos.length }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">総再生回数</p>
                                <p class="text-2xl font-bold text-gray-900">{{ totalViews.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">公開中</p>
                                <p class="text-2xl font-bold text-gray-900">{{ publishedCount }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">非公開</p>
                                <p class="text-2xl font-bold text-gray-900">{{ privateCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 動画一覧 -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-medium text-gray-900">動画一覧</h2>
                            <div class="flex items-center space-x-4">
                                <!-- フィルター -->
                                <select v-model="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="">すべて</option>
                                    <option value="published">公開中</option>
                                    <option value="private">非公開</option>
                                    <option value="unlisted">限定公開</option>
                                </select>
                                <!-- ソート -->
                                <select v-model="sortBy" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="created_at">アップロード日</option>
                                    <option value="views">再生回数</option>
                                    <option value="title">タイトル</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div v-if="filteredVideos.length === 0" class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">動画がありません</h3>
                        <p class="text-gray-500 mb-4">最初の動画をアップロードしてみましょう</p>
                        <Link :href="route('video.create')" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600">
                            動画をアップロード
                        </Link>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div v-for="video in filteredVideos" :key="video.id" class="p-6 hover:bg-gray-50">
                            <div class="flex items-start space-x-4">
                                <!-- サムネイル -->
                                <div class="flex-shrink-0">
                                    <div class="w-40 h-24 bg-gray-200 rounded-lg overflow-hidden">
                                        <img v-if="video.thumbnail" :src="video.thumbnail" :alt="video.title" class="w-full h-full object-cover">
                                        <div v-else class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- 動画情報 -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-medium text-gray-900 mb-1 truncate">{{ video.title }}</h3>
                                            <p v-if="video.description" class="text-gray-600 text-sm mb-2 line-clamp-2">{{ video.description }}</p>
                                            
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    {{ video.views.toLocaleString() }} 回再生
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ video.created_at }}
                                                </span>
                                                <span class="flex items-center">
                                                    <span :class="getStatusClasses(video.status)">
                                                        {{ getStatusText(video.status) }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- アクションボタン -->
                                        <div class="flex items-center space-x-2 ml-4">
                                            <Link :href="route('video.show', video.id)" class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50" title="視聴">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-10 5l5.803-3.887a1 1 0 011.394 0L15 19"/>
                                                </svg>
                                            </Link>
                                            <Link :href="route('video.edit', video.id)" class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50" title="編集">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </Link>
                                            <button @click="deleteVideo(video)" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50" title="削除">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 削除確認モーダル -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>
                動画の削除
            </template>

            <template #content>
                この動画を削除してもよろしいですか？この操作は取り消すことができません。
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="font-medium">{{ videoToDelete?.title }}</p>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">
                    キャンセル
                </SecondaryButton>

                <DangerButton class="ml-3" @click="confirmDeleteVideo" :class="{ 'opacity-25': isDeleting }" :disabled="isDeleting">
                    削除する
                </DangerButton>
            </template>
        </ConfirmationModal>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'

// Props
const props = defineProps({
    videos: Array,
    pagination: Object
})

// リアクティブデータ
const statusFilter = ref('')
const sortBy = ref('created_at')
const showDeleteModal = ref(false)
const videoToDelete = ref(null)
const isDeleting = ref(false)

// 計算プロパティ
const totalViews = computed(() => {
    return props.videos.reduce((total, video) => total + video.views, 0)
})

const publishedCount = computed(() => {
    return props.videos.filter(video => video.status === 'published').length
})

const privateCount = computed(() => {
    return props.videos.filter(video => video.status === 'private').length
})

const filteredVideos = computed(() => {
    let filtered = props.videos

    // ステータスフィルター
    if (statusFilter.value) {
        filtered = filtered.filter(video => video.status === statusFilter.value)
    }

    // ソート
    filtered.sort((a, b) => {
        switch (sortBy.value) {
            case 'views':
                return b.views - a.views
            case 'title':
                return a.title.localeCompare(b.title)
            case 'created_at':
            default:
                return new Date(b.created_at) - new Date(a.created_at)
        }
    })

    return filtered
})

// メソッド
const deleteVideo = (video) => {
    videoToDelete.value = video
    showDeleteModal.value = true
}

const confirmDeleteVideo = async () => {
    if (!videoToDelete.value) return

    isDeleting.value = true

    try {
        const response = await fetch(route('video.destroy', videoToDelete.value.id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            }
        })

        const data = await response.json()

        if (data.success) {
            // ページをリロードして更新されたデータを取得
            router.reload()
        } else {
            alert(data.message || '動画の削除に失敗しました')
        }
    } catch (error) {
        console.error('Delete error:', error)
        alert('動画の削除中にエラーが発生しました')
    } finally {
        isDeleting.value = false
        showDeleteModal.value = false
        videoToDelete.value = null
    }
}

// ステータス表示のためのヘルパー関数
const getStatusText = (status) => {
    switch (status) {
        case 'published': return '公開中'
        case 'private': return '非公開'
        case 'unlisted': return '限定公開'
        default: return status
    }
}

const getStatusClasses = (status) => {
    const base = 'px-2 py-1 text-xs font-medium rounded-full'
    switch (status) {
        case 'published': return `${base} bg-green-100 text-green-800`
        case 'private': return `${base} bg-gray-100 text-gray-800`
        case 'unlisted': return `${base} bg-yellow-100 text-yellow-800`
        default: return `${base} bg-gray-100 text-gray-800`
    }
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