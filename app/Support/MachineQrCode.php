<?php

namespace App\Support;

use App\Models\MachineName;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use GdImage;

class MachineQrCode
{
    public static function token(int $machineNumber): string
    {
        return hash_hmac('sha256', 'machine-scan:v1:'.$machineNumber, (string) config('app.key'));
    }

    public static function isValidToken(int $machineNumber, string $token): bool
    {
        return hash_equals(self::token($machineNumber), $token);
    }

    public static function url(int $machineNumber): string
    {
        return route('scan.machines.show', [
            'machineNumber' => $machineNumber,
            'token' => self::token($machineNumber),
        ]);
    }

    public static function png(int $machineNumber, int $size = 480, bool $withCaption = true): string
    {
        $png = self::build($machineNumber, $size)->getString();

        if (! $withCaption) {
            return $png;
        }

        return self::withCaption($png, self::captionLines($machineNumber));
    }

    public static function build(int $machineNumber, int $size = 480): ResultInterface
    {
        $builder = new Builder(
            writer: new PngWriter(),
            data: self::url($machineNumber),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: $size,
            margin: 16,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return $builder->build();
    }

    /** @return list<string> */
    public static function captionLines(int $machineNumber): array
    {
        $machine = MachineName::query()
            ->where('machine_number', $machineNumber)
            ->first();

        if ($machine) {
            return $machine->qrCaptionLines();
        }

        return [
            'No Asset: —',
            'Brand: —',
            'Tonage: —',
            'Machine Model: —',
        ];
    }

    public static function filename(int $machineNumber): string
    {
        return 'qr-mesin-'.$machineNumber.'.png';
    }

    /**
     * @param  list<string>  $lines
     */
    private static function withCaption(string $png, array $lines): string
    {
        $source = imagecreatefromstring($png);
        if (! $source instanceof GdImage) {
            return $png;
        }

        $qrWidth = imagesx($source);
        $qrHeight = imagesy($source);
        $font = new OpenSans(15);
        $fontPath = $font->getPath();
        $fontSize = $font->getSize();
        $lineHeight = (int) round($fontSize * 1.45);
        $paddingX = 18;
        $paddingTop = 10;
        $paddingBottom = 18;
        $captionHeight = $paddingTop + (count($lines) * $lineHeight) + $paddingBottom;
        $canvasHeight = $qrHeight + $captionHeight;

        $canvas = imagecreatetruecolor($qrWidth, $canvasHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        $black = imagecolorallocate($canvas, 15, 23, 42);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $source, 0, 0, 0, 0, $qrWidth, $qrHeight);

        $y = $qrHeight + $paddingTop + $fontSize;
        foreach ($lines as $line) {
            $size = self::fittingFontSize($fontPath, $fontSize, $line, $qrWidth - ($paddingX * 2));
            $box = imagettfbbox($size, 0, $fontPath, $line);
            $textWidth = is_array($box) ? (int) ($box[2] - $box[0]) : 0;
            $x = (int) max($paddingX, ($qrWidth - $textWidth) / 2);
            imagettftext($canvas, $size, 0, $x, $y, $black, $fontPath, $line);
            $y += $lineHeight;
        }

        ob_start();
        imagepng($canvas);
        $output = (string) ob_get_clean();

        imagedestroy($source);
        imagedestroy($canvas);

        return $output !== '' ? $output : $png;
    }

    private static function fittingFontSize(string $fontPath, int $preferred, string $text, int $maxWidth): int
    {
        for ($size = $preferred; $size >= 10; $size--) {
            $box = imagettfbbox($size, 0, $fontPath, $text);
            if (! is_array($box)) {
                return $preferred;
            }
            if (($box[2] - $box[0]) <= $maxWidth) {
                return $size;
            }
        }

        return 10;
    }
}
