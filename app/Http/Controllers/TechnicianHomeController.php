<?php

namespace App\Http\Controllers;

use App\Models\AreaOperatorReport;
use App\Models\OperatorReport;
use App\Support\MachineCardBoard;
use App\Support\PicSession;
use App\Support\TablePagination;
use App\Support\TechnicianRecentWork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class TechnicianHomeController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (auth()->user()->canAccessAdmin() || auth()->user()->isOperator()) {
            return redirect()->route(auth()->user()->homeRouteName());
        }

        PicSession::lockForm2();

        $board = MachineCardBoard::payload();

        return view('technician.home', [
            'machines' => $board['machines'],
            'machineStats' => $board['machineStats'],
            'preventiveToday' => $board['preventiveToday'],
            'runningMachines' => $board['runningMachines'],
            'breakdownMachines' => $board['breakdownMachines'],
            'standbyMachines' => $board['standbyMachines'],
            'machineTotal' => $board['machineTotal'],
        ]);
    }

    public function opTickets(): View|RedirectResponse
    {
        if (auth()->user()->canAccessAdmin() || auth()->user()->isOperator()) {
            return redirect()->route(auth()->user()->homeRouteName());
        }

        PicSession::lockForm2();

        $operatorReports = $this->ticketBoard();
        $areaReports = $this->areaBoard();

        return view('technician.op-tickets', [
            'operatorReports' => $operatorReports,
            'ticketsSignature' => $this->signature($operatorReports),
            'areaReports' => $areaReports,
            'areaSignature' => $this->areaSignature($areaReports),
            'activePic' => PicSession::technician(),
            'recentSubmissions' => TechnicianRecentWork::forUser((int) auth()->id()),
        ]);
    }

    public function machineCards(): JsonResponse
    {
        $board = MachineCardBoard::payload();

        return response()->json([
            'machines' => $board['machineStats'],
            'preventive_today' => $board['preventiveToday'],
            'running' => $board['runningMachines'],
            'breakdown' => $board['breakdownMachines'],
            'standby' => $board['standbyMachines'],
            'updated_at' => now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
        ]);
    }

    public function tickets(): JsonResponse
    {
        $operatorReports = $this->ticketBoard();
        $areaReports = $this->areaBoard();

        return response()->json([
            'html' => view('partials.operator-tickets', [
                'operatorReports' => $operatorReports,
            ])->render(),
            'signature' => $this->signature($operatorReports),
            'area_html' => view('partials.area-operator-tickets', [
                'areaReports' => $areaReports,
            ])->render(),
            'area_signature' => $this->areaSignature($areaReports),
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

    private function areaBoard(): LengthAwarePaginator
    {
        return AreaOperatorReport::query()->board()->paginate(TablePagination::perPage(), ['*'], 'area');
    }

    private function areaSignature(LengthAwarePaginator $areaReports): string
    {
        $payload = $areaReports->getCollection()
            ->map(function (AreaOperatorReport $ticket) {
                return implode('|', [
                    $ticket->id,
                    $ticket->status,
                    $ticket->claimed_technician_id,
                    $ticket->claimed_at,
                    $ticket->closed_at,
                    $ticket->updated_at,
                ]);
            })
            ->implode(';');

        return hash('sha256', $payload.'|'.$areaReports->total().'|'.$areaReports->currentPage());
    }
}
