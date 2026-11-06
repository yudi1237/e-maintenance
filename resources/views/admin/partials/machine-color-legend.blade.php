<div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-500 dark:text-slate-400">
    <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-slate-600 border border-slate-500"></span> Standby</span>
    <span class="flex items-center gap-2"><span class="em-machine-alert-dot h-3 w-3 rounded-full"></span> OP belum diambil</span>
    <span class="flex items-center gap-2"><span class="em-machine-pending-dot h-3 w-3 rounded-full"></span> OP On Progress</span>
    @if (auth()->user()->seesMachineApprovalColor())
        <span class="flex items-center gap-2"><span class="em-machine-approval-dot h-3 w-3 rounded-full"></span> Close by teknisi, menunggu approval</span>
    @endif
    <span class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full" style="background:#22c55e"></span> Running</span>
    <span class="flex items-center gap-2"><span class="inline-block h-3 w-3 rounded-full" style="background:#ef4444"></span> Breakdown</span>
</div>
