<?php

namespace App\Support;

use App\Models\Technician;

class PicSession
{
    public const KEY = 'pic_technician_id';

    public const USER_KEY = 'pic_user_id';

    public const CLOSE_KEY = 'pic_close_ticket_id';

    public const FORM2_UNLOCK_KEY = 'form2_unlocked_report_id';

    public static function id(): ?int
    {
        if ((int) session(self::USER_KEY) !== (int) auth()->id()) {
            return null;
        }

        $id = session(self::KEY);

        return $id ? (int) $id : null;
    }

    public static function technician(): ?Technician
    {
        $id = self::id();
        if (! $id) {
            return null;
        }

        return Technician::query()->active()->find($id);
    }

    public static function set(Technician $technician): void
    {
        session([
            self::KEY => $technician->id,
            self::USER_KEY => auth()->id(),
        ]);
        self::clearCloseAuthorization();
    }

    public static function matches(?int $technicianId): bool
    {
        return $technicianId !== null && self::id() === (int) $technicianId;
    }

    public static function authorizeClose(int $ticketId): void
    {
        session([self::CLOSE_KEY => $ticketId]);
    }

    public static function isCloseAuthorized(?int $ticketId): bool
    {
        return $ticketId !== null
            && self::id() !== null
            && (int) session(self::CLOSE_KEY) === (int) $ticketId;
    }

    public static function clearCloseAuthorization(): void
    {
        session()->forget(self::CLOSE_KEY);
    }

    public static function unlockForm2(int $reportId): void
    {
        session([self::FORM2_UNLOCK_KEY => $reportId]);
    }

    public static function isForm2Unlocked(?int $reportId): bool
    {
        return $reportId !== null && (int) session(self::FORM2_UNLOCK_KEY) === (int) $reportId;
    }

    public static function lockForm2(): void
    {
        session()->forget(self::FORM2_UNLOCK_KEY);
    }
}
