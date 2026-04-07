# 格闘家投票アプリケーション - 詳細設計書

## 1. プロジェクト概要

**プロジェクト名:** Fighter Vote App  
**説明:** 格闘家の強弱を投票し、DB に蓄積。匿名コメント機能付き  
**スタック:** Laravel + MySQL  
**ホスティング:** Mixhost レンタルサーバー

---

## 2. 要件定義

### 2.1 機能要件

| 機能 | 概要 | 必須 |
|------|------|------|
| 投票機能 | 格闘家に対して「強い」「弱い」を投票 | ○ |
| 投票履歴保存 | 投票結果を DB に蓄積 | ○ |
| 統計表示 | 格闘家ごとの投票集計（強い/弱い数、%） | ○ |
| 匿名コメント | 格闘家に対して匿名でコメント投稿 | ○ |
| コメント表示 | 投稿されたコメント一覧表示 | ○ |

### 2.2 非機能要件

| 項目 | 要件 |
|------|------|
| レスポンス時間 | 投票・コメント投稿: < 1 秒 |
| 可用性 | 99.9% アップタイム（Mixhost） |
| スケーラビリティ | 数千投票/日に対応可能 |
| セキュリティ | XSS、CSRF 対策実装 |
| スパム防止 | IP ベースのコメント投稿制限 |

---

## 3. データベース設計

### 3.1 ER 図

```
┌─────────────────┐
│    fighters     │
├─────────────────┤
│ id (PK)         │
│ name            │
│ image_url       │ (オプション)
│ created_at      │
│ updated_at      │
└─────────────────┘
        △
        │ (1:N)
        │
┌─────────────────┐         ┌─────────────────┐
│     votes       │         │    comments     │
├─────────────────┤         ├─────────────────┤
│ id (PK)         │         │ id (PK)         │
│ fighter_id (FK) │         │ fighter_id (FK) │
│ vote_type       │         │ content         │
│ ip_address      │         │ ip_address      │
│ user_agent      │         │ user_agent      │
│ created_at      │         │ created_at      │
│ updated_at      │         │ updated_at      │
└─────────────────┘         └─────────────────┘
```

### 3.2 テーブル定義

#### 3.2.1 fighters テーブル

```sql
CREATE TABLE fighters (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    image_url VARCHAR(500) NULLABLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_fighters_name ON fighters(name);
```

**カラム説明:**
- `id` - 主キー
- `name` - 格闘家名（一意制約）
- `image_url` - プロフィール画像 URL（将来対応用）
- `created_at` - 作成日時
- `updated_at` - 更新日時

---

#### 3.2.2 votes テーブル

```sql
CREATE TABLE votes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    fighter_id BIGINT UNSIGNED NOT NULL,
    vote_type ENUM('strong', 'weak') NOT NULL,
    ip_address VARCHAR(45) NULLABLE COMMENT 'IPv6対応',
    user_agent VARCHAR(500) NULLABLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE,
    INDEX idx_votes_fighter_id (fighter_id),
    INDEX idx_votes_created_at (created_at),
    INDEX idx_votes_type (vote_type),
    INDEX idx_votes_fighter_type (fighter_id, vote_type)
);
```

**カラム説明:**
- `id` - 主キー
- `fighter_id` - 格闘家 ID（外部キー）
- `vote_type` - 投票種別（'strong' または 'weak'）
- `ip_address` - IP アドレス（重複投票検知、スパム対策用）
- `user_agent` - ユーザーエージェント（デバイス識別）
- `created_at` - 投票日時

**インデックス戦略:**
- `fighter_id` - 格闘家別集計に頻繁に使用
- `vote_type` - フィルタリング用
- `fighter_id, vote_type` - 複合検索用
- `created_at` - 時系列データ取得用

---

#### 3.2.3 comments テーブル

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    fighter_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    ip_address VARCHAR(45) NULLABLE COMMENT 'IPv6対応',
    user_agent VARCHAR(500) NULLABLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (fighter_id) REFERENCES fighters(id) ON DELETE CASCADE,
    INDEX idx_comments_fighter_id (fighter_id),
    INDEX idx_comments_created_at (created_at),
    INDEX idx_comments_ip_address (ip_address)
);
```

**カラム説明:**
- `id` - 主キー
- `fighter_id` - 格闘家 ID（外部キー）
- `content` - コメント本文
- `ip_address` - IP アドレス（スパム防止用）
- `user_agent` - ユーザーエージェント
- `created_at` - 投稿日時

**インデックス戦略:**
- `fighter_id` - 格闘家別コメント取得
- `created_at` - 時系列表示用
- `ip_address` - スパム検知用

---

### 3.3 マイグレーション ファイル構成

```
database/migrations/
├── 2024_01_01_000000_create_fighters_table.php
├── 2024_01_01_000001_create_votes_table.php
└── 2024_01_01_000002_create_comments_table.php
```

---

## 4. アーキテクチャ設計

### 4.1 ディレクトリ構成

```
fighter-vote/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── VoteController.php           # 投票・コメント処理
│   │   │   └── FighterController.php        # 格闘家管理（管理画面用）
│   │   ├── Requests/
│   │   │   ├── StoreVoteRequest.php         # 投票フォーム検証
│   │   │   ├── StoreCommentRequest.php      # コメント検証
│   │   │   └── StoreFighterRequest.php      # 格闘家登録検証
│   │   └── Resources/
│   │       ├── FighterResource.php          # 格闘家リソース
│   │       ├── VoteResource.php             # 投票リソース
│   │       └── CommentResource.php          # コメントリソース
│   ├── Models/
│   │   ├── Fighter.php                      # 格闘家モデル
│   │   ├── Vote.php                         # 投票モデル
│   │   └── Comment.php                      # コメントモデル
│   ├── Services/
│   │   ├── VoteService.php                  # 投票ビジネスロジック
│   │   └── FighterService.php               # 格闘家ビジネスロジック
│   └── Traits/
│       └── HasIpAddress.php                 # IP 取得トレイト
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── vote/
│   │   │   ├── index.blade.php              # 投票ページ
│   │   │   └── show.blade.php               # 詳細・統計ページ
│   │   └── admin/
│   │       └── fighters/
│   │           ├── index.blade.php          # 格闘家一覧（管理画面）
│   │           ├── create.blade.php         # 登録フォーム
│   │           └── edit.blade.php           # 編集フォーム
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php                              # Web ルート
│   └── api.php                              # API ルート（オプション）
├── database/
│   └── migrations/
│       ├── 2024_01_01_000000_create_fighters_table.php
│       ├── 2024_01_01_000001_create_votes_table.php
│       └── 2024_01_01_000002_create_comments_table.php
└── tests/
    ├── Feature/
    │   ├── VoteTest.php
    │   ├── CommentTest.php
    │   └── FighterTest.php
    └── Unit/
        └── VoteServiceTest.php
```

---

## 5. API・ルート設計

### 5.1 Web ルート（MPA）

```php
// routes/web.php

// 投票ページ
GET  /                          → VoteController@index          # 投票ページ表示
GET  /fighter/{id}              → VoteController@show            # 詳細ページ（統計・コメント）

// 投票・コメント（AJAX）
POST /api/votes                 → VoteController@store           # 投票を記録
POST /api/comments              → VoteController@storeComment    # コメント投稿

// 管理画面（オプション - Basic Auth）
GET  /admin/fighters            → FighterController@index        # 格闘家一覧
GET  /admin/fighters/create     → FighterController@create       # 登録フォーム
POST /admin/fighters            → FighterController@store        # 格闘家を保存
GET  /admin/fighters/{id}/edit  → FighterController@edit         # 編集フォーム
PUT  /admin/fighters/{id}       → FighterController@update       # 格闘家を更新
DELETE /admin/fighters/{id}     → FighterController@destroy      # 格闘家を削除
```

### 5.2 API エンドポイント（JSON）

#### 5.2.1 POST /api/votes

**リクエスト:**
```json
{
  "fighter_id": 1,
  "vote_type": "strong"
}
```

**レスポンス (200 OK):**
```json
{
  "success": true,
  "message": "投票ありがとうございました",
  "data": {
    "fighter_id": 1,
    "vote_type": "strong",
    "stats": {
      "strong_count": 150,
      "weak_count": 50,
      "total_count": 200,
      "strong_percentage": 75.0,
      "weak_percentage": 25.0
    }
  }
}
```

**エラーレスポンス (400 Bad Request):**
```json
{
  "success": false,
  "message": "不正なリクエスト",
  "errors": {
    "fighter_id": ["格闘家が見つかりません"]
  }
}
```

---

#### 5.2.2 POST /api/comments

**リクエスト:**
```json
{
  "fighter_id": 1,
  "content": "この格闘家はすごい！"
}
```

**レスポンス (200 OK):**
```json
{
  "success": true,
  "message": "コメントありがとうございました",
  "data": {
    "id": 123,
    "fighter_id": 1,
    "content": "この格闘家はすごい！",
    "created_at": "2024-01-15 10:30:00"
  }
}
```

**エラーレスポンス (429 Too Many Requests):**
```json
{
  "success": false,
  "message": "しばらく待ってからコメントしてください"
}
```

---

## 6. モデル設計

### 6.1 Fighter モデル

```php
class Fighter extends Model
{
    protected $fillable = ['name', 'image_url'];
    
    // リレーション
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    // 投票統計取得
    public function getVoteStats()
    {
        $votes = $this->votes()->get();
        $strong = $votes->where('vote_type', 'strong')->count();
        $weak = $votes->where('vote_type', 'weak')->count();
        $total = $votes->count();
        
        return [
            'strong_count' => $strong,
            'weak_count' => $weak,
            'total_count' => $total,
            'strong_percentage' => $total > 0 ? round(($strong / $total) * 100, 1) : 0,
            'weak_percentage' => $total > 0 ? round(($weak / $total) * 100, 1) : 0,
        ];
    }
}
```

---

### 6.2 Vote モデル

```php
class Vote extends Model
{
    use HasIpAddress;
    
    protected $fillable = ['fighter_id', 'vote_type', 'ip_address', 'user_agent'];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    // リレーション
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }
    
    // スコープ
    public function scopeByType($query, $type)
    {
        return $query->where('vote_type', $type);
    }
    
    public function scopeByFighter($query, $fighterId)
    {
        return $query->where('fighter_id', $fighterId);
    }
}
```

---

### 6.3 Comment モデル

```php
class Comment extends Model
{
    use HasIpAddress;
    
    protected $fillable = ['fighter_id', 'content', 'ip_address', 'user_agent'];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    // リレーション
    public function fighter()
    {
        return $this->belongsTo(Fighter::class);
    }
    
    // バリデーション
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // HTML エスケープ
            $model->content = htmlspecialchars($model->content, ENT_QUOTES, 'UTF-8');
        });
    }
}
```

---

## 7. ビジネスロジック設計

### 7.1 VoteService

```php
class VoteService
{
    /**
     * 投票を記録
     */
    public function vote(Fighter $fighter, string $voteType, string $ipAddress): array
    {
        // 投票を記録
        Vote::create([
            'fighter_id' => $fighter->id,
            'vote_type' => $voteType,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
        ]);
        
        // 最新の統計を返す
        return $fighter->getVoteStats();
    }
    
    /**
     * 重複投票検知（オプション）
     */
    public function canVote(Fighter $fighter, string $ipAddress, int $minutes = 1): bool
    {
        $recentVote = Vote::where('fighter_id', $fighter->id)
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->first();
        
        return !$recentVote;
    }
}
```

---

### 7.2 FighterService

```php
class FighterService
{
    /**
     * ランダムに N 人の格闘家を取得
     */
    public function getRandomFighters(int $count = 2): Collection
    {
        return Fighter::inRandomOrder()->limit($count)->get();
    }
    
    /**
     * 全格闘家と投票統計を取得
     */
    public function getFightersWithStats(): Collection
    {
        return Fighter::with('votes')
            ->orderBy('name')
            ->get()
            ->map(function ($fighter) {
                $fighter->stats = $fighter->getVoteStats();
                return $fighter;
            });
    }
}
```

---

## 8. UI・UX 設計

### 8.1 投票ページ（GET /）

**概要:**
- ランダムに 1 人の格闘家を表示し、「強い」または「弱い」を投票できる。投票後、次の格闘家に更新される。

**UI コンポーネント:**
```
┌─────────────────────────────────────────┐
│              格闘家名                   │
│          [格闘家画像]                   │
│                                         │
│          [強い]    [弱い]               │
│                                         │
│        [次へ (新しい格闘家)]            │
│        [統計を見る]                     │
└─────────────────────────────────────────┘
```

**インタラクション:**
- 「強い」ボタンクリック → 現在の格闘家に「強い」を投票 → ページリロード（次の格闘家表示）
- 「弱い」ボタンクリック → 現在の格闘家に「弱い」を投票 → ページリロード（次の格闘家表示）
- 「次へ」ボタン → ページリロード（新しい格闘家表示）
- 「統計を見る」 → 詳細ページ遷移

---

### 8.2 詳細ページ（GET /fighter/{id}）

**概要:**
- 格闘家の投票統計表示
- 投票の強い/弱いの棒グラフ表示
- コメント一覧（最新 50 件）
- コメント投稿フォーム

**UI コンポーネント:**
```
┌─────────────────────────────────────────┐
│  格闘家名                                 │
│                                         │
│  投票統計:                               │
│  強い: [████████████░░░] 75% (150 票)   │
│  弱い: [████░░░░░░░░░░░] 25% (50 票)    │
│  合計: 200 票                            │
│                                         │
│  [投票ページに戻る]                      │
│                                         │
│  コメント投稿:                           │
│  ┌─────────────────────────────────┐   │
│  │ コメント（匿名）                  │   │
│  └─────────────────────────────────┘   │
│  [投稿]                                  │
│                                         │
│  コメント一覧:                           │
│  • 投稿 1 (10分前)                      │
│  • 投稿 2 (1時間前)                     │
│  ...                                    │
└─────────────────────────────────────────┘
```

---

## 9. セキュリティ設計

### 9.1 対策項目

| 脅威 | 対策 | 実装場所 |
|------|------|---------|
| XSS | HTML エスケープ | Comment モデル |
| CSRF | CSRF トークン | Laravel 標準 |
| SQL インジェクション | プリペアドステートメント | Eloquent ORM |
| スパム投稿 | IP + 時間ベース制限 | CommentService |
| DDoS | レート制限 | Middleware |

### 9.2 実装詳細

**コメント投稿レート制限:**
```php
// 同一 IP から 1 分以内に複数コメント投稿を防止
public function canPostComment($ipAddress, $minutes = 1): bool
{
    $recentComment = Comment::where('ip_address', $ipAddress)
        ->where('created_at', '>=', now()->subMinutes($minutes))
        ->first();
    
    return !$recentComment;
}
```

**コンテンツ検証:**
```php
// コメント本文のバリデーション
public function validateCommentContent($content): bool
{
    // 長さチェック
    if (strlen($content) < 3 || strlen($content) > 500) {
        return false;
    }
    
    // スパムキーワード検知（オプション）
    $spamKeywords = ['spam', 'bot', '...']; // 定義
    foreach ($spamKeywords as $keyword) {
        if (stripos($content, $keyword) !== false) {
            return false;
        }
    }
    
    return true;
}
```

---

## 10. パフォーマンス設計

### 10.1 データベース最適化

**インデックス戦略:**
- `votes.fighter_id` - 格闘家別集計クエリ
- `votes.created_at` - 時系列フィルタ
- `votes.fighter_id, vote_type` - 複合検索
- `comments.fighter_id` - コメント取得
- `comments.created_at` - 最新コメント表示

**クエリ最適化:**
```php
// N+1 問題回避
$fighters = Fighter::with('votes', 'comments')->get();

// 集計関数使用（DB 側で処理）
$stats = Vote::where('fighter_id', $id)
    ->selectRaw('vote_type, COUNT(*) as count')
    ->groupBy('vote_type')
    ->get();
```

### 10.2 キャッシング戦略

```php
// 投票統計キャッシュ（5 分）
$stats = Cache::remember(
    "fighter_stats_{$fighterId}",
    300,
    fn() => $fighter->getVoteStats()
);
```

---

## 11. テスト戦略

### 11.1 テストケース

**Feature テスト:**
- [ ] 投票が正常に記録される
- [ ] 投票統計が正しく計算される
- [ ] コメントが投稿される
- [ ] IP ベースのスパム防止が機能する
- [ ] 不正リクエストが拒否される

**Unit テスト:**
- [ ] Fighter.getVoteStats() が正しい統計を返す
- [ ] VoteService.vote() が投票を記録する
- [ ] Comment モデルが HTML エスケープする

---

## 12. デプロイメント設計

### 12.1 Mixhost デプロイ構成

**ディレクトリ構成:**
```
~/public_html/
├── fighter-vote/          # Laravel プロジェクト
│   ├── public/            # 公開ディレクトリ
│   │   └── index.php
│   └── ...
└── index.php              # .htaccess でリダイレクト
```

**必須セットアップ:**
1. SSH 接続可能
2. Composer インストール可能
3. MySQL データベース作成
4. PHP 8.1+ 環境

### 12.2 環境管理

**.env ファイル:**
```env
# ローカル開発
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite

# 本番環境
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=[Mixhost で作成]
DB_USERNAME=[DB ユーザー]
DB_PASSWORD=[セキュアなパスワード]
```

---

## 13. 拡張ポイント

**将来的に追加可能な機能:**

```
1. 管理画面
   - 格闘家の CRUD（Web UI）
   - 投票・コメント統計ダッシュボード

2. ユーザー認証
   - 管理者ログイン
   - ユーザー認証投票（匿名性低下）

3. ランキング表示
   - 最強格闘家ランキング
   - 最新投票トレンド

4. API 公開
   - JSON API で外部連携

5. ソーシャル機能
   - Twitter 共有
   - ランキング表示
```

---

## 14. 開発マイルストーン

### フェーズ 1: 基本機能（MVP）
- [ ] DB 設計・マイグレーション
- [ ] Fighter モデル作成
- [ ] Vote・Comment モデル作成
- [ ] 投票ページ実装
- [ ] 詳細ページ実装

### フェーズ 2: ポーランド
- [ ] UI/UX 改善
- [ ] パフォーマンス最適化
- [ ] テスト追加

### フェーズ 3: 本番デプロイ
- [ ] Mixhost セットアップ
- [ ] 環境変数設定
- [ ] SSL 設定
- [ ] 本番テスト

---

## 15. ドキュメント

### 15.1 参照資料

- Laravel 公式ドキュメント: https://laravel.com/docs
- Eloquent ORM: https://laravel.com/docs/eloquent
- Blade テンプレート: https://laravel.com/docs/blade

### 15.2 開発ガイドライン

**コーディング規約:**
- PSR-12 スタイル準拠
- メソッド名: camelCase
- クラス名: PascalCase
- 定数: UPPER_SNAKE_CASE

**Git コミットメッセージ:**
```
feat: 新機能追加
fix: バグ修正
refactor: リファクタリング
test: テスト追加
docs: ドキュメント
```

---

## 16. 技術スタック

| レイヤー | 技術 | バージョン |
|---------|------|-----------|
| Framework | Laravel | 11.x |
| Database | MySQL | 8.0+ |
| Web Server | Apache | 2.4+ |
| PHP | PHP | 8.1+ |
| Frontend | Blade + Alpine.js | - |
| CSS | Tailwind CSS | 3.x |

---

**設計書完成日:** 2024 年 1 月  
**最終更新:** 2024 年 1 月  
**ステータス:** 確認待ち
