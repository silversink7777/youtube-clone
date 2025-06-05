<template>
  <div class="min-h-screen bg-white">
    <!-- Header -->
    <AppHeader 
      @toggle-sidebar="toggleSidebar" 
      @search="handleSearch"
    />

    <div class="flex">
      <!-- Overlay for mobile -->
      <div 
        v-if="!sidebarCollapsed" 
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
        @click="sidebarCollapsed = true"
      ></div>
      
      <!-- Sidebar - Hidden on video page for better experience -->
      <AppSidebar :isCollapsed="true" />

      <!-- Main Content -->
      <main class="flex-1" style="margin-left: 80px;">
        <div class="max-w-[1754px] mx-auto flex gap-6 p-6">
          <!-- Primary Column (Video + Info) -->
          <div class="flex-1 max-w-[1280px]">
            <!-- Video Player -->
            <div class="bg-black rounded-lg overflow-hidden mb-4 relative" style="aspect-ratio: 16/9;">
              <video 
                ref="videoPlayer"
                :src="getVideoUrl"
                class="w-full h-full"
                controls
                autoplay
                crossorigin="anonymous"
                @loadedmetadata="onVideoLoaded"
                @error="onVideoError"
                @loadstart="onVideoLoadStart"
              >
                お使いのブラウザは動画の再生をサポートしていません。
              </video>
              
              <!-- ローディング状態 -->
              <div 
                v-if="isVideoLoading && !videoError" 
                class="absolute inset-0 bg-black flex items-center justify-center"
              >
                <svg class="w-12 h-12 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </div>
              
              <!-- エラー状態 -->
              <div 
                v-if="videoError" 
                class="absolute inset-0 bg-gray-800 flex flex-col items-center justify-center text-white"
              >
                <svg class="w-16 h-16 mb-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
                <span class="text-lg">{{ videoErrorMessage }}</span>
                <button 
                  @click="retryVideo"
                  class="mt-4 px-4 py-2 bg-white text-black rounded hover:bg-gray-200 transition-colors"
                >
                  再試行
                </button>
              </div>
            </div>

            <!-- Video Title -->
            <h1 class="text-xl font-semibold text-black mb-3 leading-7">
              {{ video.title }}
            </h1>
            
            <!-- Channel Info & Actions Row -->
            <div class="flex items-center justify-between mb-4">
              <!-- Left: Channel Info -->
              <div class="flex items-center space-x-3">
                <!-- Channel avatar -->
                <div :class="['w-10 h-10 rounded-full flex-shrink-0', video.channelColor || 'bg-gray-400']">
                  <img 
                    v-if="video.channelAvatar"
                    :src="video.channelAvatar"
                    :alt="video.channelName"
                    class="w-full h-full rounded-full object-cover"
                  >
                  <div 
                    v-else
                    class="w-full h-full rounded-full flex items-center justify-center text-white text-sm font-medium"
                  >
                    {{ video.channelInitial }}
                  </div>
                </div>
                
                <div class="flex flex-col">
                  <h3 class="font-medium text-black text-sm">{{ video.channelName }}</h3>
                  <p class="text-gray-600 text-xs">
                    {{ formatViews(video.views) }} 回視聴 • {{ formatTimeAgo(video.publishedAt) }}
                  </p>
                </div>

                <!-- Subscribe Button -->
                <button class="ml-6 px-6 py-2 bg-black text-white rounded-full text-sm font-medium hover:bg-gray-800 transition-colors">
                  チャンネル登録
                </button>
              </div>

              <!-- Right: Action buttons -->
              <div class="flex items-center space-x-2">
                <!-- Like/Dislike Button Group -->
                <div class="flex items-center bg-gray-100 rounded-full overflow-hidden">
                  <button class="flex items-center space-x-2 px-4 py-2 hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                    </svg>
                    <span class="text-sm font-medium">高評価</span>
                  </button>
                  <div class="w-px h-6 bg-gray-300"></div>
                  <button class="px-4 py-2 hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13v-9m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                    </svg>
                  </button>
                </div>
                
                <!-- Share Button -->
                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                  </svg>
                  <span class="text-sm font-medium">共有</span>
                </button>
                
                <!-- Clip Button -->
                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V3a1 1 0 011 1v10.586A2 2 0 0116.586 16H7.414A2 2 0 016 14.586V4a1 1 0 011-1z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V9a1 1 0 00-1-1H8a1 1 0 00-1 1v1"></path>
                  </svg>
                  <span class="text-sm font-medium">クリップ</span>
                </button>

                <!-- Save Button -->
                <button class="flex items-center space-x-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                  </svg>
                  <span class="text-sm font-medium">保存</span>
                </button>

                <!-- More Options -->
                <button class="flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Description Section -->
            <div v-if="video.description" class="bg-gray-100 rounded-lg p-3 mb-6">
              <div class="text-sm">
                <p class="font-medium text-black mb-1">
                  {{ formatViews(video.views) }} 回視聴 {{ formatTimeAgo(video.publishedAt) }}
                </p>
                <div class="text-gray-800 leading-relaxed whitespace-pre-line">
                  {{ truncatedDescription }}
                  <button 
                    v-if="video.description.length > 150"
                    @click="toggleDescription"
                    class="text-black font-medium ml-1 hover:underline"
                  >
                    {{ showFullDescription ? '簡潔に表示' : '...さらに表示' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-8">
              <div class="flex items-center space-x-4 mb-6">
                <h3 class="text-xl font-semibold text-black">
                  コメント ({{ comments.length }})
                </h3>
                <button class="flex items-center space-x-1 text-sm text-gray-600 hover:text-black">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                  </svg>
                  <span>並び替え</span>
                </button>
              </div>
              

              <!-- Add Comment -->
              <div v-if="authUser" class="flex space-x-3 mb-6">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                  {{ authUser.name.charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1">
                  <form @submit.prevent="submitComment">
                    <textarea
                      v-model="newComment"
                      placeholder="コメントを追加..."
                      class="w-full bg-transparent border-0 border-b border-gray-300 focus:border-black focus:ring-0 text-sm py-2 min-h-[36px] max-h-[100px] resize-none"
                      rows="1"
                      @focus="showCommentActions = true"
                      @input="autoResize"
                    ></textarea>
                    <div v-if="showCommentActions" class="flex justify-end space-x-2 mt-2">
                      <button
                        type="button"
                        @click="cancelComment"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-black"
                      >
                        キャンセル
                      </button>
                      <button
                        type="submit"
                        :disabled="!newComment.trim() || submittingComment"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {{ submittingComment ? '投稿中...' : 'コメント' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Comments Loading -->
              <div v-if="loadingComments" class="text-center py-8">
                <svg class="w-8 h-8 text-gray-400 animate-spin mx-auto" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm text-gray-500 mt-2">コメントを読み込み中...</p>
              </div>

              <!-- Comments List -->
              <div v-else-if="!loadingComments && comments && comments.length > 0" class="space-y-4">
                <div v-for="comment in comments" :key="comment.id" class="comment-item">
                  <!-- Main Comment -->
                  <div class="flex space-x-3">
                    <div :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium flex-shrink-0',
                      comment.is_deleted ? 'bg-gray-400' : 'bg-blue-500'
                    ]">
                      {{ comment.user.channel_initial || comment.user.name.charAt(0).toUpperCase() }}
                    </div>
                    
                    <div class="flex-1">
                      <div class="flex items-center space-x-2 mb-1">
                        <span :class="[
                          'font-medium text-sm',
                          comment.is_deleted ? 'text-gray-500' : 'text-black'
                        ]">{{ comment.user.channel_name || comment.user.name }}</span>
                        <span v-if="comment.is_pinned && !comment.is_deleted" class="bg-red-600 text-white text-xs px-2 py-1 rounded font-medium">
                          ピン留め
                        </span>
                        <span class="text-xs text-gray-600">{{ comment.time_ago }}</span>
                      </div>
                      
                      <p :class="[
                        'text-sm mb-2 whitespace-pre-wrap',
                        comment.is_deleted ? 'text-gray-500 italic' : 'text-gray-800'
                      ]">{{ comment.content }}</p>
                      
                      <!-- アクションボタン（削除されたコメントには表示しない） -->
                      <div v-if="!comment.is_deleted" class="flex items-center space-x-4">
                        <!-- Like button -->
                        <button 
                          @click="toggleLike(comment)"
                          :class="[
                            'flex items-center space-x-1 transition-colors',
                            comment.is_liked ? 'text-blue-600' : 'text-gray-600 hover:text-black'
                          ]"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                          </svg>
                          <span v-if="comment.likes_count > 0" class="text-xs">{{ comment.likes_count }}</span>
                        </button>
                        
                        <!-- Dislike button -->
                        <button 
                          @click="toggleDislike(comment)"
                          :class="[
                            'flex items-center space-x-1 transition-colors',
                            comment.is_disliked ? 'text-red-600' : 'text-gray-600 hover:text-black'
                          ]"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13v-9m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                          </svg>
                          <span v-if="comment.dislikes_count > 0" class="text-xs">{{ comment.dislikes_count }}</span>
                        </button>
                        
                        <!-- Reply button -->
                        <button 
                          v-if="authUser"
                          @click="toggleReplyForm(comment.id)"
                          class="text-xs text-gray-600 hover:text-black font-medium"
                        >
                          返信
                        </button>

                        <!-- Pin/Unpin button (video owner only) -->
                        <button 
                          v-if="canPinComment(comment)"
                          @click="togglePin(comment)"
                          class="text-xs text-gray-600 hover:text-black font-medium"
                        >
                          {{ comment.is_pinned ? 'ピン留め解除' : 'ピン留め' }}
                        </button>

                        <!-- Delete button -->
                        <button 
                          v-if="comment.can_delete"
                          @click="deleteComment(comment)"
                          class="text-xs text-red-600 hover:text-red-800 font-medium"
                        >
                          削除
                        </button>
                      </div>

                      <!-- Reply Form -->
                      <div v-if="replyingTo === comment.id" class="mt-3 ml-3">
                        <form @submit.prevent="submitReply(comment.id)">
                          <div class="flex space-x-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium flex-shrink-0">
                              {{ authUser.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="flex-1">
                              <textarea
                                v-model="replyContent"
                                :placeholder="`@${comment.user.channel_name || comment.user.name} に返信`"
                                class="w-full bg-transparent border-0 border-b border-gray-300 focus:border-black focus:ring-0 text-sm py-2 min-h-[36px] max-h-[100px] resize-none"
                                rows="1"
                                @input="autoResize"
                              ></textarea>
                              <div class="flex justify-end space-x-2 mt-2">
                                <button
                                  type="button"
                                  @click="cancelReply"
                                  class="px-4 py-2 text-sm text-gray-600 hover:text-black"
                                >
                                  キャンセル
                                </button>
                                <button
                                  type="submit"
                                  :disabled="!replyContent.trim() || submittingReply"
                                  class="px-4 py-2 bg-blue-600 text-white text-sm rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                  {{ submittingReply ? '投稿中...' : '返信' }}
                                </button>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>

                      <!-- Replies -->
                      <div v-if="comment.replies && comment.replies.length > 0" class="mt-4 ml-8 space-y-3">
                        <div v-for="reply in comment.replies" :key="reply.id" class="flex space-x-3">
                          <div :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-medium flex-shrink-0',
                            reply.is_deleted ? 'bg-gray-400' : 'bg-green-500'
                          ]">
                            {{ reply.user.channel_initial || reply.user.name.charAt(0).toUpperCase() }}
                          </div>
                          
                          <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                              <span :class="[
                                'font-medium text-sm',
                                reply.is_deleted ? 'text-gray-500' : 'text-black'
                              ]">{{ reply.user.channel_name || reply.user.name }}</span>
                              <span class="text-xs text-gray-600">{{ reply.time_ago }}</span>
                            </div>
                            
                            <p :class="[
                              'text-sm mb-2 whitespace-pre-wrap',
                              reply.is_deleted ? 'text-gray-500 italic' : 'text-gray-800'
                            ]">{{ reply.content }}</p>
                            
                            <!-- 返信のアクションボタン（削除されたコメントには表示しない） -->
                            <div v-if="!reply.is_deleted" class="flex items-center space-x-4">
                              <!-- Like button for reply -->
                              <button 
                                @click="toggleLike(reply)"
                                :class="[
                                  'flex items-center space-x-1 transition-colors',
                                  reply.is_liked ? 'text-blue-600' : 'text-gray-600 hover:text-black'
                                ]"
                              >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.60L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                </svg>
                                <span v-if="reply.likes_count > 0" class="text-xs">{{ reply.likes_count }}</span>
                              </button>
                              
                              <!-- Dislike button for reply -->
                              <button 
                                @click="toggleDislike(reply)"
                                :class="[
                                  'flex items-center space-x-1 transition-colors',
                                  reply.is_disliked ? 'text-red-600' : 'text-gray-600 hover:text-black'
                                ]"
                              >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.60L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0 .714.211-1.412.608-2.006L17 13v-9m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                                </svg>
                                <span v-if="reply.dislikes_count > 0" class="text-xs">{{ reply.dislikes_count }}</span>
                              </button>

                              <!-- Delete button for reply -->
                              <button 
                                v-if="reply.can_delete"
                                @click="deleteComment(reply)"
                                class="text-xs text-red-600 hover:text-red-800 font-medium"
                              >
                                削除
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- No Comments -->
              <div v-else-if="!loadingComments && (!comments || comments.length === 0)" class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="text-sm">まだコメントはありません</p>
                <p v-if="!authUser" class="text-xs mt-1">
                  <Link href="/login" class="text-blue-600 hover:underline">ログイン</Link>してコメントを投稿しましょう
                </p>
              </div>
            </div>
          </div>

          <!-- Secondary Column (Related Videos) -->
          <div class="w-[426px] flex-shrink-0">
            <div class="space-y-2">
              <!-- Filter Tabs -->
              <div class="flex space-x-2 mb-4">
                <button class="px-3 py-1 bg-black text-white rounded-lg text-sm font-medium">
                  すべて
                </button>
                <button class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-black rounded-lg text-sm font-medium">
                  このチャンネルから
                </button>
              </div>

              <h3 class="font-semibold text-black mb-4">関連動画</h3>
              
              <!-- Related Videos Placeholder -->
              <div class="space-y-3">
                <div v-for="i in 8" :key="i" class="flex space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                  <div class="w-40 h-24 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M8 5v14l11-7z"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-black line-clamp-2 mb-1">
                      関連動画のタイトル {{ i }}
                    </h4>
                    <p class="text-xs text-gray-600 mb-1">チャンネル名</p>
                    <p class="text-xs text-gray-600">
                      {{ Math.floor(Math.random() * 1000) }}万 回視聴 • {{ Math.floor(Math.random() * 30) + 1 }}日前
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import AppHeader from '../../Components/YouTube/AppHeader.vue'
import AppSidebar from '../../Components/YouTube/AppSidebar.vue'

// Props from backend
const props = defineProps({
  video: {
    type: Object,
    required: true
  }
})

const page = usePage()
const sidebarCollapsed = ref(true) // Always collapsed on video page
const videoPlayer = ref(null)
const showFullDescription = ref(false)

// 動画プレイヤー関連のリアクティブデータ
const isVideoLoading = ref(true)
const videoError = ref(false)
const videoErrorMessage = ref('動画を読み込めません')
const useDirectUrl = ref(false) // プロキシが失敗した場合の直接URL使用フラグ

// 外部URLかどうかを判定
const isExternalUrl = computed(() => {
  return props.video.video_url && (
    props.video.video_url.startsWith('http://') || 
    props.video.video_url.startsWith('https://')
  )
})

// 動画のURLを取得（外部URLの場合はプロキシ経由、失敗時は直接URL）
const getVideoUrl = computed(() => {
  if (isExternalUrl.value && !useDirectUrl.value) {
    return `/video-proxy?url=${encodeURIComponent(props.video.video_url)}`
  }
  return props.video.video_url
})

// コメント関連のリアクティブデータ
const comments = ref([])
const loadingComments = ref(false)
const newComment = ref('')
const showCommentActions = ref(false)
const submittingComment = ref(false)
const replyingTo = ref(null)
const replyContent = ref('')
const submittingReply = ref(false)

const toggleSidebar = () => {
  // Sidebar stays collapsed on video page for better viewing experience
  sidebarCollapsed.value = true
}

const handleSearch = (query) => {
  router.get('/', { search: query })
}

const toggleDescription = () => {
  showFullDescription.value = !showFullDescription.value
}

const truncatedDescription = computed(() => {
  if (!props.video.description) return ''
  
  if (showFullDescription.value || props.video.description.length <= 150) {
    return props.video.description
  }
  
  return props.video.description.substring(0, 150)
})

// 認証ユーザー情報のcomputed property
const authUser = computed(() => page.props.auth?.user || null)

// コメント機能のメソッド
const getCSRFToken = () => {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!token) {
    console.error('CSRF token not found');
    throw new Error('CSRF token not found');
  }
  return token;
};

const loadComments = async () => {
  loadingComments.value = true
  try {
    console.log('コメント読み込み開始:', props.video.id);
    const response = await fetch(`/api/videos/${props.video.id}/comments`)
    const data = await response.json()
    console.log('コメントデータ受信:', data);
    comments.value = data.comments || []
    console.log('設定されたコメント配列:', comments.value);
  } catch (error) {
    console.error('コメントの読み込みに失敗しました:', error)
  } finally {
    loadingComments.value = false
  }
}

const submitComment = async () => {
  if (!newComment.value.trim() || submittingComment.value) return
  
  submittingComment.value = true
  try {
    const csrfToken = getCSRFToken();
    console.log('コメント投稿開始:', newComment.value);
    const response = await fetch(`/api/videos/${props.video.id}/comments`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        content: newComment.value
      })
    })

    if (response.ok) {
      const data = await response.json()
      console.log('投稿されたコメント:', data.comment);
      comments.value.unshift(data.comment) // 新しいコメントを先頭に追加
      console.log('更新後のコメント配列:', comments.value);
      newComment.value = ''
      showCommentActions.value = false
    } else {
      let errorMessage = 'コメントの投稿に失敗しました';
      try {
        const errorData = await response.json()
        errorMessage = errorData.error || errorData.message || errorMessage;
        console.error('コメント投稿エラー詳細:', errorData);
      } catch (parseError) {
        console.error('エラーレスポンスのパースに失敗:', parseError);
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('コメント投稿エラー:', error)
    alert('コメントの投稿に失敗しました: ' + error.message)
  } finally {
    submittingComment.value = false
  }
}

const submitReply = async (parentId) => {
  if (!replyContent.value.trim() || submittingReply.value) return
  
  submittingReply.value = true
  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/videos/${props.video.id}/comments`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        content: replyContent.value,
        parent_id: parentId
      })
    })

    if (response.ok) {
      const data = await response.json()
      // 親コメントの返信リストに追加
      const parentComment = comments.value.find(c => c.id === parentId)
      if (parentComment) {
        if (!parentComment.replies) {
          parentComment.replies = []
        }
        parentComment.replies.push(data.comment)
      }
      replyContent.value = ''
      replyingTo.value = null
    } else {
      let errorMessage = '返信の投稿に失敗しました';
      try {
        const errorData = await response.json()
        errorMessage = errorData.error || errorData.message || errorMessage;
        console.error('返信投稿エラー詳細:', errorData);
      } catch (parseError) {
        console.error('エラーレスポンスのパースに失敗:', parseError);
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('返信投稿エラー:', error)
    alert('返信の投稿に失敗しました: ' + error.message)
  } finally {
    submittingReply.value = false
  }
}

const deleteComment = async (comment) => {
  if (!confirm('このコメントを削除しますか？')) return
  
  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/videos/${props.video.id}/comments/${comment.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      if (comment.parent_id) {
        // 返信の場合
        const parentComment = comments.value.find(c => c.id === comment.parent_id)
        if (parentComment && parentComment.replies) {
          parentComment.replies = parentComment.replies.filter(r => r.id !== comment.id)
        }
      } else {
        // トップレベルコメントの場合
        comments.value = comments.value.filter(c => c.id !== comment.id)
      }
    } else {
      let errorMessage = 'コメントの削除に失敗しました';
      try {
        const errorData = await response.json()
        errorMessage = errorData.error || errorData.message || errorMessage;
      } catch (parseError) {
        console.error('エラーレスポンスのパースに失敗:', parseError);
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('コメント削除エラー:', error)
    alert('コメントの削除に失敗しました: ' + error.message)
  }
}

const togglePin = async (comment) => {
  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/videos/${props.video.id}/comments/${comment.id}/pin`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      // コメントのピン留め状態を更新
      const commentIndex = comments.value.findIndex(c => c.id === comment.id)
      if (commentIndex !== -1) {
        comments.value[commentIndex].is_pinned = data.comment.is_pinned
        // ピン留めされたコメントを先頭に移動
        if (data.comment.is_pinned) {
          const pinnedComment = comments.value.splice(commentIndex, 1)[0]
          comments.value.unshift(pinnedComment)
        }
      }
    } else {
      let errorMessage = 'ピン留めの変更に失敗しました';
      try {
        const errorData = await response.json()
        errorMessage = errorData.error || errorData.message || errorMessage;
      } catch (parseError) {
        console.error('エラーレスポンスのパースに失敗:', parseError);
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('ピン留めエラー:', error)
    alert('ピン留めの変更に失敗しました: ' + error.message)
  }
}

const toggleReplyForm = (commentId) => {
  if (replyingTo.value === commentId) {
    replyingTo.value = null
    replyContent.value = ''
  } else {
    replyingTo.value = commentId
    replyContent.value = ''
  }
}

const cancelComment = () => {
  newComment.value = ''
  showCommentActions.value = false
}

const cancelReply = () => {
  replyingTo.value = null
  replyContent.value = ''
}

const canPinComment = (comment) => {
  // 動画の投稿者かつトップレベルコメントの場合のみピン留め可能
  return props.video.user_id === parseInt(authUser.value?.id) && !comment.parent_id
}

const autoResize = (event) => {
  const textarea = event.target
  textarea.style.height = 'auto'
  textarea.style.height = `${Math.min(textarea.scrollHeight, 100)}px`
}

const onVideoLoadStart = () => {
  isVideoLoading.value = true
  videoError.value = false
}

const onVideoLoaded = () => {
  console.log('Video loaded successfully')
  isVideoLoading.value = false
  videoError.value = false
}

const onVideoError = async (event) => {
  console.error('動画読み込みエラー:', event)
  
  // 外部URLでプロキシを使用していて、まだ直接URLを試していない場合
  if (isExternalUrl.value && !useDirectUrl.value) {
    console.log('プロキシでの読み込みに失敗。直接URLを試行します...')
    useDirectUrl.value = true
    
    // 少し待ってから動画要素のsrcを更新
    await nextTick()
    if (videoPlayer.value) {
      videoPlayer.value.load() // 動画を再読み込み
    }
    return
  }
  
  // エラー処理
  videoError.value = true
  isVideoLoading.value = false
  
  // エラーの詳細を取得
  if (event && event.target && event.target.error) {
    const error = event.target.error
    console.error('動画読み込みエラー詳細:', error)
    
    switch (error.code) {
      case error.MEDIA_ERR_ABORTED:
        videoErrorMessage.value = '動画の読み込みが中断されました'
        break
      case error.MEDIA_ERR_NETWORK:
        videoErrorMessage.value = 'ネットワークエラーが発生しました'
        break
      case error.MEDIA_ERR_DECODE:
        videoErrorMessage.value = '動画形式がサポートされていません'
        break
      case error.MEDIA_ERR_SRC_NOT_SUPPORTED:
        videoErrorMessage.value = isExternalUrl.value ? '外部動画にアクセスできません' : '動画形式がサポートされていません'
        break
      default:
        videoErrorMessage.value = '動画を読み込めません'
    }
  } else {
    videoErrorMessage.value = isExternalUrl.value ? '外部動画にアクセスできません' : '動画を読み込めません'
  }
}

const retryVideo = () => {
  videoError.value = false
  isVideoLoading.value = true
  useDirectUrl.value = false // プロキシから再試行
  
  if (videoPlayer.value) {
    videoPlayer.value.load()
  }
}

const formatViews = (views) => {
  if (views >= 10000) {
    return `${(views / 10000).toFixed(1)}万`
  }
  return views.toLocaleString()
}

const formatTimeAgo = (publishedAt) => {
  const now = new Date()
  const published = new Date(publishedAt)
  const diffInHours = Math.floor((now - published) / (1000 * 60 * 60))
  
  if (diffInHours < 24) {
    return `${diffInHours} 時間前`
  }
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 30) {
    return `${diffInDays} 日前`
  }
  
  const diffInMonths = Math.floor(diffInDays / 30)
  if (diffInMonths < 12) {
    return `${diffInMonths} か月前`
  }
  
  const diffInYears = Math.floor(diffInMonths / 12)
  return `${diffInYears} 年前`
}

const toggleLike = async (comment) => {
  if (!authUser.value) {
    alert('いいねするにはログインが必要です');
    return;
  }

  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/videos/${props.video.id}/comments/${comment.id}/like`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      
      // コメントのいいね状態を更新
      const updateComment = (comments, commentId) => {
        for (let i = 0; i < comments.length; i++) {
          if (comments[i].id === commentId) {
            comments[i].is_liked = data.is_liked
            comments[i].likes_count = data.likes_count
            comments[i].is_disliked = data.is_disliked
            comments[i].dislikes_count = data.dislikes_count
            return true
          }
          // 返信コメントもチェック
          if (comments[i].replies && comments[i].replies.length > 0) {
            if (updateComment(comments[i].replies, commentId)) {
              return true
            }
          }
        }
        return false
      }
      
      updateComment(comments.value, comment.id)
      
    } else {
      let errorMessage = 'いいね処理に失敗しました';
      try {
        const errorData = await response.json()
        errorMessage = errorData.error || errorData.message || errorMessage;
      } catch (parseError) {
        console.error('エラーレスポンスのパースに失敗:', parseError);
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('いいねエラー:', error)
    alert('いいね処理に失敗しました: ' + error.message)
  }
}

const toggleDislike = async (comment) => {
  if (!authUser.value) {
    alert('きらいするにはログインが必要です');
    return;
  }

  try {
    const csrfToken = getCSRFToken();
    const response = await fetch(`/api/videos/${props.video.id}/comments/${comment.id}/dislike`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      
      // コメントのきらい状態を更新
      const updateComment = (comments, commentId) => {
        for (let i = 0; i < comments.length; i++) {
          if (comments[i].id === commentId) {
            comments[i].is_liked = data.is_liked
            comments[i].likes_count = data.likes_count
            comments[i].is_disliked = data.is_disliked
            comments[i].dislikes_count = data.dislikes_count
            return true
          }
          // 返信コメントもチェック
          if (comments[i].replies && comments[i].replies.length > 0) {
            if (updateComment(comments[i].replies, commentId)) {
              return true
            }
          }
        }
        return false
      }
      
      updateComment(comments.value, comment.id)
      
    } else {
      let errorMessage = 'きらい処理に失敗しました';
      try {
        const errorData = await response.json()
        errorMessage = errorData.error || errorData.message || errorMessage;
      } catch (parseError) {
        console.error('エラーレスポンスのパースに失敗:', parseError);
      }
      alert(errorMessage)
    }
  } catch (error) {
    console.error('きらいエラー:', error)
    alert('きらい処理に失敗しました: ' + error.message)
  }
}

// コンポーネントマウント時にコメントを読み込み
onMounted(() => {
  loadComments()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style> 