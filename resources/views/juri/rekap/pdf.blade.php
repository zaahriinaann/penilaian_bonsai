<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Bonsai</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop h2 {
            margin: 0;
            font-size: 18px;
        }

        .kop small {
            display: block;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        td,
        th {
            border: 1px solid #333;
            padding: 6px;
        }

        .kategori {
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="kop">
        <img src="{{ public_path('assets/media/logos/logo-ppbi-nobg.png') }}" alt="Logo PPBI" height="80">
        <h2>Perkumpulan Penggemar Bonsai Indonesia (PPBI)</h2>
        <small>Cabang Cirebon</small>
    </div>

    <h4>Detail Penilaian Bonsai</h4>
    <table>
        <tr>
            <th>No Juri</th>
            <td>{{ $detail['nomor_juri'] }}</td>
        </tr>
        <tr>
            <th>No Pendaftaran</th>
            <td>{{ $detail['nomor_pendaftaran'] }}</td>
        </tr>
        <tr>
            <th>Nama Pohon</th>
            <td>{{ $detail['nama_pohon'] }}</td>
        </tr>
        <tr>
            <th>Pemilik</th>
            <td>{{ $detail['pemilik'] }}</td>
        </tr>
        <tr>
            <th>Skor Akhir</th>
            <td><strong>{{ number_format($detail['skor_akhir'], 2) }}</strong></td>
        </tr>
        <tr>
            <th>Himpunan Akhir</th>
            <td><strong>{{ $detail['himpunan_akhir'] }}</strong></td>
        </tr>
    </table>

    <h4 class="kategori">Detail per Kategori:</h4>
    <table>
        <thead>
            <tr>
                <th>Kriteria</th>
                <th>Nilai</th>
                <th>Himpunan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail['kategori'] as $kriteria => $list)
                @php
                    $avg = collect($list)->pluck('hasil')->avg();
                    $himpunan = collect($list)->groupBy('himpunan')->sortByDesc(fn($x) => count($x))->keys()->first();
                @endphp
                <tr>
                    <td>{{ $kriteria }}</td>
                    <td>{{ number_format($avg, 2) }}</td>
                    <td>{{ $himpunan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
