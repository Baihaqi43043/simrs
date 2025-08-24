<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            width: 58mm;
            margin: 0 auto;
            padding: 2mm;
            background: white;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }

        .hospital {
            font-size: 9px;
            font-weight: bold;
        }

        .address {
            font-size: 7px;
        }

        .divider {
            border-top: 1px dotted #000;
            margin: 2mm 0;
        }

        .title {
            font-size: 8px;
            font-weight: bold;
            padding: 1mm 0;
        }

        .antrian {
            font-size: 14px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 2mm;
            margin: 2mm 0;
        }

        .info {
            font-size: 7px;
            line-height: 1.3;
        }

        .row {
            margin-bottom: 0.5mm;
        }

        .label {
            display: inline-block;
            width: 15mm;
        }

        .footer {
            font-size: 6px;
            margin-top: 3mm;
            border-top: 1px dotted #000;
            padding-top: 1mm;
        }

        @page {
            size: 58mm auto;
            margin: 0;
        }

        @media print {
            body {
                width: 58mm;
                margin: 0;
                padding: 1mm;
            }
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="hospital">RS UMUM SIMRS</div>
        <div class="address">Jl. Kesehatan No. 123</div>
        <div class="address">Langsa, Aceh</div>
        <div class="address">Telp: 0641-12345678</div>
    </div>

    <div class="divider"></div>

    <div class="center title">BUKTI PENDAFTARAN</div>

    <div class="divider"></div>

    @if($kunjungan->no_antrian)
    <div class="center antrian">
        ANTRIAN: {{ $kunjungan->no_antrian }}
    </div>
    <div class="divider"></div>
    @endif

    <div class="info">
        <div class="row">
            <span class="label">No.Kunjungan</span>: {{ $kunjungan->no_kunjungan }}
        </div>
        <div class="row">
            <span class="label">Tanggal</span>: {{ \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d/m/Y') }}
        </div>
        <div class="row">
            <span class="label">Pasien</span>: {{ $kunjungan->pasien->nama ?? '-' }}
        </div>
        <div class="row">
            <span class="label">No.RM</span>: {{ $kunjungan->pasien->no_rm ?? '-' }}
        </div>
        <div class="row">
            <span class="label">Poli</span>: {{ $kunjungan->poli->nama_poli ?? '-' }}
        </div>
        <div class="row">
            <span class="label">Dokter</span>: {{ $kunjungan->dokter->nama_dokter ?? '-' }}
        </div>
        <div class="row">
            <span class="label">Status</span>: {{ ucfirst(str_replace('_', ' ', $kunjungan->status)) }}
        </div>
    </div>

    <div class="footer center">
        <div>{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        <div>Simpan bukti ini</div>
        <div>{{ $kunjungan->no_kunjungan }}</div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
