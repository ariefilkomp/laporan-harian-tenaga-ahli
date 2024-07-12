<div class="flex flex-col justify-center items-center my-2" x-data="{ openModal: false }">
    <!-- Button -->
    <button class="py-1 px-2 rounded-md border text-gray-600 border-gray-600 bg-blue-100 mb-3 shadow-sm text-xs "
        x-on:click="openModal = !openModal">
        Tambah Uraian Pekerjaan
    </button>

    <!-- Modal -->
    <div class="py-2 p-1 bg-white rounded-md border border-gray-600 w-[500px] shadow-md" x-show="openModal"
        x-on:click.away="openModal=false" x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-[-20%] scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition transform ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-[-20%] scale-90">
        <div class="flex justify-end px-3">
            <button x-on:click="openModal = false">
                <i class="fa-solid fa-x hover:pointer"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-3">
            <div class="flex items-center gap-x-3 text-gray-600 mb-5">
                <form action="{{ route('lh.save') }}" id="form{{$tanggal}}" class="w-full" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{$tanggal}}">                    
                    <label for="uraian_pekerjaan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Uraian Pekerjaan {{$tanggalStr}}</label>
                    <textarea name="uraian_pekerjaan" id="uraian_pekerjaan" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Tulis uraian pekerjaan disini..."></textarea>
                </form>
            </div>

            <div class="flex items-center gap-2">
                <button class="py-2 px-5 rounded-md border border-gray-600 text-gray-600 w-1/2"
                    x-on:click="openModal = false">
                    Batal
                </button>
                <button class="py-2 px-5 rounded-md w-1/2 bg-gray-600 text-gray-100" x-on:click="document.getElementById('form{{$tanggal}}').submit();">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
