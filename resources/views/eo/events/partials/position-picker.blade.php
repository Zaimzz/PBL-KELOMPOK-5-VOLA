@php
    $existingPositionNames = $event->positions->pluck('name')->values()->all();
    $positionCategoryOptions = collect($positionCatalog)
        ->pluck('name')
        ->push(\App\Models\PositionType::OTHER_CATEGORY)
        ->values()
        ->all();
    $positionFormErrors = collect($errors->messages())
        ->filter(fn ($messages, $key) => str_starts_with((string) $key, 'positions'))
        ->flatten()
        ->unique()
        ->values();
@endphp

<div class="mt-6"
     x-data="positionPicker({{ Js::from([
         'categories' => $positionCatalog,
         'categoryOptions' => $positionCategoryOptions,
         'existingNames' => $existingPositionNames,
         'recommendationUrl' => route('eo.positions.recommendation'),
         'initial' => old('positions', []),
     ]) }})">
    <x-card>
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold shrink-0">3</div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Posisi Volunteer yang Dibutuhkan</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Pilih posisi dari katalog atau tambahkan posisi sendiri. Keduanya bisa dipakai bersamaan dalam satu event.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs shrink-0">
                <span class="font-medium text-brand-600 bg-brand-50 px-2.5 py-1 rounded-full">
                    <span x-text="staged.length"></span> posisi dipilih
                </span>
                <span class="font-medium text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full">
                    Total <span x-text="totalQuota"></span> volunteer
                </span>
            </div>
        </div>

        {{-- Server-side errors --}}
        @if ($positionFormErrors->isNotEmpty())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-sm text-red-700 space-y-0.5">
                    @foreach ($positionFormErrors as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Method switch --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
            <button type="button"
                    x-on:click="method = 'catalog'"
                    :class="method === 'catalog' ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-gray-200 hover:border-brand-300 hover:bg-gray-50'"
                    class="flex items-start gap-3 p-4 border rounded-xl text-left transition-colors">
                <svg class="w-5 h-5 mt-0.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span>
                    <span class="block text-sm font-semibold text-gray-900">Pilih Posisi dari Katalog</span>
                    <span class="block text-xs text-gray-500 mt-0.5">Deskripsi &amp; kualifikasi terisi otomatis.</span>
                </span>
            </button>
            <button type="button"
                    x-on:click="method = 'manual'"
                    :class="method === 'manual' ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500' : 'border-gray-200 hover:border-brand-300 hover:bg-gray-50'"
                    class="flex items-start gap-3 p-4 border rounded-xl text-left transition-colors">
                <svg class="w-5 h-5 mt-0.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>
                    <span class="block text-sm font-semibold text-gray-900">Tambah Posisi Manual</span>
                    <span class="block text-xs text-gray-500 mt-0.5">Untuk posisi yang tidak ada di katalog.</span>
                </span>
            </button>
        </div>

        {{-- Catalog method --}}
        <div x-show="method === 'catalog'" style="display:block" class="mb-6">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori Posisi</p>
            <div class="flex flex-wrap gap-2 mb-4">
                <template x-for="category in categories" :key="category.name">
                    <button type="button"
                            x-on:click="activeCategory = category.name"
                            :class="activeCategory === category.name ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300'"
                            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium border rounded-full transition-colors">
                        <span x-text="category.name"></span>
                        <span x-show="stagedCountIn(category) > 0"
                              :class="activeCategory === category.name ? 'bg-white text-brand-700' : 'bg-brand-100 text-brand-700'"
                              class="text-xs font-bold rounded-full px-1.5"
                              x-text="stagedCountIn(category)"></span>
                    </button>
                </template>
            </div>

            <template x-for="category in categories" :key="'list-' + category.name">
                <div x-show="activeCategory === category.name" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="position in category.positions" :key="position.id">
                        <button type="button"
                                x-on:click="toggleCatalog(position, category.name)"
                                :disabled="isExisting(position.name)"
                                :class="isStaged(position.id)
                                    ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500'
                                    : (isExisting(position.name) ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-brand-300 hover:bg-gray-50')"
                                class="relative flex items-start gap-3 p-3 border rounded-xl text-left transition-colors">
                            <span :class="isStaged(position.id) ? 'bg-brand-600 border-brand-600' : 'bg-white border-gray-300'"
                                  class="mt-0.5 w-5 h-5 shrink-0 rounded border flex items-center justify-center">
                                <svg x-show="isStaged(position.id)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-sm font-medium text-gray-900" x-text="position.name"></span>
                                <span x-show="isExisting(position.name)" class="block text-xs text-gray-500 mt-0.5">Sudah ditambahkan pada event ini</span>
                                <span x-show="isStaged(position.id)" class="block text-xs text-brand-600 mt-0.5">Dipilih</span>
                            </span>
                        </button>
                    </template>
                </div>
            </template>

            <p x-show="categories.length === 0" class="text-sm text-gray-500 italic">Katalog posisi belum tersedia. Gunakan tambah posisi manual.</p>
        </div>

        {{-- Manual method --}}
        <div x-show="method === 'manual'" style="display:none" class="mb-6">
            <div class="border border-dashed border-brand-300 bg-brand-50/50 rounded-xl p-4 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="manual_position_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Posisi <span class="text-red-500">*</span></label>
                        <input type="text" id="manual_position_name" maxlength="255"
                               x-model="manual.name"
                               x-on:input="onManualNameInput()"
                               x-on:keydown.enter.prevent="addManual()"
                               placeholder="Contoh: Video Editor, Stage Manager, Interpreter"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm">
                    </div>
                    <div>
                        <label for="manual_position_quota" class="block text-sm font-medium text-gray-700 mb-1">Kuota <span class="text-red-500">*</span></label>
                        <input type="number" id="manual_position_quota" min="1" max="9999"
                               x-model="manual.quota"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm">
                    </div>
                </div>

                <div>
                    <label for="manual_position_category" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-xs text-gray-400 font-normal">(opsional)</span></label>
                    <select id="manual_position_category" x-model="manual.category"
                            class="w-full sm:w-72 rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm">
                        <option value="">&mdash; Pilih kategori &mdash;</option>
                        <template x-for="option in categoryOptions" :key="option">
                            <option :value="option" x-text="option"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="manual_position_description" class="block text-sm font-medium text-gray-700 mb-1">Tanggung Jawab / Deskripsi <span class="text-xs text-gray-400 font-normal">(opsional)</span></label>
                    <textarea id="manual_position_description" rows="3" maxlength="1000"
                              x-model="manual.description"
                              x-on:input="manual.descTouched = true"
                              placeholder="Deskripsikan tugas posisi ini..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm"></textarea>
                </div>

                <div>
                    <label for="manual_position_requirements" class="block text-sm font-medium text-gray-700 mb-1">Kualifikasi / Persyaratan <span class="text-xs text-gray-400 font-normal">(opsional, satu baris per poin)</span></label>
                    <textarea id="manual_position_requirements" rows="3" maxlength="1000"
                              x-model="manual.requirements"
                              x-on:input="manual.reqTouched = true"
                              placeholder="Syarat yang dibutuhkan untuk posisi ini..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm"></textarea>
                </div>

                <p x-show="manual.recommended" class="text-xs text-brand-700 bg-brand-50 border border-brand-200 rounded-lg px-3 py-2">
                    Deskripsi dan kualifikasi diisi otomatis berdasarkan nama posisi. Silakan sesuaikan jika perlu.
                </p>
                <p x-show="manual.noRecommendation" class="text-xs text-gray-500">
                    Belum ada rekomendasi untuk nama posisi ini. Anda dapat mengisi deskripsi dan kualifikasi sendiri atau membiarkannya kosong.
                </p>

                <p x-show="manualError" x-text="manualError" class="text-sm text-red-600"></p>

                <div class="flex justify-end">
                    <button type="button" x-on:click="addManual()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambahkan ke Daftar
                    </button>
                </div>
            </div>
        </div>

        {{-- Staged positions + submit --}}
        <form action="{{ route('eo.events.positions.store-many', $event) }}" method="POST">
            @csrf

            <div class="border-t border-gray-100 pt-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Posisi yang Akan Ditambahkan</h3>
                    <span class="text-xs text-gray-500"><span x-text="staged.length"></span> posisi &middot; <span x-text="totalQuota"></span> volunteer</span>
                </div>

                <div x-show="staged.length === 0" class="text-center py-8 border border-dashed border-gray-200 rounded-xl">
                    <p class="text-sm font-medium text-gray-700">Belum ada posisi yang dipilih</p>
                    <p class="text-xs text-gray-500 mt-1">Pilih posisi dari katalog atau tambahkan posisi manual di atas.</p>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in staged" :key="item.uid">
                        <div class="border border-gray-200 rounded-xl p-4">
                            <input type="hidden" :name="'positions[' + index + '][position_type_id]'" :value="item.type_id ?? ''">
                            <input type="hidden" :name="'positions[' + index + '][name]'" :value="item.name">
                            <input type="hidden" :name="'positions[' + index + '][category]'" :value="item.category ?? ''">

                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-900" x-text="item.name"></p>
                                        <span x-show="item.source === 'catalog'" class="text-xs font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full">Katalog</span>
                                        <span x-show="item.source === 'manual'" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">Manual</span>
                                        <span x-show="item.category" class="text-xs text-gray-500" x-text="item.category"></span>
                                    </div>
                                    <p x-show="item.autoFilled" class="text-xs text-brand-600 mt-1">
                                        Deskripsi &amp; kualifikasi diisi otomatis <span x-text="item.source === 'catalog' ? 'dari katalog' : 'dari rekomendasi'"></span>. Anda dapat mengeditnya.
                                    </p>
                                </div>
                                <button type="button" x-on:click="removeStaged(index)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 rounded shrink-0" aria-label="Hapus posisi dari daftar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Kuota</label>
                                    <input type="number" min="1" max="9999"
                                           :name="'positions[' + index + '][quota]'"
                                           x-model="item.quota"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggung Jawab / Deskripsi</label>
                                    <textarea rows="4" maxlength="1000"
                                              :name="'positions[' + index + '][description]'"
                                              x-model="item.description"
                                              class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Kualifikasi / Persyaratan</label>
                                    <textarea rows="4" maxlength="1000"
                                              :name="'positions[' + index + '][requirements]'"
                                              x-model="item.requirements"
                                              class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                                </div>
                            </div>

                            <div x-show="item.source === 'catalog'" class="mt-2">
                                <button type="button" x-on:click="resetToDefault(item)"
                                        class="text-xs font-medium text-brand-600 hover:text-brand-700">
                                    Kembalikan ke isian bawaan katalog
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 mt-6">
                    <button type="button" x-show="staged.length > 0"
                            x-on:click="if (confirm('Kosongkan semua posisi dalam daftar ini?')) { staged = []; }"
                            class="text-sm text-gray-500 hover:text-red-600">
                        Kosongkan daftar
                    </button>
                    <span x-show="staged.length === 0"></span>
                    <button type="submit"
                            :disabled="staged.length === 0"
                            :class="staged.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-brand-700'"
                            class="px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-lg transition-colors">
                        Simpan <span x-text="staged.length"></span> Posisi
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</div>

@push('scripts')
<script>
function positionPicker(config) {
    return {
        method: 'catalog',
        categories: config.categories || [],
        categoryOptions: config.categoryOptions || [],
        existingNames: (config.existingNames || []).map((name) => String(name).trim().toLowerCase()),
        recommendationUrl: config.recommendationUrl,
        activeCategory: (config.categories && config.categories.length) ? config.categories[0].name : null,
        staged: [],
        manual: {
            name: '',
            category: '',
            quota: 1,
            description: '',
            requirements: '',
            descTouched: false,
            reqTouched: false,
            recommended: false,
            noRecommendation: false,
        },
        manualError: '',
        recommendationTimer: null,
        uidCounter: 0,

        init() {
            (config.initial || []).forEach((old) => {
                const typeId = old.position_type_id ? Number(old.position_type_id) : null;
                const type = typeId ? this.findType(typeId) : null;

                this.staged.push(this.makeItem({
                    type_id: type ? type.id : null,
                    name: type ? type.name : (old.name || ''),
                    category: type ? type.category : (old.category || ''),
                    quota: old.quota || 1,
                    description: old.description || '',
                    requirements: old.requirements || '',
                    source: type ? 'catalog' : 'manual',
                    autoFilled: false,
                    defaultDescription: type ? type.description : '',
                    defaultRequirements: type ? type.requirements : '',
                }));
            });
        },

        makeItem(data) {
            this.uidCounter += 1;
            return Object.assign({ uid: this.uidCounter }, data);
        },

        findType(id) {
            for (const category of this.categories) {
                const found = category.positions.find((position) => position.id === id);
                if (found) {
                    return Object.assign({ category: category.name }, found);
                }
            }
            return null;
        },

        get totalQuota() {
            return this.staged.reduce((sum, item) => sum + (parseInt(item.quota, 10) || 0), 0);
        },

        stagedCountIn(category) {
            const ids = category.positions.map((position) => position.id);
            return this.staged.filter((item) => item.type_id && ids.includes(item.type_id)).length;
        },

        isStaged(typeId) {
            return this.staged.some((item) => item.type_id === typeId);
        },

        isExisting(name) {
            return this.existingNames.includes(String(name).trim().toLowerCase());
        },

        isNameStaged(name) {
            const key = String(name).trim().toLowerCase();
            return this.staged.some((item) => item.name.trim().toLowerCase() === key);
        },

        toggleCatalog(position, categoryName) {
            if (this.isExisting(position.name)) {
                return;
            }

            const index = this.staged.findIndex((item) => item.type_id === position.id);
            if (index >= 0) {
                this.staged.splice(index, 1);
                return;
            }

            this.staged.push(this.makeItem({
                type_id: position.id,
                name: position.name,
                category: categoryName,
                quota: 1,
                description: position.description,
                requirements: position.requirements,
                source: 'catalog',
                autoFilled: true,
                defaultDescription: position.description,
                defaultRequirements: position.requirements,
            }));
        },

        resetToDefault(item) {
            item.description = item.defaultDescription || '';
            item.requirements = item.defaultRequirements || '';
            item.autoFilled = true;
        },

        removeStaged(index) {
            this.staged.splice(index, 1);
        },

        onManualNameInput() {
            this.manualError = '';
            clearTimeout(this.recommendationTimer);
            this.recommendationTimer = setTimeout(() => this.fetchRecommendation(), 400);
        },

        async fetchRecommendation() {
            const name = this.manual.name.trim();

            if (name.length < 3) {
                this.clearAutoFill();
                this.manual.noRecommendation = false;
                return;
            }

            try {
                const response = await fetch(this.recommendationUrl + '?name=' + encodeURIComponent(name), {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok || name !== this.manual.name.trim()) {
                    return;
                }
                const data = await response.json();

                if (data.found) {
                    if (!this.manual.descTouched) {
                        this.manual.description = data.description;
                    }
                    if (!this.manual.reqTouched) {
                        this.manual.requirements = data.requirements;
                    }
                    this.manual.recommended = !this.manual.descTouched || !this.manual.reqTouched;
                    this.manual.noRecommendation = false;
                } else {
                    this.clearAutoFill();
                    this.manual.noRecommendation = true;
                }
            } catch (error) {
                console.error('Gagal memuat rekomendasi posisi:', error);
            }
        },

        clearAutoFill() {
            if (this.manual.recommended) {
                if (!this.manual.descTouched) {
                    this.manual.description = '';
                }
                if (!this.manual.reqTouched) {
                    this.manual.requirements = '';
                }
            }
            this.manual.recommended = false;
        },

        addManual() {
            const name = this.manual.name.trim().replace(/\s+/g, ' ');
            const quota = parseInt(this.manual.quota, 10);

            if (!name) {
                this.manualError = 'Nama posisi wajib diisi.';
                return;
            }
            if (!Number.isInteger(quota) || quota < 1) {
                this.manualError = 'Kuota minimal 1 orang.';
                return;
            }
            if (this.isExisting(name) || this.isNameStaged(name)) {
                this.manualError = 'Posisi "' + name + '" sudah ada atau sudah ada di daftar.';
                return;
            }

            this.staged.push(this.makeItem({
                type_id: null,
                name: name,
                category: this.manual.category || '',
                quota: quota,
                description: this.manual.description,
                requirements: this.manual.requirements,
                source: 'manual',
                autoFilled: this.manual.recommended,
                defaultDescription: '',
                defaultRequirements: '',
            }));

            this.manual = {
                name: '', category: '', quota: 1, description: '', requirements: '',
                descTouched: false, reqTouched: false, recommended: false, noRecommendation: false,
            };
            this.manualError = '';
        },
    };
}
</script>
@endpush
