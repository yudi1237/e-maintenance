<?php

use App\Http\Controllers\Admin\DamageCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FindingRecapController;
use App\Http\Controllers\Admin\MachineNameController;
use App\Http\Controllers\Admin\MachineQrController;
use App\Http\Controllers\Admin\MoldController;
use App\Http\Controllers\Admin\MaintenanceReportController as AdminMaintenanceReportController;
use App\Http\Controllers\Admin\OperatorReportController as AdminOperatorReportController;
use App\Http\Controllers\Admin\PreventiveChecklistItemController;
use App\Http\Controllers\Admin\PreventiveReportController as AdminPreventiveReportController;
use App\Http\Controllers\Admin\PreventiveScheduleController;
use App\Http\Controllers\Admin\ReportBoardController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\RobotController;
use App\Http\Controllers\Admin\SparepartHistoryController;
use App\Http\Controllers\Admin\TechnicianController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AreaOperatorReportController;
use App\Http\Controllers\MaintenanceReportController;
use App\Http\Controllers\PreventiveConveyorReportController;
use App\Http\Controllers\PreventiveReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Scan\MachineController as ScanMachineController;
use App\Http\Controllers\TechnicianHomeController;
use App\Http\Controllers\TechnicianSubmissionController;
use App\Support\PreventiveCategory;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(
            auth()->user()->homeRouteName()
        );
    }

    return view('auth.login');
});

Route::middleware(['throttle:30,1', 'scan.public'])->group(function () {
    Route::get('/scan/mesin/{machineNumber}/{token}', [ScanMachineController::class, 'show'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.machines.show');

    Route::get('/scan/mesin/{machineNumber}/{token}/info', [ScanMachineController::class, 'info'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.machines.info');

    Route::get('/scan/mesin/{machineNumber}/{token}/input-op', [ScanMachineController::class, 'createOperatorReport'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.operator-reports.create');

    Route::post('/scan/mesin/{machineNumber}/{token}/input-op', [ScanMachineController::class, 'storeOperatorReport'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.operator-reports.store');

    Route::get('/input-op-area', [AreaOperatorReportController::class, 'create'])->name('area-op.create');
    Route::post('/input-op-area', [AreaOperatorReportController::class, 'store'])->name('area-op.store');

    Route::get('/scan/mesin/{machineNumber}/{token}/preventive', [ScanMachineController::class, 'preventive'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.preventive.index');

    Route::get('/scan/mesin/{machineNumber}/{token}/preventive/{preventiveReport}', [ScanMachineController::class, 'preventiveShow'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.preventive.show');

    Route::get('/scan/mesin/{machineNumber}/{token}/rekap-temuan', [ScanMachineController::class, 'findingRecaps'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.finding-recaps.index');

    Route::get('/scan/mesin/{machineNumber}/{token}/laporan/{report}', [ScanMachineController::class, 'report'])
        ->whereNumber('machineNumber')
        ->where('token', '[a-f0-9]{64}')
        ->name('scan.reports.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [TechnicianHomeController::class, 'index'])->name('home');
    Route::get('/home/tiket-op', [TechnicianHomeController::class, 'opTickets'])->name('home.op-tickets');
    Route::get('/home/mesin', [TechnicianHomeController::class, 'machineCards'])->name('home.machines');
    Route::get('/operator', [DashboardController::class, 'index'])->name('operator.home');
    Route::get('/operator/running', [DashboardController::class, 'operatorRunning'])->name('operator.running');
    Route::get('/home/tiket', [TechnicianHomeController::class, 'tickets'])->name('home.tickets');
    Route::post('/tiket-op-area/{areaOperatorReport}/ambil', [AreaOperatorReportController::class, 'claim'])
        ->middleware('throttle:10,1')
        ->name('area-op.claim');
    Route::post('/tiket-op-area/{areaOperatorReport}/close', [AreaOperatorReportController::class, 'close'])
        ->middleware('throttle:10,1')
        ->name('area-op.close');
    Route::get('/home/pengisian/op/{report}', [TechnicianSubmissionController::class, 'showOp'])->name('home.submissions.op');
    Route::get('/home/pengisian/op/{report}/edit', [TechnicianSubmissionController::class, 'editOp'])->name('home.submissions.op.edit');
    Route::put('/home/pengisian/op/{report}', [TechnicianSubmissionController::class, 'updateOp'])->name('home.submissions.op.update');
    Route::get('/home/pengisian/preventive/{preventiveReport}', [TechnicianSubmissionController::class, 'showPreventive'])->name('home.submissions.preventive');
    Route::get('/home/pengisian/preventive/{preventiveReport}/edit', [TechnicianSubmissionController::class, 'editPreventive'])->name('home.submissions.preventive.edit');
    Route::put('/home/pengisian/preventive/{preventiveReport}', [TechnicianSubmissionController::class, 'updatePreventive'])->name('home.submissions.preventive.update');
    Route::get('/home/pengisian/conveyor/{preventiveConveyorReport}', [TechnicianSubmissionController::class, 'showConveyor'])->name('home.submissions.conveyor');
    Route::get('/home/pengisian/conveyor/{preventiveConveyorReport}/edit', [TechnicianSubmissionController::class, 'editConveyor'])->name('home.submissions.conveyor.edit');
    Route::put('/home/pengisian/conveyor/{preventiveConveyorReport}', [TechnicianSubmissionController::class, 'updateConveyor'])->name('home.submissions.conveyor.update');

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    Route::get('/laporan/baru', [MaintenanceReportController::class, 'create'])->name('reports.create');
    Route::post('/laporan', [MaintenanceReportController::class, 'store'])->name('reports.store');
    Route::post('/tiket-op/{operatorReport}/ambil', [MaintenanceReportController::class, 'claimTicket'])
        ->middleware('throttle:10,1')
        ->name('reports.tickets.claim');
    Route::post('/tiket-op/{operatorReport}/pic', [MaintenanceReportController::class, 'identifyPic'])
        ->middleware('throttle:10,1')
        ->name('reports.tickets.identify');
    Route::post('/tiket-op/{operatorReport}/close', [MaintenanceReportController::class, 'authorizeClose'])
        ->middleware('throttle:10,1')
        ->name('reports.tickets.close');
    Route::get('/laporan/{report}/form-2', [MaintenanceReportController::class, 'step2'])->name('reports.step2');
    Route::post('/laporan/{report}/form-2', [MaintenanceReportController::class, 'storeStep2'])->name('reports.step2.store');

    Route::post('/preventive/pic', [PreventiveReportController::class, 'identifyPic'])
        ->middleware('throttle:10,1')
        ->name('preventive.pic');
    Route::get('/preventive', [PreventiveReportController::class, 'menu'])->name('preventive.menu');
    Route::get('/preventive/jadwal', [PreventiveReportController::class, 'schedules'])->name('preventive.schedules');
    Route::get('/preventive/baru', [PreventiveReportController::class, 'create'])->name('preventive.create');
    Route::get('/preventive/baru/{machineNumber}/{category}', [PreventiveReportController::class, 'form'])
        ->whereNumber('machineNumber')
        ->where('category', '^(?:'.PreventiveCategory::routePattern().')$')
        ->name('preventive.form');
    Route::get('/preventive/baru/{machineNumber}', [PreventiveReportController::class, 'categories'])
        ->whereNumber('machineNumber')
        ->name('preventive.categories');
    Route::post('/preventive', [PreventiveReportController::class, 'store'])->name('preventive.store');
    Route::get('/preventive-conveyor/baru', [PreventiveConveyorReportController::class, 'create'])->name('preventive-conveyor.create');
    Route::post('/preventive-conveyor', [PreventiveConveyorReportController::class, 'store'])->name('preventive-conveyor.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
        Route::post('/dashboard/cluster', [DashboardController::class, 'updateClusterName'])->name('layout-clusters.update');
        Route::post('/dashboard/technician-shifts', [DashboardController::class, 'updateTechnicianShifts'])->name('technicians.shifts');
        Route::post('/dashboard/running', [DashboardController::class, 'updateRunning'])->name('machines.running');
        Route::get('/mesin/qr/semua', [MachineQrController::class, 'downloadAll'])->name('machines.qr.download-all');
        Route::get('/mesin/{machineNumber}/qr', [MachineQrController::class, 'image'])->whereNumber('machineNumber')->name('machines.qr');
        Route::get('/mesin/{machineNumber}/qr/download', [MachineQrController::class, 'download'])->whereNumber('machineNumber')->name('machines.qr.download');
        Route::get('/mesin/{machineNumber}', [DashboardController::class, 'machine'])->whereNumber('machineNumber')->name('machines.show');
        Route::post('/mesin/{machineNumber}/mold', [DashboardController::class, 'updateMold'])->whereNumber('machineNumber')->name('machines.mold');
        Route::get('/preventive', [AdminPreventiveReportController::class, 'index'])->name('preventive.index');
        Route::get('/preventive/{preventiveReport}/export', [AdminPreventiveReportController::class, 'export'])->name('preventive.export');
        Route::get('/preventive/{preventiveReport}', [AdminPreventiveReportController::class, 'show'])->name('preventive.show');
        Route::patch('/preventive/{preventiveReport}/status', [AdminPreventiveReportController::class, 'updateStatus'])->name('preventive.update-status');
        Route::get('/tiket-op/{operatorReport}', [AdminOperatorReportController::class, 'show'])->name('operator-reports.show');
        Route::get('/laporan/{report}', [AdminMaintenanceReportController::class, 'show'])->name('reports.show');
        Route::patch('/laporan/{report}/status', [AdminMaintenanceReportController::class, 'updateStatus'])->name('reports.update-status');
        Route::get('/export', [ReportExportController::class, 'export'])->name('reports.export');
        Route::get('/report/op-mesin', [ReportBoardController::class, 'machines'])->name('report.op-mesin');
        Route::get('/report/op-area', [ReportBoardController::class, 'areas'])->name('report.op-area');
        Route::get('/report/log-temuan', [ReportBoardController::class, 'findings'])->name('report.findings');

        Route::middleware('admin.full')->group(function () {
            Route::post('/preventive/schedules', [PreventiveScheduleController::class, 'store'])->name('preventive-schedules.store');
            Route::match(['put', 'patch'], '/preventive/schedules/{preventiveSchedule}', [PreventiveScheduleController::class, 'update'])->name('preventive-schedules.update');
            Route::delete('/preventive/schedules/{preventiveSchedule}', [PreventiveScheduleController::class, 'destroy'])->name('preventive-schedules.destroy');
            Route::delete('/tiket-op/{operatorReport}', [AdminOperatorReportController::class, 'destroy'])->name('operator-reports.destroy');
            Route::delete('/tiket-op-area/{areaOperatorReport}', [AreaOperatorReportController::class, 'destroy'])->name('area-operator-reports.destroy');
            Route::delete('/laporan/{report}', [AdminMaintenanceReportController::class, 'destroy'])->name('reports.destroy');
            Route::get('/rekap-temuan', [FindingRecapController::class, 'index'])->name('finding-recaps.index');
            Route::get('/rekap-temuan/export', [FindingRecapController::class, 'export'])->name('finding-recaps.export');
            Route::post('/rekap-temuan', [FindingRecapController::class, 'store'])->name('finding-recaps.store');
            Route::get('/rekap-temuan/{findingRecap}/edit', [FindingRecapController::class, 'edit'])->name('finding-recaps.edit');
            Route::put('/rekap-temuan/{findingRecap}', [FindingRecapController::class, 'update'])->name('finding-recaps.update');
            Route::delete('/rekap-temuan/{findingRecap}', [FindingRecapController::class, 'destroy'])->name('finding-recaps.destroy');
            Route::patch('/rekap-temuan/{findingRecap}/status', [FindingRecapController::class, 'updateStatus'])->name('finding-recaps.update-status');
            Route::get('/history-spare-part', [SparepartHistoryController::class, 'index'])->name('sparepart-history.index');
            Route::get('/history-spare-part/export', [SparepartHistoryController::class, 'export'])->name('sparepart-history.export');

            Route::get('/nama-mesin', [MachineNameController::class, 'index'])->name('machine-names.index');
            Route::get('/nama-mesin/export', [MachineNameController::class, 'export'])->name('machine-names.export');
            Route::get('/nama-mesin/{machineName}/edit', [MachineNameController::class, 'edit'])->name('machine-names.edit');
            Route::post('/nama-mesin', [MachineNameController::class, 'store'])->name('machine-names.store');
            Route::put('/nama-mesin/{machineName}', [MachineNameController::class, 'update'])->name('machine-names.update');
            Route::put('/nama-mesin/{machineName}/photos/{photoIndex}', [MachineNameController::class, 'updatePhoto'])->whereNumber('photoIndex')->name('machine-names.photos.update');
            Route::delete('/nama-mesin/{machineName}/photos/{photoIndex}', [MachineNameController::class, 'destroyPhoto'])->whereNumber('photoIndex')->name('machine-names.photos.destroy');
            Route::delete('/nama-mesin/{machineName}', [MachineNameController::class, 'destroy'])->name('machine-names.destroy');

            Route::get('/preventive-checklist', [PreventiveChecklistItemController::class, 'index'])->name('preventive-checklist.index');
            Route::get('/preventive-checklist/export', [PreventiveChecklistItemController::class, 'export'])->name('preventive-checklist.export');
            Route::post('/preventive-checklist/kategori', [PreventiveChecklistItemController::class, 'storeCategory'])->name('preventive-checklist.categories.store');
            Route::put('/preventive-checklist/kategori', [PreventiveChecklistItemController::class, 'updateCategory'])->name('preventive-checklist.categories.update');
            Route::delete('/preventive-checklist/kategori', [PreventiveChecklistItemController::class, 'destroyCategory'])->name('preventive-checklist.categories.destroy');
            Route::post('/preventive-checklist', [PreventiveChecklistItemController::class, 'store'])->name('preventive-checklist.store');
            Route::put('/preventive-checklist/{preventiveChecklistItem}', [PreventiveChecklistItemController::class, 'update'])->name('preventive-checklist.update');
            Route::delete('/preventive-checklist/{preventiveChecklistItem}', [PreventiveChecklistItemController::class, 'destroy'])->name('preventive-checklist.destroy');

            Route::get('/mold/export', [MoldController::class, 'export'])->name('molds.export');
            Route::get('/mold', [MoldController::class, 'index'])->name('molds.index');
            Route::post('/mold', [MoldController::class, 'store'])->name('molds.store');
            Route::put('/mold/{mold}', [MoldController::class, 'update'])->name('molds.update');
            Route::delete('/mold/{mold}', [MoldController::class, 'destroy'])->name('molds.destroy');

            Route::get('/robot/export', [RobotController::class, 'export'])->name('robots.export');
            Route::get('/robot', [RobotController::class, 'index'])->name('robots.index');
            Route::post('/robot', [RobotController::class, 'store'])->name('robots.store');
            Route::put('/robot/equipment/{equipment}', [RobotController::class, 'updateEquipment'])->name('robots.equipments.update');
            Route::delete('/robot/equipment/{equipment}', [RobotController::class, 'destroyEquipment'])->name('robots.equipments.destroy');
            Route::put('/robot/{robot}', [RobotController::class, 'update'])->name('robots.update');
            Route::delete('/robot/{robot}', [RobotController::class, 'destroy'])->name('robots.destroy');

            Route::get('/kategori-kerusakan', [DamageCategoryController::class, 'index'])->name('damage-categories.index');
            Route::post('/kategori-kerusakan', [DamageCategoryController::class, 'store'])->name('damage-categories.store');
            Route::put('/kategori-kerusakan/{damageCategory}', [DamageCategoryController::class, 'update'])->name('damage-categories.update');
            Route::delete('/kategori-kerusakan/{damageCategory}', [DamageCategoryController::class, 'destroy'])->name('damage-categories.destroy');
        });

        Route::middleware('admin.users')->group(function () {
            Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
            Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
            Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('/pengguna/{user}/reset-mfa', [UserController::class, 'resetTwoFactor'])->name('users.reset-mfa');
            Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            Route::get('/teknisi/status', [TechnicianController::class, 'status'])->name('technicians.status');
            Route::get('/teknisi', [TechnicianController::class, 'index'])->name('technicians.index');
            Route::post('/teknisi', [TechnicianController::class, 'store'])->name('technicians.store');
            Route::put('/teknisi/{technician}', [TechnicianController::class, 'update'])->name('technicians.update');
            Route::delete('/teknisi/{technician}', [TechnicianController::class, 'destroy'])->name('technicians.destroy');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
