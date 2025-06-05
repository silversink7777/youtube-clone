# YouTube Clone

Laravel、Inertia.js、Vue.jsを使用したYouTubeクローンアプリケーション

## 📋 概要

このプロジェクトは、YouTubeの基本機能を再現したWebアプリケーションです。動画のアップロード、視聴、管理機能を提供します。

## 🚀 技術スタック

- **バックエンド**: Laravel 12 Jetstream
- **フロントエンド**: Vue.js 3 + Inertia.js
- **データベース**: MySQL
- **スタイリング**: Tailwind CSS
- **ファイルストレージ**: Laravel Storage
- **認証**: Laravel Jetstream

## ✨ 主な機能

### 動画機能
- 📁 動画アップロード（MP4形式）
- 🎬 動画一覧表示
- ▶️ 動画詳細ページでの再生
- 🖼️ サムネイル自動生成
- 🔍 動画検索機能
- 📂 カテゴリ別動画表示

### 外部動画対応
- 🌐 外部URL動画の再生サポート
- 🔄 CORS対応のプロキシサーバー
- 🛡️ セキュリティを考慮した許可ドメイン制限

### ユーザー機能
- 👤 ユーザー登録・ログイン
- 📊 動画管理ダッシュボード
- ✏️ 動画情報の編集
- 🗑️ 動画削除

### UI/UX
- 📱 レスポンシブデザイン
- 🎯 YouTubeライクなインターフェース
- ⚡ ホバー時の動画プレビュー
- 💫 ローディングアニメーション
- 🚫 エラーハンドリングとフォールバック表示

## 📦 インストール

### 必要な環境
- PHP 8.1以上
- Composer
- Node.js 16以上
- MySQL 8.0以上

### セットアップ手順

1. **リポジトリのクローン**
```bash
git clone https://github.com/silversink7777/youtube-clone
cd youtube-clone
```

2. **依存関係のインストール**
```bash
composer install
npm install
```

3. **環境設定**
```bash
cp .env.example .env
php artisan key:generate
```

4. **データベース設定**
`.env`ファイルを編集してデータベース接続情報を設定：
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youtube_clone
DB_USERNAME=root
DB_PASSWORD=
```

5. **アプリケーションURL設定**
```env
APP_URL=http://127.0.0.1:8000
```

6. **データベースマイグレーション**
```bash
php artisan migrate
```

7. **ストレージリンクの作成**
```bash
php artisan storage:link
```

8. **アセットのビルド**
```bash
npm run dev
```

9. **開発サーバーの起動**
```bash
php artisan serve
```

アプリケーションは `http://127.0.0.1:8000` でアクセスできます。

## 🎯 使用方法

### 動画のアップロード
1. アカウントを作成してログイン
2. 「動画をアップロード」ボタンをクリック
3. MP4ファイルを選択してアップロード
4. タイトル、説明、カテゴリを設定
5. 「公開」をクリックして動画を公開

### 外部動画の追加
1. 管理画面で「外部動画を追加」を選択
2. 動画のURL（HTTP/HTTPS）を入力
3. メタデータを設定して保存

### 動画の視聴
- ホームページで動画一覧を閲覧
- 動画カードをクリックして詳細ページに移動
- ホバー時の自動プレビューでクイック視聴

## 🛠️ トラブルシューティング

### 動画が表示されない場合

**ローカル動画の問題**
```bash
# ストレージリンクの再作成
php artisan storage:link

# 設定キャッシュのクリア
php artisan config:clear
php artisan cache:clear
```

**外部動画の問題**
- 許可ドメインリストの確認
- CORS設定の確認
- プロキシ機能のログ確認

### 開発時の注意点

**APP_URLの設定**
開発サーバーのURLと一致するように設定してください：
```env
APP_URL=http://127.0.0.1:8000
```

**ファイルアップロード**
`storage/app/public`ディレクトリの権限を確認：
```bash
chmod -R 755 storage/app/public
```

## 📁 プロジェクト構造

```
youtube-clone/
├── app/
│   ├── Http/Controllers/
│   │   └── VideoController.php      # 動画関連の制御
│   │   └── VideoController.php      # 動画関連の制御
│   ├── Models/
│   │   └── Video.php               # 動画モデル
│   ├── Repositories/
│   │   └── VideoRepository.php     # データアクセス層
│   └── Services/
│       └── VideoUploadService.php  # 動画アップロード処理
├── resources/
│   └── js/
│       ├── Components/
│       │   └── YouTube/
│       │       ├── VideoCard.vue   # 動画カードコンポーネント
│       │       └── ...
│       └── Pages/
│           ├── YouTube.vue         # ホームページ
│           └── Watch/
│               └── Show.vue        # 動画詳細ページ
└── storage/
    └── app/
        └── public/
            ├── videos/             # アップロード動画
            └── thumbnails/         # サムネイル画像
```

## 🔧 開発用コマンド

```bash
# 開発サーバー起動
php artisan serve

# アセット監視
npm run dev

# プロダクションビルド
npm run build

# テスト実行
php artisan test

# コードフォーマット
./vendor/bin/pint
```

## 🐛 既知の問題

- 大容量動画ファイルのアップロード時間制限
- 一部ブラウザでの自動再生制限
- モバイルデバイスでの動画プレビュー性能

## 📄 ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 🤝 コントリビューション

プルリクエストやイシューの報告を歓迎します。開発に参加する際は、コーディング規約に従ってください。

## 📞 サポート

問題やバグを発見した場合は、GitHubのIssuesページで報告してください。
