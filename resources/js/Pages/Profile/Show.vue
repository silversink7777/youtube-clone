<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppHeader from '../../Components/YouTube/AppHeader.vue'
import AppSidebar from '../../Components/YouTube/AppSidebar.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import TwoFactorAuthenticationForm from './Partials/TwoFactorAuthenticationForm.vue'
import LogoutOtherBrowserSessionsForm from './Partials/LogoutOtherBrowserSessionsForm.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
})

const sidebarCollapsed = ref(false)
const activeTab = ref('profile')

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value
}

const handleSearch = (query) => {
    console.log('Search:', query)
}

const setActiveTab = (tab) => {
    activeTab.value = tab
}

const tabs = [
    { id: 'profile', name: 'プロフィール', icon: 'user' },
    { id: 'channel', name: 'チャンネル', icon: 'tv' },
    { id: 'security', name: 'セキュリティ', icon: 'shield' },
    { id: 'advanced', name: '詳細設定', icon: 'cog' }
]
</script>

<template>
    <Head title="プロフィール設定 - YouTube" />

    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <AppHeader 
            @toggle-sidebar="toggleSidebar" 
            @search="handleSearch"
        />

        <div class="flex">
            <!-- Sidebar -->
            <AppSidebar :isCollapsed="sidebarCollapsed" />

            <!-- Main Content -->
            <main :class="['flex-1 transition-all duration-300', sidebarCollapsed ? 'ml-16' : 'ml-64']">
                <div class="max-w-7xl mx-auto py-8 px-6">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ $page.props.auth.user.name }}
                                </h1>
                                <p class="text-gray-600">アカウント設定を管理</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 mb-8">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="setActiveTab(tab.id)"
                                :class="[
                                    'flex items-center space-x-2 py-2 px-1 border-b-2 font-medium text-sm transition-colors',
                                    activeTab === tab.id
                                        ? 'border-red-500 text-red-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <svg v-if="tab.icon === 'user'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <svg v-else-if="tab.icon === 'tv'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <svg v-else-if="tab.icon === 'shield'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <svg v-else-if="tab.icon === 'cog'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ tab.name }}</span>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="space-y-8">
                        <!-- Profile Tab -->
                        <div v-show="activeTab === 'profile'">
                            <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                                <UpdateProfileInformationForm :user="$page.props.auth.user" />
                            </div>
                        </div>

                        <!-- Channel Tab -->
                        <div v-show="activeTab === 'channel'">
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">チャンネル設定</h3>
                                <p class="text-gray-600 mb-6">あなたのYouTubeチャンネルの詳細を管理します。</p>
                                
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            チャンネル名
                                        </label>
                                        <input
                                            type="text"
                                            :value="$page.props.auth.user.channel_name || $page.props.auth.user.name"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
                                            placeholder="チャンネル名を入力"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            チャンネル説明
                                        </label>
                                        <textarea
                                            rows="4"
                                            :value="$page.props.auth.user.channel_description"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
                                            placeholder="チャンネルの説明を入力してください..."
                                        ></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                            保存
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div v-show="activeTab === 'security'">
                            <div class="space-y-8">
                                <div v-if="$page.props.jetstream.canUpdatePassword">
                                    <UpdatePasswordForm />
                                </div>

                                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                                    <TwoFactorAuthenticationForm
                                        :requires-confirmation="confirmsTwoFactorAuthentication"
                                    />
                                </div>

                                <LogoutOtherBrowserSessionsForm :sessions="sessions" />
                            </div>
                        </div>

                        <!-- Advanced Tab -->
                        <div v-show="activeTab === 'advanced'">
                            <div class="space-y-8">
                                <div class="bg-white rounded-lg shadow p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">データとプライバシー</h3>
                                    <p class="text-gray-600 mb-6">あなたのアカウントに関連するデータを管理します。</p>
                                    
                                    <div class="space-y-4">
                                        <button class="flex items-center justify-between w-full p-4 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <div>
                                                <h4 class="font-medium text-gray-900">データをダウンロード</h4>
                                                <p class="text-sm text-gray-500">あなたのアカウントデータのコピーをダウンロードします</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                        
                                        <button class="flex items-center justify-between w-full p-4 text-left border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                            <div>
                                                <h4 class="font-medium text-gray-900">プライバシー設定</h4>
                                                <p class="text-sm text-gray-500">データの共有とプライバシー設定を管理</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                                    <DeleteUserForm />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
