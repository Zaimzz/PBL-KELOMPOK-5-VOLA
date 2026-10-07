<x-layouts.eo title="Edit Event — Vola">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('eo.events.index') }}" class="hover:text-gray-700">Event Saya</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900 font-medium truncate max-w-xs">{{ $event->title }}</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <h1 class="text-2xl font-bold text-gray-900">Edit Event</h1>
                <x-badge-status :status="$event->status->value" />
            </div>
            @if ($event->status->value === 'rejected' && $event->rejection_reason)
                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg max-w-xl">
                    <p class="text-sm text-red-700"><strong>Alasan Penolakan Admin:</strong> {{ $event->rejection_reason }}</p>
                </div>
            @endif
            @if ($event->status->value === 'pending_review')
                <x-alert type="warning" class="mt-2 max-w-xl">
                    Event ini sedang dalam proses review admin dan tidak dapat diedit.
                </x-alert>
            @endif
        </div>

        @if (in_array($event->status->value, ['draft', 'rejected']))
            <form action="{{ route('eo.events.submit', $event) }}" method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Kirim event ini untuk review admin?')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $event->status->value === 'rejected' ? 'Submit Ulang untuk Review' : 'Kirim untuk Review' }}
                </button>
            </form>
        @endif
    </div>

    @php $isLocked = ! in_array($event->status->value, ['draft', 'rejected']); @endphp

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT: Event Form --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Section 1: Basic Info --}}
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <h2 class="text-base font-semibold text-gray-900">Informasi Dasar Event</h2>
                </div>

                <form action="{{ route('eo.events.update', $event) }}" method="POST" enctype="multipart/form-data" id="event-form"
                      x-data="editEventForm()"
                      x-init="$nextTick(() => { validateDeadline(); validateEndDate(); })">
                    @csrf
                    @method('PUT')

                    {{-- Poster --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Poster / Banner Acara</label>

                        @if ($event->poster_path)
                            <div class="mb-3 relative inline-block" x-show="!posterPreview">
                                <img src="{{ Storage::url($event->poster_path) }}"
                                     alt="Poster event"
                                     class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                                <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">Poster aktif</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-2" x-show="!posterPreview">Upload poster baru untuk menggantikan yang lama.</p>
                        @endif

                        @if (! $isLocked)
                            {{-- Dropzone --}}
                            <div class="border-2 border-dashed border-gray-200 rounded-xl transition-colors cursor-pointer min-h-[140px] flex flex-col justify-center"
                                 :class="posterError ? '!border-red-400 bg-red-50' : (posterPreview ? '!border-brand-400' : 'hover:border-brand-400')"
                                 x-on:dragover.prevent
                                 x-on:drop.prevent="handleDrop($event)"
                                 x-on:click="$refs.posterInput.click()">

                                <input type="file" name="poster" id="poster" accept=".jpg,.jpeg,.png,.webp"
                                       x-ref="posterInput"
                                       class="hidden"
                                       x-on:change="previewPoster($event)">

                                {{-- Empty state: visible sebelum Alpine init --}}
                                <div x-show="!posterPreview" style="display:block" class="p-6 text-center">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">Klik atau tarik file ke sini</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP &mdash; maks 2 MB</p>
                                </div>

                                {{-- Preview state --}}
                                <div x-show="posterPreview" class="p-4">
                                    <img :src="posterPreview" class="max-h-48 mx-auto rounded-lg object-contain block" alt="Preview poster baru">
                                    <div class="mt-3 flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-xs text-gray-600 truncate" x-text="posterFileName"></span>
                                            <span class="text-xs text-gray-400 shrink-0" x-text="posterFileSize"></span>
                                        </div>
                                        <button type="button" x-on:click.stop="clearPoster()"
                                                class="ml-2 text-xs text-red-500 hover:text-red-700 font-medium shrink-0">Hapus</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Client-side error --}}
                            <div x-show="posterError" class="mt-2 flex items-center gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.19 2.5 1.732 2.5z"/>
                                </svg>
                                <p class="text-sm text-red-600" x-text="posterError"></p>
                            </div>
                        @endif

                        @error('poster')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Nama Event <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title"
                                   value="{{ old('title', $event->title) }}"
                                   {{ $isLocked ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }} @error('title') border-red-300 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category & Deadline --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                <select name="category_id" id="category_id"
                                        {{ $isLocked ? 'disabled' : '' }}
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="registration_deadline" class="block text-sm font-medium text-gray-700 mb-1">Batas Pendaftaran</label>
                                <input type="date" name="registration_deadline" id="registration_deadline"
                                       value="{{ old('registration_deadline', $event->registration_deadline?->format('Y-m-d')) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       @if(! $isLocked) x-on:change="validateDeadline()" @endif
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                                @if(! $isLocked)
                                    <p x-show="deadlineError" x-text="deadlineError" class="mt-1 text-sm text-red-600" style="display:none"></p>
                                @endif
                                @error('registration_deadline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Dates --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date"
                                       value="{{ old('start_date', $event->start_date?->format('Y-m-d')) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       @if(! $isLocked) x-model="startDateVal" x-on:change="onStartDateChange()" @endif
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="end_date" id="end_date"
                                       value="{{ old('end_date', $event->end_date?->format('Y-m-d')) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       @if(! $isLocked) x-on:change="validateEndDate()" @endif
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                                @if(! $isLocked)
                                    <p x-show="endDateError" x-text="endDateError" class="mt-1 text-sm text-red-600" style="display:none"></p>
                                @endif
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
                                       value="{{ old('start_time', $event->start_time) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                                <input type="time" name="end_time" id="end_time"
                                       value="{{ old('end_time', $event->end_time) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>
                        </div>

                        {{-- Location --}}
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Acara</label>
                            <input type="text" name="location" id="location"
                                   value="{{ old('location', $event->location) }}"
                                   {{ $isLocked ? 'disabled' : '' }}
                                   placeholder="Alamat lengkap lokasi acara"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- City --}}
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                            <input type="text" name="city" id="city"
                                   value="{{ old('city', $event->city) }}"
                                   {{ $isLocked ? 'disabled' : '' }}
                                   placeholder="Nama kota"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Acara</label>
                            <textarea name="description" id="description"
                                      rows="4"
                                      {{ $isLocked ? 'disabled' : '' }}
                                      placeholder="Deskripsikan event Anda..."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }} @error('description') border-red-300 @enderror">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Info --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-1">Info Kontak</label>
                                <input type="text" name="contact_info" id="contact_info"
                                       value="{{ old('contact_info', $event->contact_info) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>
                            <div>
                                <label for="pic_name" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                                <input type="text" name="pic_name" id="pic_name"
                                       value="{{ old('pic_name', $event->pic_name) }}"
                                       {{ $isLocked ? 'disabled' : '' }}
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-sm {{ $isLocked ? 'bg-gray-50 text-gray-500' : '' }}">
                            </div>
                        </div>

                    </div>

                    @if (! $isLocked)
                        <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    @endif
                </form>
            </x-card>

        </div>

        {{-- RIGHT: Positions Panel --}}
        <div class="xl:col-span-1 space-y-6">

            {{-- Positions --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <h2 class="text-sm font-semibold text-gray-900">Kebutuhan Posisi</h2>
                    </div>
                    <span class="text-xs font-medium text-brand-600 bg-brand-50 px-2 py-1 rounded-full">
                        Total: {{ $event->positions->count() }} Posisi
                    </span>
                </div>

                @if ($event->positions->isEmpty())
                    <x-empty-state
                        title="Belum ada posisi"
                        description="Tambah minimal 1 posisi untuk dapat mengirim event."
                    />
                @else
                    <div class="space-y-3 mb-4">
                        @foreach ($event->positions as $position)
                            <div class="border border-gray-200 rounded-lg p-3" x-data="{ editing: false }">
                                <div x-show="!editing">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm text-gray-900">{{ $position->name }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Kuota: <strong>{{ $position->quota }}</strong> orang</p>
                                            @if ($position->description)
                                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $position->description }}</p>
                                            @endif
                                        </div>
                                        @if (! $isLocked)
                                            <div class="flex items-center gap-1 shrink-0">
                                                <button type="button"
                                                        x-on:click="editing = true"
                                                        class="p-1.5 text-gray-400 hover:text-brand-600 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <form action="{{ route('eo.events.positions.destroy', [$event, $position]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            onclick="return confirm('Hapus posisi ini?')"
                                                            class="p-1.5 text-gray-400 hover:text-red-600 rounded">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Edit Position Form --}}
                                @if (! $isLocked)
                                    <form action="{{ route('eo.events.positions.update', [$event, $position]) }}" method="POST" x-show="editing" x-cloak>
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Posisi</label>
                                                <input type="text" name="name" value="{{ $position->name }}" required
                                                       class="w-full rounded-lg border-gray-300 text-xs focus:border-brand-500 focus:ring-brand-500">
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Kuota</label>
                                                    <input type="number" name="quota" value="{{ $position->quota }}" min="1" required
                                                           class="w-full rounded-lg border-gray-300 text-xs focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi</label>
                                                <textarea name="description" rows="2"
                                                          class="w-full rounded-lg border-gray-300 text-xs focus:border-brand-500 focus:ring-brand-500">{{ $position->description }}</textarea>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Persyaratan</label>
                                                <textarea name="requirements" rows="2"
                                                          class="w-full rounded-lg border-gray-300 text-xs focus:border-brand-500 focus:ring-brand-500">{{ $position->requirements }}</textarea>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit"
                                                        class="flex-1 py-1.5 bg-brand-600 text-white text-xs font-medium rounded-lg hover:bg-brand-700">
                                                    Simpan
                                                </button>
                                                <button type="button" x-on:click="editing = false"
                                                        class="flex-1 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Add Position Form --}}
                @if (! $isLocked)
                    <div x-data="{ showForm: {{ $event->positions->isEmpty() ? 'true' : 'false' }} }">
                        <button type="button"
                                x-show="!showForm"
                                x-on:click="showForm = true"
                                class="w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-gray-200 rounded-lg text-sm text-brand-600 hover:border-brand-400 hover:bg-brand-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Divisi Lain
                        </button>

                        <form action="{{ route('eo.events.positions.store', $event) }}" method="POST" x-show="showForm" x-cloak>
                            @csrf
                            <div class="border border-dashed border-brand-300 bg-brand-50 rounded-lg p-4 space-y-3">
                                <p class="text-xs font-semibold text-brand-700">Tambah Posisi Baru</p>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nama Posisi <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required
                                           placeholder="Contoh: Liaison Officer (LO)"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Kuota <span class="text-red-500">*</span></label>
                                    <input type="number" name="quota" min="1" value="1" required
                                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    @error('quota')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggung Jawab / Deskripsi</label>
                                    <textarea name="description" rows="2"
                                              placeholder="Deskripsikan tugas posisi ini..."
                                              class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Kualifikasi / Persyaratan</label>
                                    <textarea name="requirements" rows="2"
                                              placeholder="Syarat yang dibutuhkan untuk posisi ini..."
                                              class="w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                            class="flex-1 py-2 bg-brand-600 text-white text-sm font-medium rounded-lg hover:bg-brand-700 transition-colors">
                                        Tambah Posisi
                                    </button>
                                    @if (! $event->positions->isEmpty())
                                        <button type="button"
                                                x-on:click="showForm = false"
                                                class="px-4 py-2 bg-white text-gray-600 text-sm rounded-lg border border-gray-300 hover:bg-gray-50">
                                            Batal
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    @if ($event->positions->isEmpty())
                        <p class="text-xs text-gray-400 text-center italic">Tidak ada posisi. Edit tidak tersedia saat event terkunci.</p>
                    @endif
                @endif
            </x-card>

            {{-- Danger Zone (delete) --}}
            @if (in_array($event->status->value, ['draft', 'rejected']))
                <x-card>
                    <h3 class="text-sm font-semibold text-red-700 mb-2">Zona Berbahaya</h3>
                    <p class="text-xs text-gray-500 mb-3">Menghapus event ini bersifat permanen dan tidak dapat dibatalkan.</p>
                    <form action="{{ route('eo.events.destroy', $event) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.')"
                                class="w-full py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            Hapus Event Ini
                        </button>
                    </form>
                </x-card>
            @endif
        </div>
    </div>

</x-layouts.eo>

@push('scripts')
<script>
function editEventForm() {
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

        // --- Dates ---
        todayStr: `${yyyy}-${mm}-${dd}`,
        startDateVal: '{{ old('start_date', $event->start_date?->format('Y-m-d') ?? '') }}',
        endDateError: '',
        deadlineError: '',

        previewPoster(event) {
            const file = event.target.files[0];
            if (!file) return;
            this._handleFile(file);
        },

        handleDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;
            this.$refs.posterInput.files = event.dataTransfer.files;
            this._handleFile(file);
        },

        _handleFile(file) {
            this.posterError = '';
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            const maxBytes = 2 * 1024 * 1024;

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

        onStartDateChange() {
            this.validateEndDate();
            this.validateDeadline();
        },

        validateEndDate() {
            const endEl = document.getElementById('end_date');
            if (!endEl || !endEl.value || !this.startDateVal) { this.endDateError = ''; return; }
            this.endDateError = endEl.value < this.startDateVal
                ? 'Tanggal selesai tidak boleh sebelum tanggal mulai.'
                : '';
        },

        validateDeadline() {
            const dlEl = document.getElementById('registration_deadline');
            if (!dlEl || !dlEl.value) { this.deadlineError = ''; return; }
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
</script>
@endpush
