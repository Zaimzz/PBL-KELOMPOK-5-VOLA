<x-layouts.eo title="Edit Profil Organisasi — Vola">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('eo.dashboard') }}" class="hover:text-gray-700">Beranda EO</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('eo.organization') }}" class="hover:text-gray-700">Profil Organisasi</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">Edit Profil</span>
    </nav>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profil Organisasi</h1>
        <p class="text-gray-500 mt-1 text-sm">Lengkapi informasi organisasi dan upload dokumen legal untuk pengajuan verifikasi.</p>
    </div>

    <form action="{{ route('eo.organization.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Section 1: Informasi Organisasi --}}
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <h2 class="text-base font-semibold text-gray-900">Informasi Organisasi</h2>
                </div>

                <div class="grid grid-cols-1 gap-5">

                    {{-- Nama Organisasi --}}
                    <div>
                        <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Organisasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="organization_name"
                               id="organization_name"
                               value="{{ old('organization_name', $profile?->organization_name) }}"
                               placeholder="Contoh: PT Pop Media Nusantara"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('organization_name') border-red-300 @enderror">
                        @error('organization_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Organisasi & Kota --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="organization_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Organisasi <span class="text-red-500">*</span>
                            </label>
                            <select name="organization_type"
                                    id="organization_type"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('organization_type') border-red-300 @enderror">
                                <option value="">— Pilih Jenis —</option>
                                @foreach (['Komunitas', 'Perusahaan', 'Yayasan', 'Kampus / Lembaga Pendidikan', 'Pemerintah', 'Organisasi Nirlaba', 'Lainnya'] as $type)
                                    <option value="{{ $type }}" {{ old('organization_type', $profile?->organization_type) === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organization_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                Kota / Domisili <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   value="{{ old('city', $profile?->city) }}"
                                   placeholder="Contoh: Jakarta Selatan"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('city') border-red-300 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Deskripsi Organisasi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  placeholder="Jelaskan profil, visi misi, dan kegiatan utama organisasi Anda..."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('description') border-red-300 @enderror">{{ old('description', $profile?->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Social Link --}}
                    <div>
                        <label for="social_link" class="block text-sm font-medium text-gray-700 mb-1">Social Link</label>
                        <input type="url"
                               name="social_link"
                               id="social_link"
                               value="{{ old('social_link', $profile?->social_link) }}"
                               placeholder="https://instagram.com/organisasi atau https://website.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('social_link') border-red-300 @enderror">
                        @error('social_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Section 2: Informasi PIC --}}
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <h2 class="text-base font-semibold text-gray-900">Informasi PIC (Penanggung Jawab)</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nama PIC <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="pic_name"
                               id="pic_name"
                               value="{{ old('pic_name', $profile?->pic_name) }}"
                               placeholder="Nama penanggung jawab"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('pic_name') border-red-300 @enderror">
                        @error('pic_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pic_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Telepon PIC <span class="text-red-500">*</span>
                        </label>
                        <input type="tel"
                               name="pic_phone"
                               id="pic_phone"
                               value="{{ old('pic_phone', $profile?->pic_phone) }}"
                               placeholder="Contoh: +62 812-3456-7890"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('pic_phone') border-red-300 @enderror">
                        @error('pic_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Section 3: Dokumen Legal --}}
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Dokumen Legal</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Surat izin, akta pendirian, SK, atau dokumen resmi lainnya.</p>
                    </div>
                </div>

                @if ($profile?->document_path)
                    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-4">
                        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800">Dokumen sudah ada</p>
                            <p class="text-xs text-green-600">Upload file baru untuk menggantikan dokumen yang ada.</p>
                        </div>
                    </div>
                @endif

                {{-- Upload area --}}
                <div class="border-2 border-dashed border-gray-200 rounded-xl min-h-[120px] flex flex-col justify-center transition-colors hover:border-brand-400 cursor-pointer"
                     x-data="docUpload()"
                     x-on:click="$refs.docInput.click()">

                    <input type="file"
                           name="document"
                           id="document"
                           accept=".pdf,.jpg,.jpeg,.png"
                           x-ref="docInput"
                           class="hidden"
                           x-on:change="handleFile($event)">

                    {{-- Empty / default state --}}
                    <div x-show="!fileName" style="display:block" class="p-6 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Klik untuk upload dokumen legal</p>
                        <p class="text-xs text-gray-400 mt-1">PDF, JPG, atau PNG &mdash; maks. 5 MB</p>
                    </div>

                    {{-- File selected state --}}
                    <div x-show="fileName" class="p-4">
                        <div class="flex items-center gap-3 bg-brand-50 border border-brand-200 rounded-lg px-3 py-2.5">
                            <svg class="w-5 h-5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-brand-700 truncate" x-text="fileName"></p>
                                <p class="text-xs text-brand-500" x-text="fileSize"></p>
                            </div>
                            <button type="button"
                                    x-on:click.stop="clearFile()"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium shrink-0">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Client-side error --}}
                <p x-data="docUpload()" x-show="fileError" x-text="fileError" class="mt-2 text-sm text-red-600" style="display:none"></p>

                @error('document')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </x-card>

        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between gap-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('eo.organization') }}"
               class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                Simpan Profil
            </button>
        </div>
    </form>

</x-layouts.eo>

@push('scripts')
<script>
function docUpload() {
    return {
        fileName: '',
        fileSize: '',
        fileError: '',

        handleFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            const maxBytes = 5 * 1024 * 1024;

            this.fileError = '';

            if (!allowedTypes.includes(file.type)) {
                this.fileError = 'Format tidak didukung. Gunakan PDF, JPG, atau PNG.';
                this.clearFile();
                return;
            }

            if (file.size > maxBytes) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(1);
                this.fileError = `Ukuran file terlalu besar (${sizeMB} MB). Maksimum 5 MB.`;
                this.clearFile();
                return;
            }

            this.fileName = file.name;
            this.fileSize = `(${(file.size / 1024).toFixed(0)} KB)`;
        },

        clearFile() {
            this.fileName = '';
            this.fileSize = '';
            this.$refs.docInput.value = '';
        }
    };
}
</script>
@endpush
