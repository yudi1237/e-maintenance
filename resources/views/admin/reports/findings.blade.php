<x-app-layout>
    <div class="em-shell space-y-6 py-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Log Temuan</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Temuan mesin beserta due date dan riwayat catatan status.</p>
        </div>
        <x-alert />
        <div class="em-glass overflow-hidden rounded-2xl">
            <x-admin.finding-recap-table :recaps="$recaps" :status-labels="$statusLabels" paginated :allow-status-change="false" />
        </div>
    </div>
</x-app-layout>
