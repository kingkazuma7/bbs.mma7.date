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

                // 既に同じ名前の選手がいないかチェック
                $exists = Fighter::where('name', $row[0])->exists();

                if (!$exists) {
                    Fighter::create([
                        'name' => $row[0],
                        'image_url' => $row[1],
                    ]);
                    $this->command->info('登録完了: ' . $row[0]);
                } else {
                    $this->command->warn('スキップ（既に存在）: ' . $row[0]);
                }
            }

            fclose($handle);
            $this->command->info('選手の登録完了！');
        }
    }
}
