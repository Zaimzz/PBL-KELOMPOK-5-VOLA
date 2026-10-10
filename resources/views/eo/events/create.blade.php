<x-layouts.eo title="Buat Event Baru — Vola">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('eo.events.index') }}" class="hover:text-gray-700">Event Saya</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium">Buat Event Baru</span>
    </nav>

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Buat Event & Rekrutmen Kru Baru</h1>
        <p class="text-gray-500 mt-1">Lengkapi informasi kegiatan dan tentukan divisi relawan yang dibutuhkan.</p>
    </div>

    <form action="{{ route('eo.events.store') }}" method="POST" enctype="multipart/form-data"
          x-data="eventForm()"
          x-init="$nextTick(() => { validateDeadline(); validateEndDate(); })">
        @csrf

        <div class="space-y-6">

            {{-- Section 1: Informasi Dasar Event --}}
            <x-card>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <h2 class="text-base font-semibold text-gray-900">Informasi Dasar Event</h2>
                    </div>
                    <span class="text-xs text-gray-400">*wajib diisi lengkap</span>
                </div>

                {{-- Poster Upload --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Poster / Banner Acara</label>

                    {{-- Dropzone --}}
                    <div class="border-2 border-dashed border-gray-200 rounded-xl transition-colors cursor-pointer min-h-[160px] flex flex-col justify-center"
                         :class="posterError ? '!border-red-400 bg-red-50' : (posterPreview ? '!border-brand-400' : 'hover:border-brand-400')"
                         x-on:dragover.prevent="dragover = true"
                         x-on:dragleave.prevent="dragover = false"
                         x-on:drop.prevent="handleDrop($event)"
                         x-on:click="$refs.posterInput.click()">

                        <input type="file" name="poster" id="poster" accept=".jpg,.jpeg,.png,.webp"
                               x-ref="posterInput"
                               class="hidden"
                               x-on:change="previewPoster($event)">

                        {{-- Empty state: style=display:block agar terlihat sebelum Alpine init --}}
                        <div x-show="!posterPreview" style="display:block" class="p-8 space-y-3 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">
                                Tarik & letakkan poster event di sini, atau
                                <span class="text-brand-600 font-medium">Pilih Berkas</span>
                            </p>
                            <p class="text-xs text-gray-400">Format: JPG, PNG, atau WEBP (Rekomendasi: 1:1, maks 2 MB)</p>
                        </div>

                        {{-- Preview state --}}
                        <div x-show="posterPreview" class="p-4">
                            <div class="relative inline-block w-full">
                                <img :src="posterPreview"
                                     class="max-h-56 mx-auto rounded-lg object-contain block"
                                     alt="Preview poster">
                                {{-- File info bar --}}
                                <div class="mt-3 flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-xs text-gray-600 truncate" x-text="posterFileName"></span>
                                        <span class="text-xs text-gray-400 shrink-0" x-text="posterFileSize"></span>
                                    </div>
                                    <button type="button"
                                            x-on:click.stop="clearPoster()"
                                            class="ml-2 text-xs text-red-500 hover:text-red-700 font-medium shrink-0">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Client-side size/type error --}}
                    <div x-show="posterError" class="mt-2 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.19 2.5 1.732 2.5z"/>
                        </svg>
                        <p class="text-sm text-red-600" x-text="posterError"></p>
                    </div>

                    @error('poster')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-5">
                    {{-- Nama Event --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nama Event <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title"
                               value="{{ old('title') }}"
                               placeholder="Contoh: Festival Musik Indie Malang 2025"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('title') border-red-300 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category & Registration Deadline --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori Event <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('category_id') border-red-300 @enderror">
                                <option value="">— Pilih Kategori —</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Batas Pendaftaran: tidak dibatasi date picker, cukup tampilkan peringatan --}}
                        <div>
                            <label for="registration_deadline" class="block text-sm font-medium text-gray-700 mb-1">Batas Pendaftaran</label>
                            <input type="date" name="registration_deadline" id="registration_deadline"
                                   value="{{ old('registration_deadline') }}"
                                   x-on:change="validateDeadline()"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('registration_deadline') border-red-300 @enderror">
                            <p x-show="deadlineError" x-text="deadlineError" class="mt-1 text-sm text-red-600" style="display:none"></p>
                            @error('registration_deadline')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Acara</label>
                            <input type="date" name="start_date" id="start_date"
                                   value="{{ old('start_date') }}"
                                   x-on:change="onStartDateChange()"
                                   x-model="startDateVal"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('start_date') border-red-300 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Acara</label>
                            <input type="date" name="end_date" id="end_date"
                                   value="{{ old('end_date') }}"
                                   x-on:change="validateEndDate()"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('end_date') border-red-300 @enderror">
                            <p x-show="endDateError" x-text="endDateError" class="mt-1 text-sm text-red-600" style="display:none"></p>
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Times --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                            <input type="time" name="start_time" id="start_time"
                                   value="{{ old('start_time') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('start_time') border-red-300 @enderror">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                            <input type="time" name="end_time" id="end_time"
                                   value="{{ old('end_time') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('end_time') border-red-300 @enderror">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Acara</label>
                        <input type="text" name="location" id="location"
                               value="{{ old('location') }}"
                               placeholder="Contoh: Sasana Budaya Ganesha (Sabuga), Bandung"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('location') border-red-300 @enderror">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city') }}"
                               placeholder="Contoh: Bandung"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('city') border-red-300 @enderror">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat Acara</label>
                        <textarea name="description" id="description"
                                  rows="4"
                                  placeholder="Jelaskan ringkasan agenda acara, target pengunjung, serta gambaran keterlibatan kru/relawan secara umum..."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Info --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-1">Info Kontak</label>
                            <input type="text" name="contact_info" id="contact_info"
                                   value="{{ old('contact_info') }}"
                                   placeholder="No. HP / Email kontak"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('contact_info') border-red-300 @enderror">
                            @error('contact_info')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                            <input type="text" name="pic_name" id="pic_name"
                                   value="{{ old('pic_name') }}"
                                   placeholder="Nama penanggung jawab"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm @error('pic_name') border-red-300 @enderror">
                            @error('pic_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Section 2: Benefits --}}
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <h2 class="text-base font-semibold text-gray-900">Fasilitas & Benefit Relawan</h2>
                </div>

                {{-- Benefits section punya Alpine scope sendiri, tidak nested di eventForm() --}}
                <div x-data="benefitsForm({{ json_encode(old('benefits', [])) }})" class="space-y-4">
                    <p class="text-sm text-gray-500">Pilih benefit yang akan diterima relawan:</p>

                    {{-- Preset benefits checkboxes --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $commonBenefits = [
                                'Uang saku / Transportasi',
                                'Konsumsi & Snack',
                                'Kaos Panitia & Jaket Resmi',
                                'E-Sertifikat Terverifikasi',
                                'Akomodasi',
                                'Networking',
                                'Pengalaman Kerja',
                                'Merchandise',
                            ];
                        @endphp
                        @foreach ($commonBenefits as $benefit)
                            <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer transition-colors"
                                   :class="selected.includes('{{ addslashes($benefit) }}') ? 'border-brand-500 bg-brand-50' : 'border-gray-200 hover:border-brand-300 hover:bg-gray-50'">
                                <input type="checkbox"
                                       name="benefits[]"
                                       value="{{ $benefit }}"
                                       x-model="selected"
                                       class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm text-gray-700">{{ $benefit }}</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Custom benefit input --}}
                    <div class="flex gap-2">
                        <input type="text"
                               x-model="customBenefit"
                               placeholder="Tambah benefit lainnya (tekan Enter atau klik Tambah)..."
                               class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm"
                               x-on:keydown.enter.prevent="addCustomBenefit()">
                        <button type="button"
                                x-on:click="addCustomBenefit()"
                                class="px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                            + Tambah
                        </button>
                    </div>

                    {{-- Custom benefits tags --}}
                    <div x-show="customBenefits.length > 0" class="space-y-1">
                        <p class="text-xs text-gray-500 font-medium">Benefit tambahan:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(benefit, index) in customBenefits" :key="index">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 border border-brand-200 text-brand-700 text-sm rounded-full">
                                    <input type="hidden" name="benefits[]" :value="benefit">
                                    <span x-text="benefit"></span>
                                    <button type="button"
                                            x-on:click="removeCustomBenefit(index)"
                                            class="text-brand-400 hover:text-red-600 transition-colors font-bold leading-none"
                                            :aria-label="'Hapus ' + benefit">
                                        &times;
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </x-card>

        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between gap-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('eo.events.index') }}"
               class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                Simpan sebagai Draft
            </button>
        </div>
    </form>

</x-layouts.eo>

@push('scripts')
<script>
function eventForm() {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');

    return {
        // --- Poster ---
        posterPreview: null,
        posterFileName: '',
        posterFileSize: '',
        posterError: '',
        dragover: false,

        // --- Dates ---
        todayStr: `${yyyy}-${mm}-${dd}`,
        startDateVal: '{{ old('start_date', '') }}',
        endDateError: '',
        deadlineError: '',

        previewPoster(event) {
            const file = event.target.files[0];
            if (!file) return;
            this._handleFile(file);
        },

        handleDrop(event) {
            this.dragover = false;
            const file = event.dataTransfer.files[0];
            if (!file) return;
            this.$refs.posterInput.files = event.dataTransfer.files;
            this._handleFile(file);
        },

        _handleFile(file) {
            this.posterError = '';
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const maxBytes = 2 * 1024 * 1024; // 2 MB

            if (!allowedTypes.includes(file.type)) {
                this.posterError = 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.';
                this.clearPoster();
                return;
            }

            if (file.size > maxBytes) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(1);
                this.posterError = `Ukuran file terlalu besar (${sizeMB} MB). Maksimum 2 MB.`;
                this.clearPoster();
                return;
            }

            this.posterFileName = file.name;
            this.posterFileSize = `(${(file.size / 1024).toFixed(0)} KB)`;

            const reader = new FileReader();
            reader.onload = (e) => { this.posterPreview = e.target.result; };
            reader.readAsDataURL(file);
        },

        clearPoster() {
            this.posterPreview = null;
            this.posterFileName = '';
            this.posterFileSize = '';
            this.$refs.posterInput.value = '';
        },

        // --- Date validations ---
        onStartDateChange() {
            // Re-validate end date and deadline when start date changes
            this.validateEndDate();
            this.validateDeadline();
        },

        validateEndDate() {
            const endEl = document.getElementById('end_date');
            if (!endEl || !endEl.value || !this.startDateVal) {
                this.endDateError = '';
                return;
            }
            if (endEl.value < this.startDateVal) {
                this.endDateError = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
            } else {
                this.endDateError = '';
            }
        },

        validateDeadline() {
            const dlEl = document.getElementById('registration_deadline');
            if (!dlEl || !dlEl.value) {
                this.deadlineError = '';
                return;
            }
            if (dlEl.value < this.todayStr) {
                this.deadlineError = 'Batas pendaftaran tidak boleh tanggal yang sudah lewat.';
            } else if (this.startDateVal && dlEl.value > this.startDateVal) {
                this.deadlineError = 'Batas pendaftaran seharusnya sebelum atau sama dengan tanggal mulai acara.';
            } else {
                this.deadlineError = '';
            }
        },
    };
}

function benefitsForm(initialSelected) {
    return {
        selected: Array.isArray(initialSelected) ? initialSelected : [],
        customBenefit: '',
        customBenefits: [],

        addCustomBenefit() {
            const val = this.customBenefit.trim();
            if (!val) return;

            // Prevent duplicate with preset or custom
            const allBenefits = [...this.selected, ...this.customBenefits];
            if (allBenefits.includes(val)) {
                this.customBenefit = '';
                return;
            }
            this.customBenefits.push(val);
            this.customBenefit = '';
        },

        removeCustomBenefit(index) {
            this.customBenefits.splice(index, 1);
        }
    };
}
</script>
@endpush
