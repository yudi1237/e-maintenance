<?php

namespace App\Http\Controllers;

use App\Models\OperatorReport;
use App\Support\PicSession;
use App\Support\TablePagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class TechnicianHomeController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->user()->canAccessAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        PicSession::lockForm2();

        $operatorReports = $this->ticketBoard();

        return view('technician.home', [
            'operatorReports' => $operatorReports,
            'ticketsSignature' => $this->signature($operatorReports),
            'activePic' => PicSession::technician(),
        ]);
    }

    public function tickets(): JsonResponse
    {
        $operatorReports = $this->ticketBoard();

        return response()->json([
            'html' => view('partials.operator-tickets', [
                'operatorReports' => $operatorReports,
            ])->render(),
            'signature' => $this->signature($operatorReports),
            'updated_at' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
        ]);
    }

    private function ticketBoard(): LengthAwarePaginator
    {
        return OperatorReport::query()->board()->paginate(TablePagination::perPage(), ['*'], 'tiket');
    }

    private function signature(LengthAwarePaginator $operatorReports): string
    {
        $payload = $operatorReports->getCollection()
            ->map(function (OperatorReport $ticket) {
                $ticket->loadMissing('maintenanceReport');

                return implode('|', [
                    $ticket->id,
                    $ticket->status,
                    $ticket->claimed_by,
                    $ticket->claimed_technician_id,
                    $ticket->claimed_at,
                    $ticket->maintenanceReport?->step2_completed_at,
                    $ticket->updated_at,
                ]);
            })
            ->implode(';');

        return hash('sha256', $payload.'|'.$operatorReports->total().'|'.$operatorReports->currentPage());
    }
}
