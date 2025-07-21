@php
    $prefix = $prefix ?? '';
    $values = $values ?? [];
@endphp

{{-- Nama Kontes --}}
<div class="mb-3">
    <label for="{{ $prefix }}nama_kontes" class="form-label">Nama Kontes</label>
    <input type="text" name="{{ $prefix }}nama_kontes" id="{{ $prefix }}nama_kontes"
        class="form-control {{ $prefix }}nama_kontes" value="{{ $values['nama_kontes'] ?? '' }}" required>
    <small class="msg-slug {{ $prefix }}msg-slug"></small>
</div>

{{-- Tempat Kontes --}}
<div class="mb-3">
    <label for="{{ $prefix }}tempat_kontes" class="form-label">Tempat Kontes</label>
    <div class="form-check form-switch mb-2">
        <input class="form-check-input" type="checkbox" id="{{ $prefix }}link_gmaps_checkbox">
        <label class="form-check-label" for="{{ $prefix }}link_gmaps_checkbox">Google Maps</label>
    </div>
    <textarea name="{{ $prefix }}tempat_kontes" id="{{ $prefix }}tempat_kontes" class="form-control"
        rows="2" required>{{ $values['tempat_kontes'] ?? '' }}</textarea>
</div>

{{-- Link Google Maps --}}
<div class="mb-3 d-none" id="{{ $prefix }}form_gmaps">
    <label for="{{ $prefix }}link_gmaps" class="form-label">Link Google Maps</label>
    <input type="url" name="{{ $prefix }}link_gmaps" id="{{ $prefix }}link_gmaps" class="form-control"
        value="{{ $values['link_gmaps'] ?? '' }}">
</div>

{{-- Tanggal --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="{{ $prefix }}tanggal_mulai_kontes" class="form-label">Tanggal Mulai</label>
        <input type="datetime-local" name="{{ $prefix }}tanggal_mulai_kontes"
            id="{{ $prefix }}tanggal_mulai_kontes" class="form-control"
            value="{{ $values['tanggal_mulai_kontes'] ?? '' }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="{{ $prefix }}tanggal_selesai_kontes" class="form-label">Tanggal Selesai</label>
        <input type="datetime-local" name="{{ $prefix }}tanggal_selesai_kontes"
            id="{{ $prefix }}tanggal_selesai_kontes" class="form-control"
            value="{{ $values['tanggal_selesai_kontes'] ?? '' }}" required>
    </div>
</div>

{{-- Tingkat Kontes --}}
<div class="mb-3">
    <label for="{{ $prefix }}tingkat_kontes" class="form-label">
        Tingkat Kontes <i class="bi bi-question-circle" title="Utama: min 20 | Madya: min 30"></i>
    </label>
    <select name="{{ $prefix }}tingkat_kontes" id="{{ $prefix }}tingkat_kontes" class="form-select"
        required>
        <option value="">Pilih Tingkat Kontes</option>
        <option value="Bahan" @selected(($values['tingkat_kontes'] ?? '') == 'Bahan')>Bahan</option>
        <option value="Pratama" @selected(($values['tingkat_kontes'] ?? '') == 'Pratama')>Pratama</option>
        <option value="Madya" @selected(($values['tingkat_kontes'] ?? '') == 'Madya')>Madya</option>
        <option value="Bintang" @selected(($values['tingkat_kontes'] ?? '') == 'Bintang')>Bintang</option>
    </select>
</div>

{{-- Jumlah Peserta --}}
<div class="mb-1">
    <label for="{{ $prefix }}jumlah_peserta" class="form-label">Jumlah Peserta/Bonsai</label>
    <input type="number" name="{{ $prefix }}jumlah_peserta" id="{{ $prefix }}jumlah_peserta"
        class="form-control" value="{{ $values['jumlah_peserta'] ?? '' }}" min="0" required>
</div>
<small class="text-muted" id="{{ $prefix }}jumlah_peserta_text"></small>

{{-- Poster --}}
<div class="mb-3 mt-3">
    <label for="{{ $prefix }}poster_kontes" class="form-label">Poster Kontes</label>
    <input type="file" name="{{ $prefix }}poster_kontes" id="{{ $prefix }}poster_kontes"
        class="form-control" accept="image/*">
</div>
