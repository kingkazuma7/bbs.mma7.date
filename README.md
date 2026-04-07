# 格闘家投票アプリ - 設計書パッケージ

**Cursor で開発するための必要十分な設計書です。**

---

## 📄 ファイル一覧

| ファイル | 説明 | 用途 |
|---------|------|------|
| **design_doc.md** | 技術仕様書（詳細） | 実装前に仕様を確認 |
| **cursor_guide_minimal.md** | Cursor開発ガイド（ミニマム） | 開発時にプロンプトをコピペ |

---

## 🚀 開発フロー（3ステップ）

### 1️⃣ プロジェクト作成

```bash
composer create-project laravel/laravel fighter-vote
cd fighter-vote
```

### 2️⃣ Cursor で開発

cursor_guide_minimal.md の**セッション 1～5** を順番に実行

各セッションでプロンプトをコピーして Cursor に貼り付け → 実行 → ファイル配置

### 3️⃣ 本番デプロイ

cursor_guide_minimal.md の**本番環境デプロイ**セクション参照

---

## 📋 セッション概要

| セッション | タスク | 時間 |
|----------|--------|------|
| 1 | マイグレーション（3テーブル） | 15分 |
| 2 | モデル・Service 実装 | 30分 |
| 3 | コントローラー・リクエスト | 30分 |
| 4 | ルート定義 | 10分 |
| 5 | Blade テンプレート | 45分 |
| 6 | テスト（オプション） | 20分 |
| **合計** | **全体** | **2.5時間** |

---

## 💾 プロジェクト構成

```
fighter-vote/
├── app/
│   ├── Http/
│   │   ├── Controllers/VoteController.php
│   │   └── Requests/
│   │       ├── StoreVoteRequest.php
│   │       └── StoreCommentRequest.php
│   ├── Models/
│   │   ├── Fighter.php
│   │   ├── Vote.php
│   │   └── Comment.php
│   └── Services/
│       ├── VoteService.php
│       └── CommentService.php
├── resources/views/
│   ├── layouts/app.blade.php
│   └── vote/
│       ├── index.blade.php
│       └── show.blade.php
├── routes/web.php
├── database/migrations/
│   ├── *_create_fighters_table.php
│   ├── *_create_votes_table.php
│   └── *_create_comments_table.php
└── tests/Feature/VoteTest.php
```

---

## 🎯 使い方

### cursor_guide_minimal.md の読み方

```
1. 「セッション 1: マイグレーション」のプロンプトを全コピー
2. Cursor に貼り付け
3. 「実行」をクリック
4. 生成されたコード → プロジェクトに配置
5. 動作確認 → セッション 2 へ
```

### 詰まったときの対処法

1. **エラーが出た** 
   → エラーメッセージを Cursor に貼り付け → 修正提案を実行

2. **仕様が不明**
   → design_doc.md で確認

3. **パフォーマンス最適化**
   → cursor_guide_minimal.md の「本番環境」セクション参照

---

## ✅ チェックリスト

### 開発前
- [ ] Laravel プロジェクト作成
- [ ] cursor_guide_minimal.md をブックマーク

### セッション 1-5
- [ ] マイグレーション実装・確認（php artisan migrate）
- [ ] モデル実装・確認（php artisan tinker）
- [ ] コントローラー実装・確認
- [ ] ルート定義・確認（php artisan route:list）
- [ ] ビュー実装・UI確認（php artisan serve）

### 本番環境
- [ ] composer install --no-dev
- [ ] 環境変数設定（.env）
- [ ] php artisan migrate --force
- [ ] キャッシュ最適化
- [ ] HTTPS 設定

---

## 📊 DB スキーマ（一目で確認）

ログイン
`mysql -u kingkazuma7 -p`

```
fighters
├── id (PK)
├── name (UNIQUE)
├── image_url (NULL)
└── timestamps

votes
├── id (PK)
├── fighter_id (FK)
├── vote_type (strong/weak)
├── ip_address
├── user_agent
└── timestamps

comments
├── id (PK)
├── fighter_id (FK)
├── content
├── ip_address
├── user_agent
└── timestamps
```

---

## 🔗 よく使うコマンド

```bash
# マイグレーション実行
php artisan migrate

# DB 接続確認
php artisan tinker
>>> \App\Models\Fighter::all();

# ルート確認
php artisan route:list

# サーバー起動
php artisan serve

# テスト実行
php artisan test

# 本番環境準備
php artisan config:cache
php artisan view:cache
```

---

## 🆘 よくある問題

| 問題 | 原因 | 解決策 |
|------|------|------|
| Table not found | マイグレーション未実行 | `php artisan migrate` |
| Model not found | クラスパス誤り | namespace を確認 |
| CSRF token mismatch | トークン検証失敗 | meta タグで csrf_token を設定 |
| N+1 query | Eager Loading 未使用 | `with()` を追加 |

---

## 📚 外部ドキュメント

- Laravel 公式: https://laravel.com
- Eloquent ORM: https://laravel.com/docs/eloquent
- Blade テンプレート: https://laravel.com/docs/blade

---

## 🎉 完成後

- **管理画面追加** - 格闘家 CRUD
- **ランキング表示** - 強い順ランキング
- **トレンド分析** - 投票トレンド表示
- **API 公開** - JSON API で外部連携

詳細は design_doc.md の「13. 拡張ポイント」参照

---

**Cursor で効率的に開発してください！🚀**

**→ cursor_guide_minimal.md を開いて、セッション 1 から始めてください。**
