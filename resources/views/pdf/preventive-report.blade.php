<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px 32px 36px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        .letterhead { width: 100%; border-collapse: collapse; border-bottom: 3px solid #0f766e; margin-bottom: 10px; }
        .letterhead td { padding: 0 0 8px 0; vertical-align: top; text-align: left; }
        .logo { display: block; height: 40px; width: 232px; margin: 0 0 4px 0; padding: 0; border: 0; }
        .company { font-size: 16px; font-weight: bold; color: #1e3a8a; letter-spacing: 0.4px; }
        .tagline { font-size: 10px; color: #15803d; font-weight: bold; margin-top: 1px; }
        .address { font-size: 8.5px; color: #334155; line-height: 1.35; margin-top: 3px; }
        .doc-title { font-size: 13px; font-weight: bold; color: #0f766e; text-align: right; }
        .doc-no { font-size: 9px; text-align: right; color: #475569; margin-top: 2px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.meta td { padding: 2px 6px 2px 0; vertical-align: top; }
        table.meta .label { width: 120px; color: #64748b; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: #0f766e; color: #fff; font-size: 8px; text-align: left; padding: 4px 5px; }
        table.items td { border-bottom: 1px solid #e2e8f0; padding: 3px 5px; font-size: 8px; vertical-align: top; }
        table.items tr.group td { background: #ecfdf5; font-weight: bold; color: #065f46; }
        .notes { margin-top: 8px; font-size: 9px; }
        table.signs { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.signs td { width: 25%; border: 1px solid #cbd5e1; text-align: center; padding: 8px 6px 10px; vertical-align: top; }
        .sign-title { font-size: 8px; color: #64748b; }
        .signed { font-size: 13px; font-weight: bold; color: #15803d; margin: 8px 0 2px; letter-spacing: 0.6px; }
        .not-signed { font-size: 13px; font-weight: bold; color: #b91c1c; margin: 8px 0 2px; letter-spacing: 0.4px; }
        .sign-date { font-size: 9px; color: #0f172a; min-height: 12px; }
        .sign-role { font-size: 8px; font-weight: bold; margin-top: 6px; color: #1e293b; }
        .sign-name { font-size: 8px; color: #334155; }
        .footer { margin-top: 12px; font-size: 7.5px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 6px; }
        .evidence-title { margin-top: 14px; font-size: 11px; font-weight: bold; color: #0f766e; }
        table.evidence { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.evidence td { width: 33.33%; padding: 4px 6px 0 0; vertical-align: top; }
        table.evidence img { width: 160px; height: auto; }
    </style>
</head>
<body>
    <table class="letterhead">
        <tr>
            <td>
                @if (filled($logo ?? null))
                    <img class="logo" src="{{ $logo }}" alt="PT Dharma Poliplast" width="232" height="40">
                @else
                    <div class="company">PT. DHARMA POLIPLAST</div>
                    <div class="tagline">Exist To Contribute</div>
                @endif
                <div class="address">
                    Jl. Meranti 1 Blok L1-10, Cikarang Bekasi Indonesia<br>
                    Telp. (021) 55763388, 5520084 &nbsp;&nbsp; Fax. (021) 55763290<br>
                    NPWP. 02.239.576.8-415.000
                </div>
            </td>
            <td style="width: 220px;">
                <div class="doc-title">HASIL PREVENTIVE MESIN</div>
                <div class="doc-no">
                    @if (filled($documentNo))
                        No. Dokumen: {{ $documentNo }}<br>
                    @endif
                    No. PM-{{ $report->id }}<br>{{ $report->work_date?->format('d-m-Y') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td class="label">Mesin</td>
            <td>{{ $report->machine_number }} — {{ $report->machine_name_display }}</td>
            <td class="label">Shift</td>
            <td>{{ $report->work_shift_label }}</td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td>{{ $report->schedule_group_label }}</td>
            <td class="label">Tipe</td>
            <td>{{ $report->category_label }}</td>
        </tr>
        <tr>
            <td class="label">PIC</td>
            <td>{{ $report->pic_name }}</td>
            <td class="label">Hasil</td>
            <td>{{ $report->result_label }}</td>
        </tr>
        <tr>
            <td class="label">Anggota Tim</td>
            <td colspan="3">{{ $report->team_members_label }}</td>
        </tr>
        @if ($equipment)
            <tr>
                <td class="label">{{ $equipment[0] }}</td>
                <td colspan="3">{{ $equipment[1] }}</td>
            </tr>
        @endif
        @if (filled($report->penggunaan_sparepart))
            <tr>
                <td class="label">Sparepart</td>
                <td colspan="3">{!! nl2br(e($report->penggunaan_sparepart)) !!}</td>
            </tr>
        @endif
        @if (filled($report->notes))
            <tr>
                <td class="label">Catatan</td>
                <td colspan="3">{{ $report->notes }}</td>
            </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th>Item Pemeriksaan</th>
                <th>Standar</th>
                <th style="width: 70px;">Isi Manual</th>
                <th style="width: 70px;">Hasil</th>
                <th style="width: 90px;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $currentGroup = null; @endphp
            @foreach ($rows as $row)
                @if ($currentGroup !== $row['group'])
                    @php $currentGroup = $row['group']; @endphp
                    <tr class="group">
                        <td colspan="6">{{ $row['group'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td>{{ $row['label'] }}</td>
                    <td>{{ $row['standard'] }}</td>
                    <td>{{ $row['manual'] }}</td>
                    <td>{{ $row['result'] }}</td>
                    <td>{{ $row['remark'] ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signs">
        <tr>
            @foreach ($signatures as $signature)
                <td>
                    <div class="sign-title">{{ $signature['title'] }}</div>
                    <div class="{{ $signature['signed'] ? 'signed' : 'not-signed' }}">{{ $signature['signed'] ? 'SIGNED' : 'NOT SIGNED' }}</div>
                    <div class="sign-date">{{ $signature['date'] !== '' ? $signature['date'] : '—' }}</div>
                    <div class="sign-role">{{ $signature['role'] }}</div>
                    @if ($signature['name'] !== '')
                        <div class="sign-name">{{ $signature['name'] }}</div>
                    @endif
                </td>
            @endforeach
        </tr>
    </table>

    @if (! empty($evidences ?? []))
        <div class="evidence-title">Lampiran</div>
        <table class="evidence">
            @foreach (array_chunk($evidences, 3) as $row)
                <tr>
                    @foreach ($row as $image)
                        <td><img src="{{ $image }}" width="160" alt="Lampiran evidence"></td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    @endif

    <div class="footer">
        Dokumen ini diterbitkan oleh sistem e-Maintenance dan diakui sebagai dokumen preventive yang sah oleh PT Dharma Poliplast.
        Approval yang belum selesai ditandai NOT SIGNED. Approval yang sudah selesai ditandai SIGNED.
    </div>
</body>
</html>
