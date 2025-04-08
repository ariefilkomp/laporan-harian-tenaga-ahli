<?php

namespace App\Http\Controllers;

use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\LaporanHarian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        $yearMonth = $request->get('ym', Carbon::now()->format('Y-m'));
        $ym = Carbon::createFromFormat('Y-m', $yearMonth);
        $laporanHarian = LaporanHarian::with(['buktiLaporan'])->where('user_id', Auth::user()->id)
            ->whereMonth('tanggal', $ym->format('m'))
            ->whereYear('tanggal', $ym->format('Y'))->get();
        $firstDate = new Carbon('first day of ' . $ym->format('F Y'));
        $lastDate = new Carbon('last day of ' . $ym->format('F Y'));
        $period = CarbonPeriod::create($firstDate, $lastDate);

        //laporan maping
        $laporans = [];
        if($laporanHarian->count() > 0)
        {
            foreach($laporanHarian as $laporan) {
                $laporans[$laporan->tanggal][] = [
                    "id" => $laporan->id,
                    "uraian_pekerjaan" => $laporan->uraian_pekerjaan,
                    "buktiLaporan" => $laporan->buktiLaporan
                ];
            }
        }

        //get libur use Illuminate\Support\Facades\Http;
        $liburs = Cache::remember('liburs'.date('Ymd'), 86400, function () use ($ym) {
            $libur = Http::get('https://dayoffapi.vercel.app/api?month='. $ym->format('m').'&year='. $ym->format('Y'))->json();
            $liburs = [];
            if(count($libur) > 0)
            {
                foreach($libur as $lib)
                {
                    $liburs[] = $lib['tanggal'];
                }
            }
            return $liburs;
        });

        return view('lh.index', compact('yearMonth', 'laporanHarian', 'period', 'ym', 'laporans', 'liburs', 'yearMonth'));
    }

    public function delete(Request $request)
    {
        $lh = LaporanHarian::findOrFail($request->id);
        BuktiLaporan::where('laporan_harian_id', $lh->id)->delete();
        $anchorID = 'date'.str_replace('-', '', $lh->tanggal);
        $lh->delete();
        return Redirect::to(URL::previous() . "#" .$anchorID)->with('message'.$anchorID, 'Data deleted successfully.');
    }

    public function save(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date_format:Y-m-d',
            'uraian_pekerjaan' => 'required'
        ]);

        LaporanHarian::create([
            'user_id' => Auth::user()->id,
            'tanggal' => $request->tanggal,
            'uraian_pekerjaan' => $request->uraian_pekerjaan
        ]);

        $anchorID = 'date'.str_replace('-', '', $request->tanggal);
        return Redirect::to(URL::previous() . "#" .$anchorID)->with('message'.$anchorID, 'Data saved successfully.');
    }

    public function uploadBukti(Request $request)
    {
        $lh = LaporanHarian::findOrFail($request->id);
        $lh->buktiLaporan()->create([
            'bukti' => $request->bukti->store('public/images/bukti-laporans')
        ]);
        $anchorID = 'date'.str_replace('-', '', $lh->tanggal);
        $urlPref = URL::previous();
        if(str_contains($urlPref, '?')) {
            $rdr = URL::previous() ."&rand=".rand(1, 9999) ."#" .$anchorID;
        } else {
            $rdr = URL::previous() ."?ym=" .substr($lh->tanggal, 0, 7) ."&rand=".rand(1, 9999) ."#" .$anchorID;
        }

        return response()->json(['success' => true, 'pesan' => "Data saved successfully.", 'rdr' => $rdr]);
    }

    public function hapusBukti(Request $request)
    {
        $lh = BuktiLaporan::findOrFail($request->id);
        $tanggal = $lh->laporanHarian->tanggal;
        $lh->delete();
        $anchorID = 'date'.str_replace('-', '', $tanggal);
        return Redirect::to(URL::previous() . "#" .$anchorID)->with('message'.$anchorID, 'Data deleted successfully.');
    }

    public function docx(Request $request)
    {
        $yearMonth = $request->get('ym', Carbon::now()->format('Y-m'));
        $ym = Carbon::createFromFormat('Y-m', $yearMonth);
        $laporanHarian = LaporanHarian::with(['buktiLaporan'])->where('user_id', Auth::user()->id)
            ->whereMonth('tanggal', $ym->format('m'))
            ->whereYear('tanggal', $ym->format('Y'))->get();
        $firstDate = new Carbon('first day of ' . $ym->format('F Y'));
        $lastDate = new Carbon('last day of ' . $ym->format('F Y'));
        $period = CarbonPeriod::create($firstDate, $lastDate);

        //laporan maping
        $laporans = [];
        if($laporanHarian->count() > 0)
        {
            foreach($laporanHarian as $laporan) {
                $laporans[$laporan->tanggal][] = [
                    "id" => $laporan->id,
                    "uraian_pekerjaan" => $laporan->uraian_pekerjaan,
                    "buktiLaporan" => $laporan->buktiLaporan
                ];
            }
        }

        //get libur use Illuminate\Support\Facades\Http;
        $liburs = Cache::remember('liburs'.date('Ymd'), 86400, function () use ($ym) {
            $libur = Http::get('https://dayoffapi.vercel.app/api?month='. $ym->format('m').'&year='. $ym->format('Y'))->json();
            $liburs = [];
            if(count($libur) > 0)
            {
                foreach($libur as $lib)
                {
                    $liburs[] = $lib['tanggal'];
                }
            }
            return $liburs;
        });
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        $paragraphStyleName = 'pStyle';
        $paragraphStyleNameLeft = 'pStyleLeft';
        $phpWord->addParagraphStyle($paragraphStyleName, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $phpWord->addParagraphStyle($paragraphStyleNameLeft, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::START]);

        $section->addText(
            'Laporan Pelaksanaan Pekerjaan',
            array('size' => 14, 'bold' => true, 'align' => 'center'),
            $paragraphStyleName
        );
        $h2 = auth()->user()->jabatan." pada ".auth()->user()->nama_dinas;
        $section->addText(
            $h2,
            array('size' => 12, 'bold' => true, 'align' => 'center'),
            $paragraphStyleName
        );

        $section->addText(
            "Nama : ".auth()->user()->name,
            array('size' => 12, 'align' => 'center'),
            $paragraphStyleName
        );
        $section->addText(
            "Bulan : ".$ym->isoFormat('MMMM Y'),
            array('size' => 12, 'align' => 'center'),
            $paragraphStyleName
        );

        $section->addText(
            "",
            array('size' => 12, 'align' => 'center'),
        );

        $tableStyle = array(
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 50
        );
        $firstRowStyle = array('bgColor' => '66BBFF');
        $phpWord->addTableStyle('myTable', $tableStyle, $firstRowStyle);

        $table = $section->addTable('myTable');
        $table->addRow();
        $table->addCell(550)->addText("NO", array('bold' => true), $paragraphStyleName);
        $table->addCell(2000)->addText("HARI/ TGL", array('bold' => true), $paragraphStyleName);
        $table->addCell(5000)->addText("URAIAN PEKERJAAN", array('bold' => true), $paragraphStyleName);
        $table->addCell(1750)->addText("TANDA TANGAN", array('bold' => true), $paragraphStyleName);
        $number = 1;
        foreach ($period as $per) {
            if (
                $per->isoFormat('dddd') == 'Sabtu' ||
                    $per->isoFormat('dddd') == 'Minggu' ||
                    in_array($per->format('Y-m-j'), $liburs)) {
                        continue;
            }
            
            $table->addRow();
            $table->addCell(550)->addText($number++);
            $table->addCell(2000)->addText($per->isoFormat('dddd, D MMMM Y'));
            $uraian = $table->addCell(5000);
            if (isset($laporans[$per->format('Y-m-d')]) && count($laporans[$per->format('Y-m-d')]) > 0) {
                foreach ($laporans[$per->format('Y-m-d')] as $laporan) {
                    $uraian->addListItem($laporan['uraian_pekerjaan']);
                }
            }
            $table->addCell(1750)->addText("");
        }

        $section->addText("");

        $tableTtd = $section->addTable();
        $tableTtd->addRow();
        $atasan = $tableTtd->addCell(4000);
        $tableTtd->addCell(1000);
        
        $atasan->addText("Pejabat Pembuat Komitmen", null, $paragraphStyleNameLeft);
        $atasan->addText(auth()->user()->nama_dinas, null, $paragraphStyleNameLeft);
        $atasan->addText("");
        $atasan->addText("");
        $atasan->addText("");
        $atasan->addText("");
        $atasan->addText("");
        $atasan->addText("");
        $atasan->addText(auth()->user()->nama_pejabat, array('bold' => true, 'underline' => 'single'), $paragraphStyleNameLeft);
        $atasan->addText('NIP. '.auth()->user()->nip_pejabat, null, $paragraphStyleNameLeft);
        $user = $tableTtd->addCell(4000, array('align' => 'left'));
        $user->addText("");
        $user->addText("Yang Melaporkan", null, $paragraphStyleNameLeft);
        $user->addText("");
        $user->addText("");
        $user->addText("");
        $user->addText("");
        $user->addText("");
        $user->addText("");
        $user->addText(auth()->user()->name, array('bold' => true, 'underline' => 'single'), $paragraphStyleNameLeft);

        $section->addText("");
        $section->addText("");
        $section->addText("LAMPIRAN", array('size' => 14, 'bold' => true, 'align' => 'center'), $paragraphStyleName);
        $section->addText("");


        $nomor = 1;
        foreach ($period as $per) {
            if (
                $per->isoFormat('dddd') == 'Sabtu' ||
                    $per->isoFormat('dddd') == 'Minggu' ||
                    in_array($per->format('Y-m-j'), $liburs))
                continue;

            $section->addText($nomor.". ".$per->isoFormat('dddd, D MMMM Y'));
            $images = [];
            if (isset($laporans[$per->format('Y-m-d')]) && count($laporans[$per->format('Y-m-d')]) > 0) {
                foreach ($laporans[$per->format('Y-m-d')] as $laporan) {
                    $section->addListItem($laporan['uraian_pekerjaan']);
                    $images = array_merge($images, $laporan['buktiLaporan']->toArray());
                }
            }

            if (count($images) > 0)
            {
                foreach ($images as $image) {
                    $source = storage_path('app/'.$image['bukti']);
                    $fileContent = file_get_contents($source);
                    $section->addImage($fileContent,['width' => 350, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                }
            }

            $section->addText("");

            $nomor++;
        }
                    
        $file = 'laporan-harian-'.$yearMonth.'.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $xmlWriter->save("php://output");


    }
}
