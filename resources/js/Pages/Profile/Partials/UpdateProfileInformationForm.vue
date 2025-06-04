<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    channel_name: props.user.channel_name || '',
    channel_description: props.user.channel_description || '',
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">プロフィール情報</h3>
            <p class="text-sm text-gray-600 mt-1">アカウントのプロフィール情報とメールアドレスを更新してください。</p>
        </div>

        <form @submit.prevent="updateProfileInformation" class="p-6 space-y-6">
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    <!-- Profile Photo File Input -->
                    <input
                        id="photo"
                        ref="photoInput"
                        type="file"
                        class="hidden"
                        @change="updatePhotoPreview"
                    >

                    <!-- Current Profile Photo -->
                    <div v-show="! photoPreview" class="relative">
                        <img :src="user.profile_photo_url" :alt="user.name" class="w-20 h-20 rounded-full object-cover border-4 border-gray-200">
                        <button
                            type="button"
                            @click="selectNewPhoto"
                            class="absolute bottom-0 right-0 w-7 h-7 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- New Profile Photo Preview -->
                    <div v-show="photoPreview" class="relative">
                        <div
                            class="w-20 h-20 rounded-full bg-cover bg-no-repeat bg-center border-4 border-gray-200"
                            :style="'background-image: url(\'' + photoPreview + '\');'"
                        />
                        <button
                            type="button"
                            @click="selectNewPhoto"
                            class="absolute bottom-0 right-0 w-7 h-7 bg-red-600 rounded-full flex items-center justify-center text-white hover:bg-red-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">プロフィール写真</h4>
                    <div class="flex space-x-3">
                        <button
                            type="button"
                            @click="selectNewPhoto"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-md hover:bg-gray-200 transition-colors"
                        >
                            写真を変更
                        </button>
                        <button
                            v-if="user.profile_photo_path"
                            type="button"
                            @click="deletePhoto"
                            class="px-4 py-2 bg-red-100 text-red-700 text-sm rounded-md hover:bg-red-200 transition-colors"
                        >
                            削除
                        </button>
                    </div>
                    <InputError :message="form.errors.photo" class="mt-2" />
                </div>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    名前 <span class="text-red-500">*</span>
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    autocomplete="name"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="あなたの名前を入力"
                />
                <InputError :message="form.errors.name" class="mt-1" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    メールアドレス <span class="text-red-500">*</span>
                </label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="username"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="your@email.com"
                />
                <InputError :message="form.errors.email" class="mt-1" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null" class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">
                        メールアドレスが未認証です。
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-yellow-800 hover:text-yellow-900 font-medium"
                            @click.prevent="sendEmailVerification"
                        >
                            認証メールを再送信する
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 text-sm text-green-600">
                        新しい認証リンクがメールアドレスに送信されました。
                    </div>
                </div>
            </div>

            <!-- Channel Name -->
            <div>
                <label for="channel_name" class="block text-sm font-medium text-gray-700 mb-2">
                    チャンネル名
                </label>
                <input
                    id="channel_name"
                    v-model="form.channel_name"
                    type="text"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    :placeholder="user.name + ' のチャンネル'"
                />
                <p class="mt-1 text-xs text-gray-500">空欄の場合、お名前がチャンネル名として使用されます。</p>
                <InputError :message="form.errors.channel_name" class="mt-1" />
            </div>

            <!-- Channel Description -->
            <div>
                <label for="channel_description" class="block text-sm font-medium text-gray-700 mb-2">
                    チャンネル説明
                </label>
                <textarea
                    id="channel_description"
                    v-model="form.channel_description"
                    rows="4"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 text-sm"
                    placeholder="あなたのチャンネルについて説明してください..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-500">視聴者にあなたのチャンネルについて知ってもらいましょう。</p>
                <InputError :message="form.errors.channel_description" class="mt-1" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div v-if="form.recentlySuccessful" class="flex items-center text-sm text-green-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        保存されました
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
                        保存中...
                    </span>
                    <span v-else>変更を保存</span>
                </button>
            </div>
        </form>
    </div>
</template>
