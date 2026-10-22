<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Edit Master Data Mesin {{ $machine->machine_number }}</h2>
            <a href="{{ route('admin.machine-names.index') }}" class="text-sm font-medium text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">← Kembali ke daftar mesin</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="em-shell space-y-6">
            <x-alert />

            <div class="em-glass rounded-2xl p-4 sm:p-6">
                <form method="POST" action="{{ route('admin.machine-names.update', $machine) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin.machine-names.fields', ['machine' => $machine])
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <a href="{{ route('admin.machine-names.index') }}" class="em-btn-secondary w-full justify-center sm:w-auto">Batal</a>
                        <button type="submit" class="em-btn-primary w-full justify-center sm:w-auto">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
