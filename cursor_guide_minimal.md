# Cursor 開発ガイド（ミニマム版）

## プロジェクト概要

```
【プロジェクト】格闘家投票アプリケーション
【スタック】Laravel 11 + MySQL + PHP 8.1+ + Blade

【DB スキーマ】
- fighters: id, name, created_at, updated_at
- votes: id, fighter_id, vote_type(strong/weak), ip_address, created_at
- comments: id, fighter_id, content, ip_address, created_at

【主な機能】
1. 投票機能: 格闘家に「強い」「弱い」を投票
2. 統計表示: 強い/弱いの集計表示
3. コメント: 匿名コメント投稿

【ホスティング】Mixhost
```

---

## 実装タスク（セッション別）

### セッション 1: マイグレーション

```
【指示】
以下の 3 つのテーブルのマイグレーションを作成してください。

1. fighters テーブル
   - id, name(UNIQUE), image_url(NULL), timestamps

2. votes テーブル
   - id, fighter_id(FK), vote_type(ENUM: strong/weak), ip_address, user_agent, timestamps
   - インデックス: fighter_id, vote_type, (fighter_id, vote_type)

3. comments テーブル
   - id, fighter_id(FK), content(TEXT), ip_address, user_agent, timestamps
   - インデックス: fighter_id, created_at

【出力形式】
database/migrations/ 配下の PHP ファイル
```

実行後確認:
```bash
php artisan migrate
```

---

### セッション 2: モデル・Service

```
【指示】
以下を実装してください。

1. app/Models/Fighter.php
   - votes(), comments() リレーション
   - getVoteStats() メソッド（strong/weak 集計、%計算）

2. app/Models/Vote.php
   - fighter() リレーション
   - scopeByType($query, $type)

3. app/Models/Comment.php
   - fighter() リレーション
   - boot(): HTML エスケープ処理

4. app/Services/VoteService.php
   - vote($fighter, $voteType, $ipAddress)
   - canVote($fighter, $ipAddress, $minutes=1)

5. app/Services/CommentService.php
   - postComment($fighterId, $content, $ipAddress)
   - canPostComment($ipAddress, $minutes=1)
```

確認:
```bash
php artisan tinker
>>> \App\Models\Fighter::first()?->getVoteStats()
```

---

### セッション 3: コントローラー・リクエスト

```
【指示】
以下を実装してください。

1. app/Http/Controllers/VoteController.php
   - index(): ランダムに 2 人の格闘家を表示
   - show($id): 統計とコメント表示
   - store(): 投票を記録（JSON レスポンス）
   - storeComment(): コメント投稿（JSON レスポンス）

2. app/Http/Requests/StoreVoteRequest.php
   - fighter_id: required|integer|exists:fighters,id
   - vote_type: required|in:strong,weak

3. app/Http/Requests/StoreCommentRequest.php
   - fighter_id: required|integer|exists:fighters,id
   - content: required|string|min:3|max:500

【JSON レスポンス例】
- 成功: { "success": true, "message": "...", "data": {...} }
- エラー: { "success": false, "message": "..." }
```

---

### セッション 4: ルート定義

```
【指示】
routes/web.php に以下のルートを定義してください。

Web ルート:
- GET  /                      → VoteController@index
- GET  /fighter/{id}          → VoteController@show

API ルート:
- POST /api/votes             → VoteController@store
- POST /api/comments          → VoteController@storeComment

【要件】
- CSRF 保護有効
- JSON レスポンス形式
```

確認:
```bash
php artisan route:list
```

---

### セッション 5: ビュー実装

```
【指示】
以下の Blade テンプレートを作成してください。

1. resources/views/layouts/app.blade.php
   - ベースレイアウト
   - CSRF トークン meta タグ
   - Tailwind CSS または Bootstrap 読み込み

2. resources/views/vote/index.blade.php
   - 左右に 2 人の格闘家表示
   - 左に「強い」ボタン（AJAX）
   - 右に「弱い」ボタン（AJAX）
   - 中央に「次へ」ボタン
   - 詳細ページへのリンク

3. resources/views/vote/show.blade.php
   - 格闘家名
   - 投票統計（棒グラフ）
   - コメント一覧（最新 50 件）
   - コメント投稿フォーム（AJAX）

【要件】
- モバイルレスポンシブ
- Alpine.js または Vanilla JS
```

確認:
```bash
php artisan serve
# http://localhost:8000 にアクセス
```

---

### セッション 6: テスト（オプション）

```
【指示】
tests/Feature/VoteTest.php を作成してください。

テストケース:
1. test_vote_is_recorded: 投票が記録される
2. test_vote_stats: 統計が正しく計算される
3. test_invalid_fighter_error: 不正な fighter_id でエラー
4. test_comment_posted: コメントが投稿される
5. test_spam_prevention: 同一IP 1分以内の重複投稿が拒否される
```

実行:
```bash
php artisan test
```

---

## よく使うコマンド

```bash
# マイグレーション
php artisan migrate
php artisan migrate:rollback
php artisan tinker

# ルート確認
php artisan route:list

# キャッシュ
php artisan config:cache
php artisan view:cache

# テスト
php artisan test

# サーバー起動
php artisan serve
```

---

## Cursor での使い方

### パターン 1: ファイル作成
```
Cursor に上記プロンプトをコピペ → 実行 → ファイルをプロジェクトに配置
```

### パターン 2: エラー修正
```
エラーメッセージをコピペ → Cursor に貼り付け → 修正コードを適用
```

### パターン 3: リファクタリング
```
コード選択 → 「このコードを改善」 → 修正コード確認
```

---

## 本番環境デプロイ（Mixhost）

```bash
# ローカルで準備
composer install --optimize-autoloader --no-dev

# Mixhost にアップロード（Git or SFTP）
git clone [repository] ~/public_html/fighter-vote
cd ~/public_html/fighter-vote

# サーバー側セットアップ
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
# .env 編集（DB 接続情報）
php artisan migrate --force

# パーミッション・キャッシュ
chmod -R 755 storage bootstrap/cache
php artisan config:cache
php artisan view:cache
```

---

## チェックリスト

### 開発前
- [ ] 設計書確認（design_doc.md）
- [ ] DB スキーマ理解

### 各セッション完了時
- [ ] 機能動作確認
- [ ] エラーハンドリング確認
- [ ] テスト実行

### 本番環境
- [ ] APP_DEBUG=false
- [ ] APP_ENV=production
- [ ] HTTPS 設定
- [ ] DB バックアップ確認

---

**Cursor でスムーズに開発を進めてください！🚀**
