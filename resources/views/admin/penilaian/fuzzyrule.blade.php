@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="container">
            <h2>Daftar Fuzzy Rules</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('fuzzy-rules.auto-generate') }}" method="POST" class="mb-3">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-sync"></i> Auto Generate Rules
                </button>
            </form>

            @forelse ($rules as $rule)
                <div class="mb-2 p-3 border rounded bg-light">
                    <strong>Rule {{ $loop->iteration }}:</strong>
                    @php
                        $inputParts = [];
                        foreach ($rule->details as $detail) {
                            $inputParts[] = $detail->input_variable . ' <em>' . ($detail->himpunan ?? '-') . '</em>';
                        }
                        $ifClause = implode(' and ', $inputParts);
                        $output = $rule->output_himpunan ?? '-';
                        $kriteriaOutput = $rule->kriteria->kriteria ?? '-';
                    @endphp
                    <div>
                        If {!! $ifClause !!} then <strong>{{ $kriteriaOutput }}</strong> <em>{{ $output }}</em>
                    </div>
                    <small class="text-muted">Status: {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}</small>
                </div>
            @empty
                <div class="alert alert-info">Belum ada rule.</div>
            @endforelse
        </div>
    </div>
@endsection
