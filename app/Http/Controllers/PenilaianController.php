<?php

namespace App\Http\Controllers;

use App\Models\{
    Penilaian,
    HelperDomain,
    HelperHimpunan,
    HelperKriteria,
    HelperSubKriteria
};
use App\Services\AutoGenerateFuzzyRuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenilaianController extends Controller
{
    public function index()
    {
        $penilaians = Penilaian::all();
        $kriteria   = HelperKriteria::all()->toArray();

        if ($penilaians->isEmpty()) {
            return view('admin.penilaian.index', [
                'kategori'   => [],
                'himpunan'   => [],
                'penilaians' => [],
                'isEmpty'    => true,
                'kriteria'   => $kriteria,
            ]);
        }

        $kategori       = [];
        $penilaianGroup = [];
        $himpunanRange  = [];

        foreach ($penilaians as $row) {
            $kategori[$row->kriteria][$row->sub_kriteria] = true;

            $slug = Str::slug($row->sub_kriteria, '_');
            $penilaianGroup[$slug][$row->himpunan] = [
                'min' => $row->min,
                'max' => $row->max
            ];
            $himpunanRange[$row->himpunan] = [$row->min, $row->max];
        }

        foreach ($kategori as $k => $subs) $kategori[$k] = array_keys($subs);

        return view('admin.penilaian.index', [
            'kategori'   => $kategori,
            'himpunan'   => $himpunanRange,
            'penilaians' => $penilaianGroup,
            'isEmpty'    => false,
            'kriteria'   => $kriteria,
        ]);
    }

    public function store(Request $r)
    {
        $kriteriaId   = $r->input('kriteria');
        $subKriteria  = $r->input('sub_kriteria');
        $himpunanList = $r->input('himpunan', []);
        $minList      = $r->input('min', []);
        $maxList      = $r->input('max', []);

        $krit = HelperKriteria::findOrFail($kriteriaId);

        $sub  = HelperSubKriteria::ValidateSubKriteria([
            'id'           => $krit->id,
            'kriteria'     => $krit->kriteria,
            'sub_kriteria' => $subKriteria,
        ]);

        $himp = HelperHimpunan::ValidateHimpunan($sub['data'], $himpunanList);
        $dom  = HelperDomain::ValidateDomain($himp['data'], $minList, $maxList);

        $rows = collect($dom['data'])->map(function ($d) {
            return [
                'kriteria'     => $d['kriteria'],
                'sub_kriteria' => $d['sub_kriteria'],
                'himpunan'     => $d['himpunan'],
                'min'          => $d['domain_min'],
                'max'          => $d['domain_max'],
                'created_at'   => now(),
            ];
        })->toArray();

        Penilaian::insert($rows);

        $outExists = HelperDomain::where('id_kriteria', $krit->id)
            ->whereNull('id_sub_kriteria')
            ->exists();

        if (!$outExists) {
            $default = [
                ['Kurang', 50, 65],
                ['Cukup', 55, 75],
                ['Baik', 65, 85],
                ['Baik Sekali', 75, 90],
            ];

            foreach ($default as $i => $d) {
                HelperDomain::create([
                    'id_kriteria'     => $krit->id,
                    'kriteria'        => $krit->kriteria,
                    'id_sub_kriteria' => null,
                    'sub_kriteria'    => null,
                    'id_himpunan'     => $i + 1,
                    'himpunan'        => $d[0],
                    'id_domain'       => 1000 + $i,
                    'domain_min'      => $d[1],
                    'domain_max'      => $d[2],
                ]);
            }
        }

        return back()->with('success', 'Data penilaian & domain berhasil disimpan!');
    }

    public function update(Request $r, Penilaian $penilaian)
    {
        $kriteria    = $r->input('kategori');
        $subKriteria = $r->input('sub_kriteria');
        $slug        = $r->input('slug');
        $dat         = $r->input($slug, []);

        $list = DB::table('penilaian')
            ->join('helper_sub_kriteria', function ($j) {
                $j->on('penilaian.kriteria', '=', 'helper_sub_kriteria.kriteria')
                    ->on('penilaian.sub_kriteria', '=', 'helper_sub_kriteria.sub_kriteria');
            })
            ->join('helper_himpunan', function ($j) {
                $j->on('helper_sub_kriteria.id_sub_kriteria', '=', 'helper_himpunan.id_sub_kriteria')
                    ->on('penilaian.himpunan', '=', 'helper_himpunan.himpunan');
            })
            ->where('penilaian.kriteria', $kriteria)
            ->where('penilaian.sub_kriteria', $subKriteria)
            ->select(
                'penilaian.*',
                'helper_sub_kriteria.id_kriteria',
                'helper_sub_kriteria.id_sub_kriteria',
                'helper_himpunan.id_himpunan'
            )->get();

        if ($list->isEmpty()) {
            return back()->with('error', 'Data penilaian tidak ditemukan!');
        }

        foreach ($list as $row) {
            DB::table('penilaian')->where('id', $row->id)->update([
                'min' => $dat[$row->himpunan]['min'] ?? $row->min,
                'max' => $dat[$row->himpunan]['max'] ?? $row->max,
            ]);
        }

        $helperFormat = $list->map(function ($r) {
            return [
                'id_kriteria'     => $r->id_kriteria,
                'kriteria'        => $r->kriteria,
                'id_sub_kriteria' => $r->id_sub_kriteria,
                'sub_kriteria'    => $r->sub_kriteria,
                'id_himpunan'     => $r->id_himpunan,
                'himpunan'        => $r->himpunan,
            ];
        })->toArray();

        HelperDomain::ValidateDomain(
            $helperFormat,
            collect($dat)->pluck('min')->values()->toArray(),
            collect($dat)->pluck('max')->values()->toArray()
        );

        foreach ($helperFormat as $item) {
            HelperDomain::where('id_kriteria', $item['id_kriteria'])
                ->where('id_sub_kriteria', $item['id_sub_kriteria'])
                ->where('id_himpunan', $item['id_himpunan'])
                ->update([
                    'domain_min' => $dat[$item['himpunan']]['min'] ?? null,
                    'domain_max' => $dat[$item['himpunan']]['max'] ?? null,
                ]);
        }

        return back()->with('success', 'Domain & penilaian berhasil diperbarui!');
    }

    public function destroy(Request $r, Penilaian $penilaian)
    {
        $kriteria    = $r->input('kriteria');
        $subKriteria = $r->input('sub_kriteria');
        $himpunan    = $r->input('himpunan');

        $q = $penilaian->newQuery();
        if ($himpunan && $subKriteria) {
            $q->where('himpunan', $himpunan)->where('sub_kriteria', $subKriteria);
        } elseif ($subKriteria) {
            $q->where('sub_kriteria', $subKriteria);
        } elseif ($kriteria) {
            $q->where('kriteria', $kriteria);
        } else {
            return back()->with('error', 'Parameter penghapusan tidak lengkap!');
        }

        $toDelete = $q->get();

        if ($toDelete->isEmpty()) {
            return back()->with('warning', 'Tidak ada data yang dihapus.');
        }

        // Ambil ID untuk hapus dari helper_domain (input)
        $refIds = DB::table('penilaian')
            ->join('helper_sub_kriteria', function ($j) {
                $j->on('penilaian.kriteria', '=', 'helper_sub_kriteria.kriteria')
                    ->on('penilaian.sub_kriteria', '=', 'helper_sub_kriteria.sub_kriteria');
            })
            ->join('helper_himpunan', function ($j) {
                $j->on('helper_sub_kriteria.id_sub_kriteria', '=', 'helper_himpunan.id_sub_kriteria')
                    ->on('penilaian.himpunan', '=', 'helper_himpunan.himpunan');
            })
            ->whereIn('penilaian.id', $toDelete->pluck('id'))
            ->select(
                'helper_sub_kriteria.id_kriteria',
                'helper_sub_kriteria.id_sub_kriteria',
                'helper_himpunan.id_himpunan'
            )->get();

        foreach ($refIds as $ref) {
            HelperDomain::where('id_kriteria', $ref->id_kriteria)
                ->where('id_sub_kriteria', $ref->id_sub_kriteria)
                ->where('id_himpunan', $ref->id_himpunan)
                ->delete();
        }

        // Hapus dari penilaian
        $deleted = Penilaian::whereIn('id', $toDelete->pluck('id'))->delete();

        // ✅ CEK & HAPUS SEMUA OUTPUT YANG TIDAK PUNYA INPUT (orphaned outputs)
        $outputDomains = HelperDomain::whereNull('id_sub_kriteria')->get();

        foreach ($outputDomains->groupBy('id_kriteria') as $idKriteria => $rows) {
            $hasInput = HelperDomain::where('id_kriteria', $idKriteria)
                ->whereNotNull('id_sub_kriteria')
                ->exists();

            if (!$hasInput) {
                HelperDomain::where('id_kriteria', $idKriteria)
                    ->whereNull('id_sub_kriteria')
                    ->delete();
            }
        }

        return back()->with(
            $deleted ? 'success' : 'warning',
            $deleted ? 'Data berhasil dihapus!' : 'Tidak ada data yang dihapus.'
        );
    }

    public function autoGenerate()
    {
        app(AutoGenerateFuzzyRuleService::class)->generate();
        return redirect()->back()->with('success', 'Fuzzy rules berhasil di-generate!');
    }
}
