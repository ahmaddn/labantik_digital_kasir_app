@php
    $rawNote = $debt->note;
    $originalNote = $rawNote;
    $deletionDetails = [
        'date' => '-',
        'user' => '-',
        'reason' => '-'
    ];

    if (preg_match('/\[Dihapus pd (.*?) oleh (.*?)\. Alasan: (.*?)\]/', $rawNote, $matches)) {
        $originalNote = trim(str_replace($matches[0], '', $rawNote));
        $deletionDetails['date'] = $matches[1];
        $deletionDetails['user'] = $matches[2];
        $deletionDetails['reason'] = $matches[3];
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Pertanggungjawaban Penghapusan Hutang</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; color: #000; line-height: 1.6; font-size: 14px; background: #fff; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px; }
        .kop-title { font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .kop-subtitle { font-size: 12px; margin: 5px 0 0 0; font-style: italic; }
        .doc-title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-top: 15px; margin-bottom: 5px; }
        .doc-number { text-align: center; font-size: 11px; margin-bottom: 30px; font-family: monospace; }
        .table-details { width: 100%; border-collapse: collapse; margin: 25px 0; }
        .table-details td { padding: 10px 12px; vertical-align: top; border: 1px solid #000; }
        .table-details td.label { font-weight: bold; width: 30%; background: #f2f2f2; text-transform: uppercase; font-size: 12px; }
        .statement { border: 1px solid #000; padding: 15px; background: #fafafa; font-style: italic; margin-top: 30px; margin-bottom: 40px; text-align: justify; }
        .signature-section { margin-top: 60px; width: 100%; }
        .sig-table { width: 100%; border: none; }
        .sig-table td { border: none; width: 50%; text-align: center; }
        .sig-space { height: 80px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <div class="kop-title">TEACHING FACTORY (TEFA) LABANTIK</div>
        <div class="kop-title" style="font-size:14px;">APLIKASI DIGITAL KASIR &amp; KEUANGAN</div>
        <div class="kop-subtitle">Sistem Pencatatan Transaksi &amp; Pengelolaan Keuangan Jurusan</div>
    </div>
    
    <div class="doc-title">SURAT PERNYATAAN PERTANGGUNGJAWABAN PENGHAPUSAN HUTANG</div>
    <div class="doc-number">Nomor Dokumen: SPPH-{{ strtoupper(substr($debt->id, 0, 8)) }}/{{ date('Y') }}</div>
    
    <p>Yang bertanda tangan di bawah ini menerangkan dengan sebenar-benarnya bahwa transaksi hutang toko/kasir berikut ini telah resmi dihapus dari sistem pencatatan:</p>
    
    <table class="table-details">
        <tr>
            <td class="label">Kreditor / Supplier</td>
            <td>{{ $debt->creditor_name }}</td>
        </tr>
        <tr>
            <td class="label">Nominal Hutang</td>
            <td>Rp{{ number_format($debt->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Berhutang</td>
            <td>{{ $debt->date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Catatan Asli</td>
            <td>{{ $originalNote ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Dihapus Oleh</td>
            <td>{{ $deletionDetails['user'] }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Dihapus</td>
            <td>{{ $deletionDetails['date'] }}</td>
        </tr>
        <tr>
            <td class="label">Alasan Penghapusan</td>
            <td style="font-weight: bold; color: #d00;">{{ $deletionDetails['reason'] }}</td>
        </tr>
    </table>
    
    <div class="statement">
        <strong>Pernyataan Integritas Data Keuangan:</strong><br>
        &ldquo;Saya bersaksi secara jujur dan bersedia bertanggung jawab secara penuh baik secara administratif maupun hukum bahwa tindakan penghapusan data hutang ini murni karena kesalahan pencatatan/administrasi dan sama sekali bukan merupakan tindakan manipulasi finansial atau penyalahgunaan wewenang.&rdquo;
    </div>
    
    <div class="signature-section">
        <table class="sig-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Pengelola Jurusan</strong>
                    <div class="sig-space"></div>
                    <div class="sig-name">( ________________________ )</div>
                </td>
                <td>
                    Yang Menyatakan,<br>
                    <strong>Petugas Kasir / Pemohon</strong>
                    <div class="sig-space"></div>
                    <div class="sig-name">( {{ $deletionDetails['user'] }} )</div>
                </td>
            </tr>
        </table>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
