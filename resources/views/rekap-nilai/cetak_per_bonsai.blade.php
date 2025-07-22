<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        h3,
        h4 {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="kop">
        <img src="{{ public_path('assets/media/logos/logo-ppbi-nobg.png') }}" alt="Logo PPBI">
        <h2>Perkumpulan Penggemar Bonsai Indonesia (PPBI)</h2>
        <small>Cabang Cirebon</small>
    </div>

    <h3>Rekap Nilai Bonsai</h3>
    <hr>

    <table>
        <tr>
            <th width="200">Nama Bonsai</th>
            <td>{{ $detail['nama_pohon'] }}</td>
        </tr>
        <tr>
            <th>Kelas/Ukuran</th>
            <td>{{ $detail['kelas'] }} / {{ $detail['ukuran_2'] }}</td>
        </tr>
        <tr>
            <th>Pemilik</th>
            <td>{{ $detail['pemilik'] }}</td>
        </tr>
        <tr>
            <th>No Juri</th>
            <td>{{ $detail['nomor_juri'] }}</td>
        </tr>
        <tr>
            <th>No Pendaftaran</th>
            <td>{{ $detail['nomor_pendaftaran'] }}</td>
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

    <br>
    <h4>Detail Per Kategori</h4>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kriteria</th>
                <th>Nilai Rata-rata</th>
                <th>Himpunan Dominan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($detail['kategori'] as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data['nama_kriteria'] }}</td>
                    <td>{{ number_format($data['rata2'], 2) }}</td>
                    <td>{{ $data['himpunan'] ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data kategori</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
