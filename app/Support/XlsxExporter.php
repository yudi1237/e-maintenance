<?php

namespace App\Support;

use ZipArchive;

class XlsxExporter
{
    /** @var list<list<scalar|null>> */
    private array $rows = [];

    private string $sheetName = 'Sheet1';

    /**
     * @param  list<scalar|null>  $cells
     */
    public function addRow(array $cells): self
    {
        $this->rows[] = $cells;

        return $this;
    }

    public function setSheetName(string $name): self
    {
        $this->sheetName = mb_substr($name, 0, 31);

        return $this;
    }

    public function download(string $filename): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($tmp === false) {
            throw new \RuntimeException('Tidak dapat membuat file sementara untuk export Excel.');
        }

        $path = $tmp.'.xlsx';

        if (! rename($tmp, $path)) {
            @unlink($tmp);
            throw new \RuntimeException('Tidak dapat menyiapkan file export Excel.');
        }

        try {
            $this->writeToFile($path);
            $safeName = str_replace(['"', "\r", "\n"], '', $filename);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$safeName.'"');
            header('Cache-Control: max-age=0');
            header('Content-Length: '.(string) filesize($path));

            readfile($path);
        } finally {
            @unlink($path);
        }
    }

    public function writeToFile(string $path): void
    {
        $zip = new ZipArchive;

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Tidak dapat membuat arsip Excel (.xlsx).');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('docProps/app.xml', $this->appXml());
        $zip->addFromString('docProps/core.xml', $this->coreXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->stylesXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml());

        $zip->close();
    }

    private function contentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    private function rootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function workbookXml(): string
    {
        $name = htmlspecialchars($this->sheetName, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="{$name}" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
    }

    private function workbookRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    private function stylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>
    <fills count="1"><fill><patternFill patternType="none"/></fill></fills>
    <borders count="1"><border/></borders>
    <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
    <cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>
</styleSheet>
XML;
    }

    private function coreXml(): string
    {
        $created = gmdate('Y-m-d\TH:i:s\Z');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>e-Maintenance</dc:creator>
    <dcterms:created xsi:type="dcterms:W3CDTF">{$created}</dcterms:created>
</cp:coreProperties>
XML;
    }

    private function appXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>e-Maintenance</Application>
</Properties>
XML;
    }

    private function sheetXml(): string
    {
        $rowsXml = '';

        foreach ($this->rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $cellsXml = '';

            foreach ($row as $colIndex => $value) {
                $cellRef = $this->columnLetter($colIndex).$rowNumber;
                $cellsXml .= $this->cellXml($cellRef, $value);
            }

            $rowsXml .= '<row r="'.$rowNumber.'">'.$cellsXml.'</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData>'.$rowsXml.'</sheetData>'
            .'</worksheet>';
    }

    private function cellXml(string $cellRef, mixed $value): string
    {
        if ($value === null || $value === '') {
            return '<c r="'.$cellRef.'"/>';
        }

        if (is_int($value) || is_float($value)) {
            return '<c r="'.$cellRef.'"><v>'.$value.'</v></c>';
        }

        if (is_string($value) && is_numeric($value) && strlen($value) < 12 && ! str_contains($value, ' ')) {
            return '<c r="'.$cellRef.'"><v>'.$value.'</v></c>';
        }

        $text = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return '<c r="'.$cellRef.'" t="inlineStr"><is><t>'.$text.'</t></is></c>';
    }

    private function columnLetter(int $index): string
    {
        $index++;
        $letters = '';

        while ($index > 0) {
            $index--;
            $letters = chr(65 + ($index % 26)).$letters;
            $index = intdiv($index, 26);
        }

        return $letters;
    }
}
