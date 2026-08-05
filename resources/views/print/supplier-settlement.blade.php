<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Settlement Supplier - {{ $supplier->name }}</title>
    <script src="{{ asset('js/html2canvas-pro.min.js') }}"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.5;
            font-size: 13px;
            background: #fff;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .action-bar {
            display: flex;
            gap: 12px;
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 16px;
            margin-bottom: 30px;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e2e8f0;
        }
        .action-info {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-print {
            background: #0f172a;
            color: #fff;
        }
        .btn-print:hover {
            background: #1e293b;
        }
        .btn-whatsapp {
            background: #128c7e;
            color: #fff;
        }
        .btn-whatsapp:hover {
            background: #075e54;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #0f172a;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .kop-title {
            font-size: 20px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #0f172a;
        }
        .kop-subtitle {
            font-size: 12px;
            margin: 5px 0 0 0;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .invoice-title-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .invoice-number {
            font-family: monospace;
            font-size: 12px;
            color: #64748b;
            margin-top: 5px;
        }
        .meta-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .meta-box {
            width: 48%;
        }
        .meta-box h3 {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .meta-value {
            font-size: 13px;
            color: #0f172a;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table-items th {
            background: #f8fafc;
            border-bottom: 2px solid #cbd5e1;
            padding: 12px 10px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 0.5px;
        }
        .table-items td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .table-items tr:last-child td {
            border-bottom: 2px solid #cbd5e1;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .summary-table {
            width: 300px;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 10px;
        }
        .summary-table tr.grand-total td {
            border-top: 2px solid #0f172a;
            font-weight: bold;
            font-size: 15px;
            color: #0f172a;
            padding-top: 12px;
        }
        .note-box {
            background: #f8fafc;
            border-left: 4px solid #cbd5e1;
            padding: 15px;
            font-size: 11px;
            color: #64748b;
            border-radius: 4px;
            margin-bottom: 40px;
            text-align: justify;
        }
        /* Premium custom modal style */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .custom-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .custom-modal-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 35px;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            transform: scale(0.9) translateY(20px);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .custom-modal-overlay.active .custom-modal-card {
            transform: scale(1) translateY(0);
        }
        .custom-modal-icon {
            width: 64px;
            height: 64px;
            background: #e6f7ed;
            color: #12b76a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .custom-modal-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .custom-modal-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .custom-modal-btn {
            background: #128c7e;
            color: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 14px 28px;
            font-weight: 750;
            font-size: 13px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .custom-modal-btn:hover {
            background: #075e54;
        }
        @media print {
            body {
                padding: 0;
                color: #000;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    @php
        $activeJurusanId = session('active_jurusan_id');
        $themeSettings = null;
        if ($activeJurusanId) {
            $jurusanModel = \App\Models\Jurusan::find($activeJurusanId);
            if ($jurusanModel && $jurusanModel->theme_settings) {
                $themeSettings = $jurusanModel->theme_settings;
            }
        }
        $tefaName = $themeSettings['tefa_name'] ?? 'TEFA LABANTIK';
        $tefaLogo = $themeSettings['tefa_logo'] ?? '';
        $docPrefixInvoice = $themeSettings['doc_prefix_invoice'] ?? 'INV-SUP';

        $totalShare = 0;
        $totalQty = 0;
        foreach($products as $product) {
            $totalShare += $product->total_modal;
            $totalQty += $product->sold_qty;
        }

        // WhatsApp Link Generator
        $whatsappUrl = '';
        if (!empty($supplier->contact)) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $supplier->contact);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            
            $message = "Halo *" . $supplier->name . "*, berikut adalah Bukti Pembayaran / Bagi Hasil Barang Titipan " . $tefaName . " untuk periode *" . \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') . " - " . \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') . "*.\n\n"
                     . "Total Pembayaran: *Rp" . number_format($totalShare, 0, ',', '.') . "*.\n";
                     // . "Detail Invoice Lengkap: " . request()->fullUrl();
                     
            $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($message);
        }
    @endphp

    <!-- Action Toolbar (Hidden on Print) -->
    <div class="action-bar no-print">
        <div class="action-info">
            Tindakan Dokumen ({{ $docPrefixInvoice }})
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-action btn-print">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229-2.523a1.125 1.125 0 011.12-1.012M17.66 18a2.25 2.25 0 01-2.25 2.25H8.59A2.25 2.25 0 016.34 18m0 0l-.229-2.523a1.125 1.125 0 01-1.12-1.012M6.071 6.341c.063-.04.129-.079.196-.118a1.875 1.875 0 012.335.297l.006.007m1.244-1.137a1.875 1.875 0 012.335-.296c.067.04.133.079.196.118m-.196-.118L10.5 8.5m7.429-2.159l-4.172 4.172m0 0a1.125 1.125 0 01-1.591-1.591l4.172-4.172z"></path></svg>
                Cetak Invoice
            </button>
            @if($whatsappUrl)
                <button onclick="shareToWhatsApp('{{ $whatsappUrl }}')" class="btn-action btn-whatsapp">
                    <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.004 2c-5.51 0-9.99 4.48-9.99 9.99 0 2.05.62 3.96 1.7 5.56L2.3 22l4.62-1.37c1.51.91 3.27 1.44 5.08 1.44 5.51 0 10-4.48 10-9.99S17.514 2 12.004 2zm5.83 14.28c-.24.67-1.19 1.25-1.63 1.29-.44.04-.97.19-2.92-.58-2.49-.99-4.08-3.53-4.2-3.7-.12-.17-1.01-1.34-1.01-2.56 0-1.22.64-1.82.87-2.06.23-.24.51-.3.67-.3.16 0 .32 0 .46.01.15.01.35-.06.55.42.2.49.69 1.68.75 1.8.06.12.1.27.02.43-.08.16-.18.27-.3.41-.12.14-.26.31-.37.42-.12.12-.25.26-.11.51.14.25.64 1.05 1.37 1.7.94.84 1.73 1.1 1.97 1.22.24.12.38.1.52-.06.14-.16.61-.71.77-.96.16-.24.32-.2.53-.12.21.08 1.35.63 1.58.75.23.12.39.18.45.28.06.1.06.57-.18 1.24z"/></svg>
                    Kirim WA &amp; Tandai Lunas
                </button>
            @endif
        </div>
    </div>

    <div id="invoice-card" style="background:#fff; padding: 25px 35px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 10px; box-sizing: border-box;">
        <!-- Kop Surat -->
        <div class="kop-surat" style="display: flex; align-items: center; justify-content: center; gap: 20px;">
            @if($tefaLogo)
                <img src="{{ asset('storage/' . $tefaLogo) }}" alt="Logo" style="height: 60px; max-width: 150px; object-fit: contain;">
            @endif
            <div style="text-align: {{ $tefaLogo ? 'left' : 'center' }};">
                <div class="kop-title">{{ $tefaName }}</div>
                <div class="kop-subtitle">Aplikasi Penjualan &amp; Keuangan Digital RPL SMART</div>
            </div>
        </div>

        <!-- Title Bar -->
        <div class="invoice-title-bar">
            <div>
                <h1 class="invoice-title">INVOICE SETTLEMENT SUPPLIER</h1>
                <div class="invoice-number">No. Dokumen: {{ $docPrefixInvoice }}/{{ strtoupper(substr($supplier->id, 0, 8)) }}/{{ date('Ymd') }}</div>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="meta-container">
            <div class="meta-box">
                <h3>Diberikan Kepada (Supplier):</h3>
                <div class="meta-value">
                    <strong>{{ $supplier->name }}</strong><br>
                    <span>Kontak: {{ $supplier->contact ?? '-' }}</span><br>
                    <span>Alamat: {{ $supplier->address ?? '-' }}</span>
                </div>
            </div>
            <div class="meta-box">
                <h3>Rincian Pembayaran:</h3>
                <div class="meta-value">
                    <span>Tanggal Settle: <strong>{{ now()->translatedFormat('d F Y') }}</strong></span><br>
                    <span>Periode Transaksi: <strong>{{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table-items">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">No</th>
                    <th style="width: 45%;">Nama Produk</th>
                    <th class="text-center" style="width: 12%;">Stok Awal</th>
                    <th class="text-center" style="width: 12%;">Sisa Stok</th>
                    <th class="text-center" style="width: 12%;">Terjual</th>
                    <th class="text-right" style="width: 18%;">Harga HPP</th>
                    <th class="text-right" style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach($products as $product)
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                        </td>
                        <td class="text-center">{{ $product->opening_stock }}</td>
                        <td class="text-center">{{ $product->closing_stock }}</td>
                        <td class="text-center"><strong>{{ $product->sold_qty }}</strong></td>
                        <td class="text-right">Rp{{ number_format($product->modal_price, 0, ',', '.') }}</td>
                        <td class="text-right"><strong>Rp{{ number_format($product->total_modal, 0, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-container">
            <table class="summary-table">
                <tr>
                    <td>Total Item Terjual</td>
                    <td class="text-right"><strong>{{ $totalQty }} Unit</strong></td>
                </tr>
                <tr class="grand-total">
                    <td>Total Pembayaran</td>
                    <td class="text-right">Rp{{ number_format($totalShare, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Note Box -->
        <div class="note-box">
            <strong>Bukti Pembayaran Konsinyasi:</strong><br>
            Dokumen ini merupakan bukti pembayaran resmi atas penyerahan dana bagi hasil/settlement barang konsinyasi antara Pihak Sekolah (TEFA LABANTIK) dengan Pihak Supplier mitra. Rincian stok awal dan sisa stok dicocokkan berdasarkan pencatatan audit stock harian sistem kasir TEFA. Transaksi ini dianggap sah dan telah tercatat secara digital pada sistem keuangan sekolah.
        </div>
    </div>

    <script>
    async function shareToWhatsApp(waUrl) {
        const btn = document.querySelector('.btn-whatsapp');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Menyalin Gambar...';
        
        const invoiceCard = document.getElementById('invoice-card');
        
        // Temporarily style invoiceCard for perfect snapshot output
        const originalBorder = invoiceCard.style.border;
        invoiceCard.style.border = 'none';
        
        try {
            // Call API to mark as paid and record payout in Buku Kas
            try {
                await fetch('{{ route('supplier-settlement.pay', $supplier->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        date_from: '{{ $dateFrom }}',
                        date_to: '{{ $dateTo }}',
                        amount: {{ $totalShare }}
                    })
                });
            } catch (apiErr) {
                console.error("Gagal mencatat pembayaran bagi hasil supplier:", apiErr);
            }

            const h2c = window.html2canvas || window.html2canvasPro;
            if (!h2c) {
                alert('Error: Gagal memuat library perekam gambar.');
                btn.disabled = false;
                btn.innerHTML = originalText;
                return;
            }
            
            const canvas = await h2c(invoiceCard, {
                scale: 2, // better quality
                useCORS: true,
                backgroundColor: '#ffffff',
                onclone: (clonedDoc) => {
                    const clonedInvoice = clonedDoc.getElementById('invoice-card');
                    if (clonedInvoice) {
                        clonedInvoice.style.width = '700px';
                        clonedInvoice.style.minWidth = '700px';
                        clonedInvoice.style.maxWidth = '700px';
                        clonedInvoice.style.padding = '40px';
                        clonedInvoice.style.borderRadius = '16px';
                        clonedInvoice.style.boxShadow = 'none';
                        clonedInvoice.style.border = '1px solid #cbd5e1';
                        clonedInvoice.style.position = 'relative';
                        
                        // Add a beautiful diagonal stamp watermark "LUNAS / PAID"
                        const watermark = clonedDoc.createElement('div');
                        watermark.style.position = 'absolute';
                        watermark.style.right = '45px';
                        watermark.style.top = '120px';
                        watermark.style.border = '4px double #10b981';
                        watermark.style.color = '#10b981';
                        watermark.style.fontSize = '24px';
                        watermark.style.fontWeight = '900';
                        watermark.style.padding = '8px 16px';
                        watermark.style.borderRadius = '8px';
                        watermark.style.transform = 'rotate(-15deg)';
                        watermark.style.textTransform = 'uppercase';
                        watermark.style.opacity = '0.85';
                        watermark.style.letterSpacing = '2px';
                        watermark.style.fontFamily = 'Arial, sans-serif';
                        watermark.textContent = 'LUNAS';
                        clonedInvoice.appendChild(watermark);
                    }
                }
            });
            
            // Restore border style
            invoiceCard.style.border = originalBorder;
            
            canvas.toBlob(async (blob) => {
                try {
                    const item = new ClipboardItem({ "image/png": blob });
                    await navigator.clipboard.write([item]);
                    
                    // Show custom beautiful modal instead of alert
                    const modal = document.getElementById('waModal');
                    const modalBtn = document.getElementById('waModalBtn');
                    
                    // Set up click handler for the modal action button
                    modalBtn.onclick = function() {
                        window.open(waUrl, '_blank');
                        modal.classList.remove('active');
                    };
                    
                    // Activate modal
                    modal.classList.add('active');
                } catch (err) {
                    console.error(err);
                    alert('Gagal menyalin gambar otomatis ke clipboard. Namun chat WhatsApp tetap dibuka.');
                    window.open(waUrl, '_blank');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            }, 'image/png');
        } catch (e) {
            console.error(e);
            alert('Gagal membuat gambar invoice.');
            invoiceCard.style.border = originalBorder;
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
    </script>

    <!-- Premium WhatsApp Modal Overlay -->
    <div id="waModal" class="custom-modal-overlay no-print">
        <div class="custom-modal-card">
            <div class="custom-modal-icon">
                <svg style="width: 32px; height: 32px;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                </svg>
            </div>
            <div class="custom-modal-title">INVOICE DISALIN!</div>
            <div class="custom-modal-desc" style="margin-top: 15px; margin-bottom: 25px; font-weight: bold; font-size: 13px;">
                Gambar invoice telah disalin ke clipboard.<br><br>
                Silakan lakukan **Paste (Ctrl + V)** langsung pada room chat WhatsApp untuk mengirim gambar.
            </div>
            <button id="waModalBtn" class="custom-modal-btn">Buka WhatsApp</button>
        </div>
    </div>
</body>
</html>
