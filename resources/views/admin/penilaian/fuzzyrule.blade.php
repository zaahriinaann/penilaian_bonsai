@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card w-100 mb-5 card-kelola" style="z-index: 1">
        <div class="card-header sticky-top bg-white">
            <h1 class="card-title">Daftar Fuzzy Rules</h1>
        </div>

        <div class="card-body">
            <form action="{{ route('fuzzy-rules.auto-generate') }}" method="POST" class="mb-3">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-cogs"></i> Generate Fuzzy Rules
                </button>
            </form>

            @forelse ($rules as $rule)
                <div class="mb-2 p-3 border rounded bg-light">
                    <strong>Rule {{ $loop->iteration + ($rules->currentPage() - 1) * $rules->perPage() }}:</strong>
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

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $rules->links() }}
            </div>
        </div>
    </div>
@endsection
