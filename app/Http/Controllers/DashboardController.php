<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $yearMonth = $request->get('ym', Carbon::now()->format('Y-m'));
        $ym = Carbon::createFromFormat('Y-m', $yearMonth);
        $sm = Carbon::createFromFormat('Y-m', $yearMonth);
        $laporanHarian = LaporanHarian::where('user_id', auth()->user()->id)
        ->whereMonth('tanggal', $ym->format('m'))
        ->whereYear('tanggal', $ym->format('Y'))->get();
        $start = new Carbon('first day of ' . $ym->format('F Y'));
        $lastMonday = new Carbon('last Monday of '.$sm->subMonth()->format('F Y'));
        $period = $start->format('l') == 'Monday' ? CarbonPeriod::create($start->format('Y-m-d'), 42) : CarbonPeriod::create($lastMonday->format('Y-m-d'), 42);
        $currentMonth = $start->format('m');
        $currentMonthStr = $start->format('F Y');

        //laporan maping
        $laporans = [];
        if($laporanHarian->count() > 0)
        {
            foreach($laporanHarian as $laporan) {
                $laporans[$laporan->tanggal][] = [
                    "id" => $laporan->id,
                    "uraian_pekerjaan" => $laporan->uraian_pekerjaan
                ];
            }
        }

        //get libur use Illuminate\Support\Facades\Http;
        $liburs = [];
        $liburUrl = 'https://dayoffapi.vercel.app/api?month='. $ym->format('m').'&year='. $ym->format('Y');
        $libur = Http::get($liburUrl)->json();
        if(count($libur) > 0)
        {
            foreach($libur as $lib)
            {
                $liburs[] = $lib['tanggal'];
            }
        }
        return view('dashboard', compact('period', 'currentMonth', 'yearMonth', 'currentMonthStr', 'laporanHarian', 'laporans', 'liburs'));
    }
}
