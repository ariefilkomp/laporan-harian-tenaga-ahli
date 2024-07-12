<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex gap-4">
            {{ __('Laporan Harian') }}
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button type="button"
                        class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        id="menu-button" aria-expanded="false" aria-haspopup="true">
                        {{ $yearMonth }}
                        <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    @for ($month = 1; $month <= 12; $month++)
                        @php
                            $ymCtr = date('Y') . '-' . sprintf('%02d', $month); // 2022-$month;
                            $url = route('laporanHarian') . '?ym=' . $ymCtr;
                            $active = $yearMonth == $ymCtr;
                        @endphp
                        <x-dropdown-link :href="$url" :class="$active ? 'bg-gray-100' : ''">
                            {{ $ymCtr }}
                        </x-dropdown-link>
                    @endfor
                </x-slot>
            </x-dropdown>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg min-h-[600px]">
                <div class="mx-8 mt-8 flex gap-4">
                    <a href="{{ route('docx') }}?ym={{ $ym->format('Y-m') }}" class="text-slate-500 font-medium text-sm px-4 py-2 border border-slate-600 rounded-lg hover:bg-slate-100"> Unduh Docx</a>
                    <a href="#bukti{{ $ym->format('Ymd') }}" class="text-slate-500 font-medium text-sm px-4 py-2 border border-slate-600 rounded-lg hover:bg-slate-100"> Pdf</a>
                </div>
                <div class="mx-8 mt-8 flex">
                    <h1 class="mx-auto text-3xl font-bold">Laporan Pelaksanaan Pekerjaan</h1>
                </div>

                <div class="mx-8 flex">
                    <h1 class="mx-auto text-2xl font-bold">{{ auth()->user()->jabatan }} pada
                        {{ auth()->user()->nama_dinas }}</h1>
                </div>

                <div class="mx-8 mb-8 flex">
                    <table class="mx-auto text-lg">
                        <tr>
                            <td class="w-24">Nama</td>
                            <td>:</td>
                            <td>{{ auth()->user()->name }}</td>
                        </tr>
                        <tr>
                            <td>Bulan</td>
                            <td>:</td>
                            <td>{{ $ym->isoFormat('MMMM Y') }}</td>
                        </tr>
                    </table>
                </div>

                @if (session('success'))
                    <div class="m-8 flex">
                        <div class="bg-green-600 text-white px-4 py-2 rounded-lg mx-auto">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif


                <div class="relative overflow-x-auto mx-8">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">NO</th>
                                <th scope="col" class="px-6 py-3 lg:w-44 text-center">HARI / TGL</th>
                                <th scope="col" class="px-6 py-3 text-center">URAIAN PEKERJAAN</th>
                                <th scope="col" class="px-6 py-3 lg:w-40">TANDA TANGAN</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $nomor = 1;
                            @endphp
                            @foreach ($period as $per)
                                @if (
                                    $per->isoFormat('dddd') == 'Sabtu' ||
                                        $per->isoFormat('dddd') == 'Minggu' ||
                                        in_array($per->format('Y-m-j'), $liburs))
                                    @continue
                                @endif
                                @php
                                    $trBg =
                                        isset($laporans[$per->format('Y-m-d')]) &&
                                        count($laporans[$per->format('Y-m-d')]) > 0
                                            ? 'bg-white'
                                            : 'bg-orange-50';
                                @endphp
                                <tr class="{{ $trBg }} border-b dark:bg-gray-800 dark:border-gray-700"
                                    id="date{{ $per->format('Ymd') }}">
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $nomor++ }}
                                    </th>
                                    <td class="px-6 py-4 w-44">{{ $per->isoFormat('dddd, DD MMMM Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if (session('messagedate' . $per->format('Ymd')))
                                            <div class="m-8 flex">
                                                <div class="bg-green-600 text-white px-4 py-2 rounded-lg mx-auto">
                                                    {{ session('messagedate' . $per->format('Ymd')) }}
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset($laporans[$per->format('Y-m-d')]) && count($laporans[$per->format('Y-m-d')]) > 0)
                                            @foreach ($laporans[$per->format('Y-m-d')] as $laporan)
                                                - &nbsp;{{ $laporan['uraian_pekerjaan'] }}
                                                <div class="flex gap-4">
                                                    <form method="POST" action="{{ route('lh.delete') }}"
                                                        onsubmit="return confirm('Beneran, mau hapus?');">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $laporan['id'] }}">
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-700 border border-red-300 rounded-lg px-2 py-1">delete</button>
                                                    </form>

                                                    <div class="flex items-center">
                                                        <label for="file{{ $laporan['id'] }}"
                                                            class="flex flex-col items-center justify-center w-full h-8 p-2 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                            <div class="flex flex-col items-center justify-center ">
                                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 20 16">
                                                                    <path stroke="currentColor" stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                                </svg>
                                                            </div>
                                                            <input id="file{{ $laporan['id'] }}" type="file" class="hidden"
                                                                accept="image/png, image/gif, image/jpeg, image/jpg"
                                                                name="file" onchange="uploadFile({{ $laporan['id'] }})"/>
                                                        </label>
                                                    </div>

                                                    <div>
                                                        <a href="#bukti{{ $per->format('Ymd') }}"
                                                            class="text-slate-500 font-medium text-sm">
                                                            {{ $laporan['buktiLaporan']->count() }} Bukti</a>
                                                    </div>

                                                </div>
                                                <br>
                                            @endforeach
                                        @endif
                                        <x-lh-modal :tanggal="$per->format('Y-m-d')" :tanggalStr="$per->isoFormat('dddd, DD MMMM Y')" />
                                    </td>
                                    <td class="px-6 py-4">&nbsp;</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="my-12">&nbsp;</div>
                <div class="flex flex-row ">
                    <div class="flex flex-col h-64 w-full text-center justify-center align-middle">
                        <p>
                            Pejabat Pembuat Komitmen
                        </p>
                        <p>
                            {{ auth()->user()->nama_dinas }}
                        </p>
                        <span class="h-24"> &nbsp;</span>
                        <p>
                            {{ auth()->user()->nama_pejabat }}
                        </p>
                        <p>
                            {{ auth()->user()->nip_pejabat }}
                        </p>
                    </div>
                    <div class="flex flex-col h-64 w-full text-center justify-center align-middle">
                        <p>
                            Yang Melaporkan
                        </p>
                        <span class="h-24">&nbsp;</span>
                        <p>
                            {{ auth()->user()->name }}
                        </p>
                    </div>
                </div>
                <div class="my-12">&nbsp;</div>

                <div class="flex">
                    <h2 class="text-xl font-bold text-slate-900 mx-auto">LAMPIRAN</h2>
                </div>

                <div class="my-12">&nbsp;</div>
                @php
                    $nomor = 1;
                @endphp
                @foreach ($period as $per)
                    @if (
                        $per->isoFormat('dddd') == 'Sabtu' ||
                            $per->isoFormat('dddd') == 'Minggu' ||
                            in_array($per->format('Y-m-j'), $liburs))
                        @continue
                    @endif
                    <div class="flex flex-col mx-8 mt-8">
                        <p class="text-slate-800 font-semibold">{{ $nomor++ }}.
                            {{ $per->isoFormat('dddd, DD MMMM Y') }}</p>
                        <ul class="pl-6">

                            @php
                                $images = [];
                            @endphp
                            @if (isset($laporans[$per->format('Y-m-d')]) && count($laporans[$per->format('Y-m-d')]) > 0)
                                @foreach ($laporans[$per->format('Y-m-d')] as $laporan)
                                    <li>
                                        - &nbsp;{{ $laporan['uraian_pekerjaan'] }}
                                    </li>
                                    @php
                                        $images = array_merge($images, $laporan['buktiLaporan']->toArray());
                                    @endphp
                                @endforeach
                            @endif

                        </ul>
                        <div class="pl-6 flex flex-col gap-4" id="bukti{{ $per->format('Ymd') }}">
                            @if (count($images) > 0)
                                @foreach ($images as $image)
                                <div class="bg-gray-400 w-[500px] h-96 relative z-0">
                                    <form method="POST" action="{{ route('hapusBukti') }}" onsubmit="return confirm('Yakin, mau hapus gambar ini?');">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $image['id'] }}">
                                        <button type="submit" class="absolute top-2 right-2 z-20 rounded-full bg-red-100 hover:bg-red-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500 hover:text-red-700" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                    <div class="absolute inset-0 flex justify-center items-center z-10 overflow-hidden">
                                        <img src="{{ url('/').'/storage'.substr($image['bukti'],6) }}" alt="" class="object-fit">
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        function uploadFile(laporanId) {
            var formData = new FormData();
            let file = $("#file" + laporanId)[0].files[0];
            if (file == undefined) return;
            urlx = '{{ route('uploadBukti') }}';
            formData.append('bukti', file);
            formData.append('id', laporanId);
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                type: "POST",
                url: urlx,
                data: formData,
                processData: false, // tell jQuery not to process the data
                contentType: false, // tell jQuery not to set contentType
                beforeSend: function() {
                    Swal.fire({
                        text: 'Menyimpan ...',
                    });
                    Swal.showLoading();
                },
                success: function(result) {
                    const urlRefresh = () => window.location.href = result.rdr;
                    if (result.success == true) {
                        Swal.fire({
                            text: result.pesan,
                            icon: 'success',
                            confirmButtonText: 'close',
                            willClose: urlRefresh,
                            
                        });
                    } else if (result.success === false) {
                        Swal.fire({
                            text: result.pesan,
                            icon: 'error',
                            confirmButtonText: 'close'
                        });
                    } else {
                        console.log('parse error!');
                    }
                }
            });
        }
    </script>
</x-app-layout>
