<template>
    <AppLayout title="動画をアップロード">
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-4xl mx-auto px-4">
                <!-- ヘッダー -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-8 h-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14,13h-3v3H9v-3H6v-2h3V8h2v3h3v2z"/>
                        </svg>
                        動画をアップロード
                    </h1>
                    <p class="text-gray-600 mt-2">あなたの動画を世界中の人々と共有しましょう</p>
                </div>

                <!-- アップロードフォーム -->
                <div class="bg-white rounded-lg shadow">
                    <form @submit.prevent="submitUpload" enctype="multipart/form-data">
                        <div class="p-6 space-y-6">
                            <!-- 動画ファイル選択 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    動画ファイル <span class="text-red-500">*</span>
                                </label>
                                <div v-if="!form.video" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-red-400" @click="$refs.videoInput.click()">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-700">動画ファイルを選択</p>
                                    <p class="text-gray-500">MP4, AVI, MOV, WMV, WebM (最大1GB)</p>
                                </div>
                                <div v-else class="border border-gray-300 rounded-lg p-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                        </svg>
                                        <div>
                                            <p class="font-medium">{{ form.video.name }}</p>
                                            <p class="text-sm text-gray-500">{{ formatFileSize(form.video.size) }}</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeVideo" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <input ref="videoInput" type="file" @change="handleVideoSelect" accept="video/*" class="hidden">
                                <div v-if="errors.video" class="mt-2 text-sm text-red-600">{{ errors.video }}</div>
                            </div>

                            <!-- 一般的なエラーメッセージ -->
                            <div v-if="errors.general" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm text-red-800">{{ errors.general }}</div>
                                </div>
                            </div>

                            <!-- タイトル -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    タイトル <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.title" type="text" maxlength="255" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="魅力的なタイトルを入力してください">
                                <div v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</div>
                            </div>

                            <!-- 説明 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">説明</label>
                                <textarea v-model="form.description" rows="4" maxlength="5000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="動画の内容について説明してください"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- カテゴリ -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        カテゴリ <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        <option value="">選択してください</option>
                                        <option value="gaming">ゲーム</option>
                                        <option value="music">音楽</option>
                                        <option value="education">教育</option>
                                        <option value="entertainment">エンターテイメント</option>
                                        <option value="sports">スポーツ</option>
                                        <option value="technology">テクノロジー</option>
                                        <option value="news">ニュース</option>
                                        <option value="lifestyle">ライフスタイル</option>
                                        <option value="comedy">コメディ</option>
                                        <option value="food">料理・グルメ</option>
                                    </select>
                                    <div v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category }}</div>
                                </div>

                                <!-- 公開設定 -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        公開設定 <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                        <option value="published">公開</option>
                                        <option value="unlisted">限定公開</option>
                                        <option value="private">非公開</option>
                                    </select>
                                </div>
                            </div>

                            <!-- サムネイル -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">サムネイル（オプション）</label>
                                <div class="flex items-center space-x-4">
                                    <div v-if="thumbnailPreview" class="relative">
                                        <img :src="thumbnailPreview" alt="サムネイル" class="w-32 h-18 object-cover rounded border">
                                        <button type="button" @click="removeThumbnail" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <button type="button" @click="$refs.thumbnailInput.click()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                        サムネイルを選択
                                    </button>
                                </div>
                                <input ref="thumbnailInput" type="file" @change="handleThumbnailSelect" accept="image/*" class="hidden">
                                <p class="mt-1 text-sm text-gray-500">推奨サイズ: 1280x720px (JPEG, PNG, GIF)</p>
                            </div>
                        </div>

                        <!-- フッター -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-between">
                            <Link :href="route('dashboard')" class="text-gray-600 hover:text-gray-800">キャンセル</Link>
                            <button type="submit" :disabled="!canSubmit || isUploading" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                {{ isUploading ? 'アップロード中...' : '動画をアップロード' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- アップロード進捗 -->
                <div v-if="isUploading" class="mt-6 bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium">アップロード進捗</span>
                        <span class="text-sm text-gray-500">{{ uploadProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" :style="{ width: uploadProgress + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = ref({
    title: '',
    description: '',
    video: null,
    thumbnail: null,
    category: '',
    status: 'published',
    tags: ''
})

const errors = ref({})
const isUploading = ref(false)
const uploadProgress = ref(0)
const thumbnailPreview = ref(null)

const canSubmit = computed(() => {
    return form.value.video && form.value.title.trim() && form.value.category && form.value.status
})

const handleVideoSelect = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.value.video = file
        if (!form.value.title) {
            form.value.title = file.name.replace(/\.[^/.]+$/, '')
        }
    }
}

const removeVideo = () => {
    form.value.video = null
    if (form.value.title === form.value.video?.name?.replace(/\.[^/.]+$/, '')) {
        form.value.title = ''
    }
}

const handleThumbnailSelect = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.value.thumbnail = file
        const reader = new FileReader()
        reader.onload = (e) => {
            thumbnailPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const removeThumbnail = () => {
    form.value.thumbnail = null
    thumbnailPreview.value = null
}

const formatFileSize = (bytes) => {
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    if (bytes === 0) return '0 Bytes'
    const i = Math.floor(Math.log(bytes) / Math.log(1024))
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const submitUpload = async () => {
    if (!canSubmit.value) return
    
    isUploading.value = true
    errors.value = {}
    uploadProgress.value = 0
    
    try {
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('description', form.value.description)
        formData.append('video', form.value.video)
        if (form.value.thumbnail) {
            formData.append('thumbnail', form.value.thumbnail)
        }
        formData.append('category', form.value.category)
        formData.append('status', form.value.status)
        formData.append('tags', form.value.tags)
        
        console.log('Upload data:', {
            title: form.value.title,
            category: form.value.category,
            status: form.value.status,
            videoSize: form.value.video?.size,
            videoType: form.value.video?.type
        })
        
        // 進捗シミュレーション
        const progressInterval = setInterval(() => {
            if (uploadProgress.value < 90) {
                uploadProgress.value += Math.random() * 10
            }
        }, 500)
        
        const response = await fetch(route('video.store'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        
        clearInterval(progressInterval)
        uploadProgress.value = 100
        
        console.log('Response status:', response.status)
        console.log('Response headers:', response.headers)
        
        if (!response.ok) {
            const errorText = await response.text()
            console.error('Error response:', errorText)
            throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`)
        }
        
        const data = await response.json()
        console.log('Response data:', data)
        
        if (data.success) {
            // 成功メッセージを表示
            console.log('Upload successful, redirecting...')
            
            // アップロード状態をリセット
            isUploading.value = false
            
            // 成功通知を表示（オプション）
            alert('動画のアップロードが完了しました！')
            
            // ページ遷移
            setTimeout(() => {
                try {
                    router.visit(route('video.manage'))
                } catch (navError) {
                    console.error('Navigation error:', navError)
                    // フォールバック：ページリロード
                    window.location.href = route('video.manage')
                }
            }, 500)
        } else {
            if (data.errors) {
                errors.value = data.errors
            }
            throw new Error(data.message || 'アップロードに失敗しました')
        }
        
    } catch (error) {
        console.error('Upload error:', error)
        isUploading.value = false
        uploadProgress.value = 0
        
        if (error.name === 'SyntaxError' && error.message.includes('Unexpected token')) {
            errors.value = { general: 'サーバーエラーが発生しました。ページを再読み込みして再試行してください。' }
        } else {
            errors.value = { general: error.message || 'アップロードに失敗しました' }
        }
    }
}
</script> 