@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h3>🏆 Ranking Hasil Penilaian Bonsai</h3>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Nama Pemilik</th>
                        <th>Nama Bonsai</th>
                        <th>Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ranking as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->bonsai->user->name }}</td>
                            <td>{{ $item->bonsai->nama_pohon }}</td>
                            <td><strong>{{ $item->skor_akhir }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
