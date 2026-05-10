<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $nota->nomor_nota }}</title>
    <style>
        /* Gaya CSS khusus untuk struk kasir */
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm; /* Ukuran printer thermal standar */
            margin: 0 auto;
            padding: 10px;
            color: #000;
        }
        .header, .footer { text-align: center; margin-bottom: 15px; }
        .toko-nama { font-size: 1.2rem; font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .text-right { text-align: right; }
        .item-name { display: block; font-size: 0.9rem; margin-top: 5px; }
    </style>
</head>
<body onload="window.print()"> <div class="header">
        <div class="toko-nama">TOKOKU</div>
        <div>Jl. Sumber Sari Gg. 1 No.45, RT.002/RW.001, Sumbersari, Kec. Lowokwaru, Kota Malang, Jawa Timur</div>
        <div class="divider"></div>
        <div style="text-align: left;">
            <div>No  : {{ $nota->nomor_nota }}</div>
            <div>Tgl : {{ \Carbon\Carbon::parse($nota->tanggal)->format('d/m/Y H:i') }}</div>
            <div>Ksr : {{ $nota->karyawan->nama ?? 'Admin' }}</div>
            @if($nota->id_pelanggan)
            <div>Plg : {{ $nota->pelanggan->nama }}</div>
            @endif
        </div>
    </div>

    <div class="divider"></div>

    <table>
        @foreach($nota->item as $it)
        <tr>
            <td colspan="3"><span class="item-name">{{ $it->barang->nama }}</span></td>
        </tr>
        <tr>
            <td>{{ $it->jumlah }} x</td>
            <td>{{ number_format($it->harga_jual_saat_itu, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($it->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Total Belanja</td>
            <td class="text-right">{{ number_format($nota->total_belanja, 0, ',', '.') }}</td>
        </tr>
        @if($nota->diskon > 0)
        <tr>
            <td>Diskon</td>
            <td class="text-right">-{{ number_format($nota->diskon, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>TOTAL AKHIR</strong></td>
            <td class="text-right"><strong>{{ number_format($nota->total_akhir, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>Pembayaran</td>
            <td class="text-right">{{ strtoupper($nota->metode_bayar) }}</td>
        </tr>
    </table>

    <div class="divider"></div>
    
    <div class="footer">
        <p>Terima Kasih Telah Berbelanja!</p>
        <p style="font-size: 0.8rem;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>

</body>
</html>