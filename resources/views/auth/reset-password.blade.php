<x-guest-layout>
    <div class="w-full bg-white/90 backdrop-blur-xl rounded-[28px] shadow-[0_20px_50px_rgba(69,52,230,0.08)] hover:shadow-[0_25px_60px_rgba(69,52,230,0.12)] p-6 sm:p-10 z-10 border border-white/80 transition-all duration-300 animate-card-entry">

        <!-- Logo -->
        <div class="flex justify-center items-center mb-5 animate-stagger-item delay-50">
            <div class="flex items-center space-x-1.5">
                <span class="text-xl font-black tracking-tight text-gray-900">
                    VOLA<span class="text-indigo-600">.</span>
                </span>
            </div>
        </div>

        <!-- Heading -->
        <div class="text-center mb-7 animate-stagger-item delay-100">
            <h2 class="text-2xl sm:text-[28px] font-bold text-gray-900 tracking-tight">
                Atur Ulang Kata Sandi
            </h2>
            <p class="mt-1.5 text-sm text-gray-500 font-medium px-2 sm:px-4">
                Masukkan kata sandi baru untuk akun VOLA Anda.
            </p>
        </div>

        <!-- Reset Password Form -->
        <form method="POST"
              action="{{ route('password.store') }}"
              class="space-y-5"
              x-data="{
                  email: @js(old('email', $request->email ?? '')),
                  password: '',
                  password_confirmation: '',
                  showPassword: false,
                  showConfirmPassword: false,
                  isSubmitting: false,

                  errors: {
                      email: @js($errors->first('email')),
                      password: @js($errors->first('password')),
                      password_confirmation: @js($errors->first('password_confirmation'))
                  },

                  get passwordValidLength() {
                      return this.password.length >= 8;
                  },

                  get passwordValidCombination() {
                      return /^(?=.*[A-Za-z])(?=.*\d).+$/.test(this.password);
                  },

                  validate() {
                      this.errors.email = '';
                      this.errors.password = '';
                      this.errors.password_confirmation = '';

                      let isValid = true;

                      if (!this.email || !this.email.trim()) {
                          this.errors.email = 'Email wajib diisi.';
                          isValid = false;
                      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                          this.errors.email = 'Format email tidak valid.';
                          isValid = false;
                      }

                      if (!this.password) {
                          this.errors.password = 'Kata sandi wajib diisi.';
                          isValid = false;
                      } else if (!this.passwordValidLength) {
                          this.errors.password = 'Kata sandi minimal 8 karakter.';
                          isValid = false;
                      } else if (!this.passwordValidCombination) {
                          this.errors.password = 'Kata sandi harus mengandung huruf dan angka.';
                          isValid = false;
                      }

                      if (!this.password_confirmation) {
                          this.errors.password_confirmation = 'Konfirmasi kata sandi wajib diisi.';
                          isValid = false;
                      } else if (this.password !== this.password_confirmation) {
                          this.errors.password_confirmation = 'Konfirmasi kata sandi tidak sesuai.';
                          isValid = false;
                      }

                      return isValid;
                  },

                  handleResetSubmit(event) {
                      if (this.isSubmitting) {
                          event.preventDefault();
                          return;
                      }

                      if (!this.validate()) {
                          event.preventDefault();
                          return;
                      }

                      this.isSubmitting = true;
                  }
              }"
              @submit="handleResetSubmit($event)"
              novalidate>

            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="animate-stagger-item delay-150">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email Terdaftar
                </label>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200"
                         :class="errors.email ? 'text-red-400' : 'text-gray-400 group-hover:text-gray-600'">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>

                    <input id="email"
                           name="email"
                           type="email"
                           autocomplete="email"
                           class="block w-full pl-11 pr-4 py-3 bg-[#F8F9FA]/90 rounded-xl text-sm font-medium placeholder-gray-400 transition-all duration-200 ease-out border border-gray-200/80 hover:border-indigo-300 hover:bg-white focus:bg-white focus:outline-none"
                           :class="errors.email
                               ? 'border-red-500 ring-2 ring-red-100 shadow-[0_0_0_1px_rgba(239,68,68,0.2)]'
                               : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 focus:shadow-[0_0_20px_rgba(99,102,241,0.15)]'"
                           x-model="email"
                           @input="errors.email = ''"
                           placeholder="Masukkan email yang terdaftar"
                           {{ filled($request->email) && ! $errors->has('email') ? 'readonly' : '' }}>
                </div>

                <p x-show="errors.email"
                   x-text="errors.email"
                   class="mt-2 text-[13px] text-red-600 font-medium"
                   x-cloak>
                </p>
            </div>

            <!-- New Password -->
            <div class="animate-stagger-item delay-200">

                <!-- Label + Request New Link -->
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        Kata Sandi Baru
                    </label>

                    <a href="{{ route('password.request') }}"
                       class="text-xs sm:text-[13px] font-semibold text-[#4534E6] hover:text-indigo-800 hover:underline transition-colors whitespace-nowrap">
                        Minta tautan baru?
                    </a>
                </div>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200"
                         :class="errors.password ? 'text-red-400' : 'text-gray-400 group-hover:text-gray-600'">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>

                    <input id="password"
                           name="password"
                           x-model="password"
                           @input="errors.password = ''"
                           x-bind:type="showPassword ? 'text' : 'password'"
                           autocomplete="new-password"
                           class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA]/90 rounded-xl text-sm font-medium placeholder-gray-400 transition-all duration-200 ease-out border border-gray-200/80 hover:border-indigo-300 hover:bg-white focus:bg-white focus:outline-none"
                           :class="errors.password
                               ? 'border-red-500 ring-2 ring-red-100 shadow-[0_0_0_1px_rgba(239,68,68,0.2)]'
                               : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 focus:shadow-[0_0_20px_rgba(99,102,241,0.15)]'"
                           placeholder="Masukkan kata sandi baru">

                    <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-indigo-600 transition-colors duration-200"
                            aria-label="Toggle password visibility">

                        <!-- Password Hidden -->
                        <svg x-show="!showPassword"
                             xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" x2="22" y1="2" y2="22"/>
                        </svg>

                        <!-- Password Visible -->
                        <svg x-cloak x-show="showPassword"
                             xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <!-- Password Validation Indicators -->
                <div class="mt-2 text-[11px] font-medium space-y-1.5"
                     x-cloak
                     x-show="!errors.password">

                    <p class="flex items-center"
                       :class="passwordValidLength ? 'text-green-600' : 'text-gray-500'">

                        <svg x-show="passwordValidLength"
                             class="h-4 w-4 mr-1.5 text-green-600"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        <svg x-show="!passwordValidLength"
                             class="h-4 w-4 mr-1.5 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>

                        Minimal 8 karakter
                    </p>

                    <p class="flex items-center"
                       :class="passwordValidCombination ? 'text-green-600' : 'text-gray-500'">

                        <svg x-show="passwordValidCombination"
                             class="h-4 w-4 mr-1.5 text-green-600"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        <svg x-show="!passwordValidCombination"
                             class="h-4 w-4 mr-1.5 text-gray-400"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>

                        Mengandung huruf dan angka
                    </p>
                </div>

                <p x-show="errors.password"
                   x-text="errors.password"
                   class="mt-2 text-[13px] text-red-600 font-medium"
                   x-cloak>
                </p>
            </div>

            <!-- Confirm Password -->
            <div class="animate-stagger-item delay-250">

                <label for="password_confirmation"
                       class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Konfirmasi Kata Sandi Baru
                </label>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200"
                         :class="errors.password_confirmation ? 'text-red-400' : 'text-gray-400 group-hover:text-gray-600'">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </div>

                    <input id="password_confirmation"
                           name="password_confirmation"
                           x-model="password_confirmation"
                           @input="errors.password_confirmation = ''"
                           x-bind:type="showConfirmPassword ? 'text' : 'password'"
                           autocomplete="new-password"
                           class="block w-full pl-11 pr-12 py-3 bg-[#F8F9FA]/90 rounded-xl text-sm font-medium placeholder-gray-400 transition-all duration-200 ease-out border border-gray-200/80 hover:border-indigo-300 hover:bg-white focus:bg-white focus:outline-none"
                           :class="errors.password_confirmation
                               ? 'border-red-500 ring-2 ring-red-100 shadow-[0_0_0_1px_rgba(239,68,68,0.2)]'
                               : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 focus:shadow-[0_0_20px_rgba(99,102,241,0.15)]'"
                           placeholder="Masukkan kembali kata sandi baru">

                    <button type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-indigo-600 transition-colors duration-200"
                            aria-label="Toggle confirm password visibility">

                        <!-- Password Hidden -->
                        <svg x-show="!showConfirmPassword"
                             xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                            <line x1="2" x2="22" y1="2" y2="22"/>
                        </svg>

                        <!-- Password Visible -->
                        <svg x-cloak x-show="showConfirmPassword"
                             xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20"
                             viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <p x-show="errors.password_confirmation"
                   x-text="errors.password_confirmation"
                   class="mt-2 text-[13px] text-red-600 font-medium"
                   x-cloak>
                </p>
            </div>

            <!-- Submit Button -->
            <div class="pt-1.5 animate-stagger-item delay-300">
                <button type="submit"
                        :disabled="isSubmitting"
                        class="group relative w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-[#4534E6] hover:bg-[#3b2bc7] hover:shadow-[0_10px_25px_rgba(69,52,230,0.35)] hover:-translate-y-0.5 active:translate-y-0 active:scale-95 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">

                    <span x-show="!isSubmitting"
                          class="flex items-center justify-center transition-opacity duration-150">
                        <span>Ubah Kata Sandi</span>

                        <svg class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M14 5l7 7m0 0l-7 7m7-7H3">
                            </path>
                        </svg>
                    </span>

                    <span x-cloak x-show="isSubmitting"
                          class="flex items-center justify-center transition-opacity duration-150">

                        <svg class="animate-spin -ml-1 mr-2.5 h-4 w-4 text-white"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25"
                                    cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75"
                                  fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>

                        <span>Memproses...</span>
                    </span>
                </button>
            </div>

            <!-- Back to Login -->
            <div class="text-center mt-4 text-sm text-gray-500 font-medium animate-stagger-item delay-350">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-1 font-bold text-[#4534E6] hover:text-indigo-800 hover:underline transition-colors">
                    Kembali ke Masuk
                </a>
            </div>

        </form>
    </div>
</x-guest-layout>
