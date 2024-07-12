<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Laporan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg min-h-[600px]">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="lg:flex lg:h-full lg:flex-col">
                        <header
                            class="flex items-center justify-between border-b border-gray-200 px-6 py-4 lg:flex-none">
                            <h1 class="text-base font-semibold leading-6 text-gray-900">
                                <time datetime="{{ $yearMonth }}">{{ $currentMonthStr }}</time>
                            </h1>
                            <div class="flex items-center">
                                
                                <div class="md:ml-4 md:flex md:items-center">
                                    <div class="relative">
                                        <x-dropdown align="right" width="48">
                                            <x-slot name="trigger">
                                                <button type="button"
                                                    class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                                    id="menu-button" aria-expanded="false" aria-haspopup="true">
                                                    {{ $yearMonth }}
                                                    <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                @for($month = 1; $month <= 12; $month++)
                                                    @php
                                                        $ym = date('Y') . '-' . sprintf('%02d', $month); // 2022-$month;
                                                        $url = route('dashboard') . '?ym=' . $ym;
                                                        $active = $yearMonth == $ym;    
                                                    @endphp
                                                    <x-dropdown-link :href="$url" :class="$active ? 'bg-gray-100' : ''">
                                                        {{ $ym }}
                                                    </x-dropdown-link>
                                                @endfor                                                
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                </div>
                            </div>
                        </header>
                        <div class="shadow ring-1 ring-black ring-opacity-5 lg:flex lg:flex-auto lg:flex-col">
                            <div
                                class="grid grid-cols-7 gap-px border-b border-gray-300 bg-gray-200 text-center text-xs font-semibold leading-6 text-gray-700 lg:flex-none">
                                <div class="flex justify-center bg-white py-2">
                                    <span>M</span>
                                    <span class="sr-only sm:not-sr-only">on</span>
                                </div>
                                <div class="flex justify-center bg-white py-2">
                                    <span>T</span>
                                    <span class="sr-only sm:not-sr-only">ue</span>
                                </div>
                                <div class="flex justify-center bg-white py-2">
                                    <span>W</span>
                                    <span class="sr-only sm:not-sr-only">ed</span>
                                </div>
                                <div class="flex justify-center bg-white py-2">
                                    <span>T</span>
                                    <span class="sr-only sm:not-sr-only">hu</span>
                                </div>
                                <div class="flex justify-center bg-white py-2">
                                    <span>F</span>
                                    <span class="sr-only sm:not-sr-only">ri</span>
                                </div>
                                <div class="flex justify-center bg-white py-2">
                                    <span>S</span>
                                    <span class="sr-only sm:not-sr-only">at</span>
                                </div>
                                <div class="flex justify-center bg-white py-2">
                                    <span>S</span>
                                    <span class="sr-only sm:not-sr-only">un</span>
                                </div>
                            </div>
                            <div class="flex bg-gray-200 text-xs leading-6 text-gray-700 lg:flex-auto">
                                <div class="isolate grid w-full grid-cols-7 grid-rows-6 gap-px">
                                    <!--
                                        Always include: "relative py-2 px-3"
                                        Is current month, include: "bg-white"
                                        Is not current month, include: "bg-gray-50 text-gray-500"
                                    -->
                                    @php
                                        $hariKerja = 0;
                                    @endphp
                                    @foreach ($period as $per)
                                        @if($per->format('m') == $currentMonth)
                                            @php
                                                $bgClass = 'bg-white';
                                                if( $per->format('Y-m-d') == date('Y-m-d') ) 
                                                {
                                                    $bgClass = 'bg-indigo-200';
                                                } else if( in_array($per->format('Y-m-j'), $liburs) || 
                                                    ($per->isoFormat('dddd') == 'Sabtu' || $per->isoFormat('dddd') == 'Minggu') 
                                                    )  
                                                {
                                                    $bgClass = 'bg-red-200';
                                                } else if( isset($laporans[$per->format('Y-m-d')]) && count($laporans[$per->format('Y-m-d')]) > 0 ) {
                                                    $bgClass = 'bg-green-200';
                                                }

                                                if($bgClass != 'bg-red-200') {
                                                    $hariKerja++;
                                                }
                                            @endphp
                                            <a href="{{ route('laporanHarian') }}?ym={{ $per->format('Y-m') }}#date{{ $per->format('Ymd') }}"
                                                class="flex h-14 flex-col {{ $bgClass }} px-3 py-2 text-gray-900 hover:bg-gray-100 focus:z-10">
                                                <time datetime="{{ $per->format('Y-m-d') }}" class="ml-auto">{{ $per->format('d') }}</time>
                                            </a>
                                        @else
                                            <a href="#"
                                                class="flex h-14 flex-col bg-gray-50 px-3 py-2 text-gray-500 hover:bg-gray-100 focus:z-10">
                                                <time datetime="{{ $per->format('Y-m-d') }}" class="ml-auto">{{ $per->format('d') }}</time>
                                                
                                            </a>
                                        @endif
                                    @endforeach
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-8">
                    <h3 class="text-lg text-slate-900 font-bold">Hari Kerja: {{ $hariKerja }}</h3>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
