<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Rekap Nilai Bonsai</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
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

        .bonsai-box {
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
    <p><strong>Kontes:</strong> {{ $kontes->nama_kontes }}</p>

    @foreach ($rekapData as $i => $item)
        <div class="bonsai-box">
            <table>
                <tr>
                    <th colspan="2">Data Bonsai ke-{{ $i + 1 }}</th>
                </tr>
                <tr>
                    <th>Nama Pohon</th>
                    <td class="left">{{ $item['nama_pohon'] }}</td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td class="left">{{ $item['kelas'] ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Pemilik</th>
                    <td class="left">{{ $item['pemilik'] }}</td>
                </tr>
                <tr>
                    <th>No Juri</th>
                    <td>{{ $item['nomor_juri'] }}</td>
                </tr>
                <tr>
                    <th>No Pendaftaran</th>
                    <td>{{ $item['nomor_pendaftaran'] }}</td>
                </tr>
            </table>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kriteria</th>
                        <th>Nilai Rata-rata</th>
                        <th>Himpunan Dominan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($item['kategori'] as $data)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start">{{ $data['nama_kriteria'] }}</td>
                            <td>{{ number_format($data['rata2'], 2) }}</td>
                            <td>{{ $data['himpunan'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Data kategori tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <table>
                <tr>
                    <th>Skor Akhir</th>
                    <th>Himpunan Akhir</th>
                </tr>
                <tr>
                    <td><strong>{{ number_format($item['skor_akhir'], 2) }}</strong></td>
                    <td><strong>{{ $item['himpunan_akhir'] }}</strong></td>
                </tr>
            </table>
        </div>
    @endforeach

</body>

</html>
