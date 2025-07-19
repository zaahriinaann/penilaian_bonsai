<?php

namespace App\Http\Controllers;

use App\Models\FuzzyRule;
use Illuminate\Http\Request;
use App\Services\AutoGenerateFuzzyRuleService;

class FuzzyRuleController extends Controller
{
    public function index()
    {
        // ✅ Ambil 10 rule per halaman dan eager load relasinya
        $rules = FuzzyRule::with(['kriteria', 'details'])->paginate(10);

        return view('admin.penilaian.fuzzyrule', compact('rules'));
    }

    public function autoGenerate(Request $request)
    {
        try {
            app(AutoGenerateFuzzyRuleService::class)->generate();
            return redirect()->back()->with('success', 'Fuzzy rules berhasil digenerate. Cek di menu fuzzy rules.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal generate fuzzy rules: ' . $e->getMessage());
        }
    }
}
