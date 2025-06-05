<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">パスワードを更新</h3>
            <p class="text-sm text-gray-600 mt-1">セキュリティを保つため、長くランダムなパスワードを使用してください。</p>
        </div>

        <form @submit.prevent="updatePassword" class="p-6 space-y-6">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    現在のパスワード <span class="text-red-500">*</span>
                </label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="現在のパスワードを入力"
                />
                <InputError :message="form.errors.current_password" class="mt-1" />
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    新しいパスワード <span class="text-red-500">*</span>
                </label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="8文字以上の新しいパスワード"
                />
                <p class="mt-1 text-xs text-gray-500">
                    強力なパスワードには大文字、小文字、数字、記号を含めることをお勧めします。
                </p>
                <InputError :message="form.errors.password" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    パスワード確認 <span class="text-red-500">*</span>
                </label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="新しいパスワードを再入力"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-1" />
            </div>

            <!-- Security Tips -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800 mb-1">パスワードのセキュリティヒント</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• 少なくとも8文字以上使用する</li>
                            <li>• 大文字と小文字を混在させる</li>
                            <li>• 数字と記号を含める</li>
                            <li>• 他のサイトで使用していないパスワードを使用する</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div v-if="form.recentlySuccessful" class="flex items-center text-sm text-green-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        パスワードが更新されました
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="[
                        'px-6 py-2 rounded-md text-sm font-medium text-white transition-colors',
                        form.processing 
                            ? 'bg-gray-400 cursor-not-allowed' 
                            : 'bg-red-600 hover:bg-red-700'
                    ]"
                >
                    <span v-if="form.processing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        更新中...
                    </span>
                    <span v-else>パスワードを更新</span>
                </button>
            </div>
        </form>
    </div>
</template>
