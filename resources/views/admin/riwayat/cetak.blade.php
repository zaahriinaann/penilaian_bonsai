<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Nilai Bonsai</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
        }

        .kop img {
            height: 70px;
            margin-bottom: 5px;
        }

        .kop h2 {
            margin: 0;
            font-size: 18px;
        }

        .kop small {
            font-size: 12px;
        }

        h4 {
            margin-top: 10px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }

        td.left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="kop">
        <img src="{{ public_path('assets/media/logos/logo-ppbi-nobg.png') }}" alt="Logo PPBI">
        <h2>Perkumpulan Penggemar Bonsai Indonesia (PPBI)</h2>
        <small>Cabang Cirebon</small>
    </div>

    <h4>Laporan Rekap Nilai Bonsai</h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pohon</th>
                <th>Pemilik</th>
                <th>Skor Akhir</th>
                <th>Himpunan Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekapData as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="left">{{ $item['nama_pohon'] }}</td>
                    <td class="left">{{ $item['pemilik'] }}</td>
                    <td>{{ number_format($item['skor_akhir'], 2) }}</td>
                    <td>{{ $item['himpunan_akhir'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
