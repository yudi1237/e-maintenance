<?php

namespace Database\Seeders;

use App\Models\DamageCategory;
use App\Models\Equipment;
use App\Models\MachineName;
use App\Models\PreventiveCategoryRecord;
use App\Models\PreventiveChecklistItem;
use App\Models\Technician;
use App\Models\User;
use App\Support\PreventiveEvidence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LiveFeatureDemoSeeder extends Seeder
{
    public function run(): void
    {
        $masters = [
            'users' => User::query()->count(),
            'technicians' => Technician::query()->count(),
            'machines' => MachineName::query()->count(),
            'categories' => DamageCategory::query()->count(),
            'equipments' => Equipment::query()->count(),
            'checklist' => PreventiveChecklistItem::query()->count(),
            'preventive_categories' => PreventiveCategoryRecord::query()->count(),
        ];

        $this->deleteEvidenceFiles();
        $this->deleteOperationalRows();

        $created = $this->ensureMissingUsers();

        $this->call([
            DemoDashboardSeeder::class,
            PreventiveApprovalDemoSeeder::class,
            PreventiveScheduleDemoSeeder::class,
        ]);

        $unchanged = [
            'machines' => MachineName::query()->count(),
            'categories' => DamageCategory::query()->count(),
            'equipments' => Equipment::query()->count(),
            'checklist' => PreventiveChecklistItem::query()->count(),
            'preventive_categories' => PreventiveCategoryRecord::query()->count(),
        ];

        foreach ($unchanged as $key => $count) {
            if ($count !== $masters[$key]) {
                throw new \RuntimeException('Master '.$key.' berubah dari '.$masters[$key].' menjadi '.$count.'.');
            }
        }

        $this->command?->info('masters kept machines='.$masters['machines'].' equipments='.$masters['equipments'].' categories='.$masters['categories'].' users_before='.$masters['users'].' technicians_before='.$masters['technicians']);
        $this->command?->info('created_users='.(implode(',', $created) ?: 'none'));
        $this->command?->info('tickets='.DB::table('operator_reports')->count());
        $this->command?->info('maintenance_reports='.DB::table('maintenance_reports')->count());
        $this->command?->info('preventive_reports='.DB::table('preventive_reports')->count());
        $this->command?->info('preventive_schedules='.DB::table('preventive_schedules')->count());
        $this->command?->info('finding_recaps='.DB::table('finding_recaps')->count());
        $this->command?->info('users_after='.User::query()->count());
        $accounts = User::query()->orderBy('npk')->get(['npk', 'name', 'role']);
        foreach ($accounts as $account) {
            $this->command?->info('account '.$account->npk.' '.$account->role.' '.$account->name);
        }
    }

    private function deleteEvidenceFiles(): void
    {
        $disk = Storage::disk('public');

        foreach (DB::table('maintenance_reports')->whereNotNull('evidence_path')->pluck('evidence_path') as $path) {
            if (is_string($path) && $path !== '' && ! str_starts_with($path, 'machine-photos/')) {
                $disk->delete($path);
            }
        }

        foreach (['preventive_reports', 'preventive_conveyor_reports'] as $table) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                continue;
            }

            foreach (DB::table($table)->whereNotNull('evidence_paths')->pluck('evidence_paths') as $raw) {
                $paths = is_string($raw) ? json_decode($raw, true) : $raw;
                PreventiveEvidence::delete(is_array($paths) ? $paths : []);
            }
        }

        foreach (['evidence', PreventiveEvidence::DIRECTORY, PreventiveEvidence::CONVEYOR_DIRECTORY] as $directory) {
            $disk->deleteDirectory($directory);
        }
    }

    private function deleteOperationalRows(): void
    {
        DB::table('finding_recap_status_notes')->delete();
        DB::table('finding_recaps')->delete();
        DB::table('maintenance_reports')->delete();
        DB::table('operator_reports')->delete();
        DB::table('preventive_reports')->delete();

        if (DB::getSchemaBuilder()->hasTable('preventive_conveyor_reports')) {
            DB::table('preventive_conveyor_reports')->delete();
        }

        DB::table('preventive_schedules')->delete();

        if (DB::getSchemaBuilder()->hasTable('machine_run_days')) {
            DB::table('machine_run_days')->delete();
        }
    }

    /**
     * @return list<string>
     */
    private function ensureMissingUsers(): array
    {
        $accounts = [
            ['npk' => '100001', 'name' => 'Administrator', 'role' => User::ROLE_ADMIN],
            ['npk' => '200001', 'name' => 'Teknisi Maintenance', 'role' => User::ROLE_TECHNICIAN],
            ['npk' => '400001', 'name' => 'Produksi Injection Demo', 'role' => User::ROLE_PRODUKSI_INJECTION],
            ['npk' => '300001', 'name' => 'Quality Demo', 'role' => User::ROLE_QUALITY],
            ['npk' => '500001', 'name' => 'Management Demo', 'role' => User::ROLE_MANAGEMENT],
        ];

        $created = [];

        foreach ($accounts as $account) {
            if (User::query()->where('npk', $account['npk'])->exists()) {
                continue;
            }

            $user = User::query()->create([
                'npk' => $account['npk'],
                'name' => $account['name'],
                'password' => 'password',
                'role' => $account['role'],
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $created[] = $account['npk'].':'.$account['role'];
        }

        return $created;
    }
}
