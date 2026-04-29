<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fighter;

class FighterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CSVファイルのパス
        $csvFile = database_path('seeders/data/fighters.csv');

        // CSVファイルが存在するかチェック
        if (!file_exists($csvFile)) {
            $this->command->error('fighters.csv ファイルが見つかりません: ' . $csvFile);
            return;
        }

        // CSVを開く
        if (($handle = fopen($csvFile, 'r')) !== false) {
            // ヘッダー行をスキップ
            fgetcsv($handle, 1000, ',');

            // CSVの各行を読み込む
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // 空行をスキップ
                if (empty($row[0])) {
                    continue;
                }

                // 名前をキーにして、存在すれば更新、なければ作成
                $categories = !empty($row[2]) ? array_map('trim', explode(',', $row[2])) : [];
                
                Fighter::updateOrCreate(
                    ['name' => $row[0]],
                    [
                        'image_url' => $row[1],
                        'weight_class' => $categories
                    ]
                );
                $this->command->info('処理完了: ' . $row[0]);
            }

            fclose($handle);
            $this->command->info('選手の登録完了！');
        }
    }
}
