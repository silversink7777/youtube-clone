<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <div class="bg-white rounded-lg shadow border-l-4 border-red-500">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-red-900">アカウントを削除</h3>
            <p class="text-sm text-gray-600 mt-1">アカウントを完全に削除します。</p>
        </div>

        <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800 mb-1">重要な注意事項</h4>
                        <p class="text-sm text-red-700">
                            アカウントを削除すると、すべてのリソースとデータが完全に削除されます。削除前に、保持したいデータや情報をダウンロードしてください。
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="font-medium text-gray-900">削除される内容：</h4>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        プロフィール情報とアカウント設定
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        アップロードしたすべての動画
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        チャンネル登録者とコミュニティデータ
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        コメント、いいね、再生リスト
                    </li>
                </ul>
            </div>

            <div class="mt-8">
                <button
                    @click="confirmUserDeletion"
                    class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-medium"
                >
                    アカウントを削除
                </button>
            </div>
        </div>

        <!-- Delete Account Confirmation Modal -->
        <div
            v-if="confirmingUserDeletion"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    アカウントを削除
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        本当にアカウントを削除しますか？削除すると、すべてのリソースとデータが完全に削除されます。この操作を確認するため、パスワードを入力してください。
                                    </p>
                                </div>
                                
                                <div class="mt-4">
                                    <input
                                        ref="passwordInput"
                                        v-model="form.password"
                                        type="password"
                                        placeholder="パスワードを入力"
                                        autocomplete="current-password"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                                        @keyup.enter="deleteUser"
                                    />
                                    <InputError :message="form.errors.password" class="mt-1" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            :disabled="form.processing"
                            @click="deleteUser"
                            :class="[
                                'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white sm:ml-3 sm:w-auto sm:text-sm transition-colors',
                                form.processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'
                            ]"
                        >
                            <span v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                削除中...
                            </span>
                            <span v-else>アカウントを削除</span>
                        </button>
                        
                        <button
                            type="button"
                            @click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                        >
                            キャンセル
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
