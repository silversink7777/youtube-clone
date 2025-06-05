<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'video' => 'required|file|mimes:mp4,avi,mov,wmv,webm|max:1048576', // 1GB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'category' => 'required|string|in:gaming,music,education,entertainment,sports,technology,news,lifestyle,comedy,food',
            'status' => 'required|string|in:published,private,unlisted',
            'tags' => 'nullable|string|max:500'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'description.max' => '説明は5000文字以内で入力してください。',
            'video.required' => '動画ファイルをアップロードしてください。',
            'video.mimes' => '動画ファイルはmp4, avi, mov, wmv, webm形式のみ対応しています。',
            'video.max' => '動画ファイルは1GB以下にしてください。',
            'thumbnail.image' => 'サムネイルは画像ファイルを選択してください。',
            'thumbnail.mimes' => 'サムネイルはjpeg, png, jpg, gif形式のみ対応しています。',
            'thumbnail.max' => 'サムネイルは10MB以下にしてください。',
            'category.required' => 'カテゴリを選択してください。',
            'category.in' => '無効なカテゴリが選択されています。',
            'status.required' => '公開設定を選択してください。',
            'status.in' => '無効な公開設定が選択されています。',
            'tags.max' => 'タグは500文字以内で入力してください。'
        ];
    }
} 