<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Console\Command;
use PDO;

/**
 * استيراد العملاء والمشاريع من قاعدة بيانات نظام marketlink القديم (SQLite).
 *
 * php artisan marketlink:import-legacy storage/legacy.sqlite
 *
 * - العملاء: customers -> clients (إيميل مولّد لعدم وجوده في القديم)
 * - المشاريع: projects -> projects مع إزالة تكرار النسخ الشهرية (نفس الاسم لنفس العميل)
 * - إعادة التشغيل آمنة: يتخطى ما تم استيراده (مطابقة على الإيميل المولّد/اسم المشروع)
 */
class ImportLegacyData extends Command
{
    protected $signature = 'marketlink:import-legacy {path : Path to legacy sqlite file}';

    protected $description = 'Import clients and projects from the legacy marketlink sqlite database';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error("Legacy sqlite not found: {$path}");

            return self::FAILURE;
        }

        $organization = Organization::where('slug', 'marketlink')->first();

        if (! $organization) {
            $this->error('Organization "marketlink" not found. Run MarketLinkTeamSeeder first.');

            return self::FAILURE;
        }

        $legacy = new PDO('sqlite:'.$path, options: [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $clientMap = $this->importClients($legacy, $organization);
        $this->importProjects($legacy, $organization, $clientMap);

        return self::SUCCESS;
    }

    /** @return array<int,int> legacy customer id => new client id */
    private function importClients(PDO $legacy, Organization $organization): array
    {
        $map = [];
        $created = 0;
        $skipped = 0;

        $rows = $legacy->query('SELECT id, name, phone, status, created_at FROM customers ORDER BY id')
            ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $email = 'legacy-client-'.$row['id'].'@import.marketlink.app';

            $client = Client::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'status' => $row['status'] === 'active' ? 'active' : 'inactive',
                    'organization_id' => $organization->id,
                    'notes' => 'مستورد من النظام القديم (customer #'.$row['id'].')',
                ]
            );

            $client->wasRecentlyCreated ? $created++ : $skipped++;
            $map[(int) $row['id']] = $client->id;
        }

        $this->info("Clients: {$created} imported, {$skipped} already present.");

        return $map;
    }

    /** @param array<int,int> $clientMap */
    private function importProjects(PDO $legacy, Organization $organization, array $clientMap): void
    {
        $rows = $legacy->query(
            'SELECT customer_id, name, status,
                    GROUP_CONCAT(DISTINCT month) AS months,
                    MIN(created_at) AS first_created
             FROM projects
             GROUP BY customer_id, name
             ORDER BY MIN(id)'
        )->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $skipped = 0;
        $orphans = 0;

        foreach ($rows as $row) {
            $clientId = $clientMap[(int) $row['customer_id']] ?? null;

            if (! $clientId) {
                $orphans++;

                continue;
            }

            $exists = Project::where('organization_id', $organization->id)
                ->where('client_id', $clientId)
                ->where('business_name', $row['name'])
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            Project::create([
                'client_id' => $clientId,
                'business_name' => $row['name'],
                'business_description' => 'مستورد من النظام القديم — شهور نشطة: '.($row['months'] ?: 'غير محدد'),
                'status' => $row['status'] === 'active' ? 'active' : 'inactive',
                'organization_id' => $organization->id,
            ]);

            $created++;
        }

        $this->info("Projects: {$created} imported, {$skipped} already present, {$orphans} without client.");
    }
}
