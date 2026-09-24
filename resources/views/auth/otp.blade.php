<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Verifikasi Kode OTP — {{ config('app.name', 'Google Notes') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: #F8F9FA;
                min-height: 100vh;
            }
            .otp-input {
                width: 48px;
                height: 56px;
                text-align: center;
                font-size: 24px;
                font-weight: 700;
                border-radius: 12px;
                border: 2px solid #E5E7EB;
                background: #ffffff;
                color: #202124;
                transition: all 0.2s ease;
                outline: none;
            }
            .otp-input:focus {
                border-color: #4285F4;
                box-shadow: 0 0 0 4px rgba(66,133,244,0.2);
                transform: translateY(-2px);
            }
            .btn-google {
                background: #4285F4;
                color: white;
                box-shadow: 0 4px 14px rgba(66,133,244,0.3);
                transition: all 0.2s ease;
            }
            .btn-google:hover {
                background: #3367D6;
                transform: translateY(-1px);
                box-shadow: 0 6px 20px rgba(66,133,244,0.4);
            }
        </style>
    </head>
    <body class="font-sans antialiased flex items-center justify-center p-4">
        <div class="max-w-md w-full" x-data="{
            digits: ['', '', '', '', '', ''],
            timer: 60,
            canResend: false,
            interval: null,

            init() {
                this.startTimer();
                this.$nextTick(() => {
                    if (this.$refs.digit0) this.$refs.digit0.focus();
                });
            },

            startTimer() {
                this.timer = 60;
                this.canResend = false;
                if (this.interval) clearInterval(this.interval);
                this.interval = setInterval(() => {
                    if (this.timer > 0) {
                        this.timer--;
                    } else {
                        this.canResend = true;
                        clearInterval(this.interval);
                    }
                }, 1000);
            },

            handleInput(index, event) {
                const val = event.target.value.replace(/[^0-9]/g, '');
                this.digits[index] = val ? val.slice(-1) : '';
                event.target.value = this.digits[index];

                if (this.digits[index] && index < 5) {
                    const nextInput = this.$refs['digit' + (index + 1)];
                    if (nextInput) nextInput.focus();
                }
            },

            handleKeyDown(index, event) {
                if (event.key === 'Backspace' && !this.digits[index] && index > 0) {
                    const prevInput = this.$refs['digit' + (index - 1)];
                    if (prevInput) {
                        prevInput.focus();
                        this.digits[index - 1] = '';
                    }
                }
            },

            handlePaste(event) {
                event.preventDefault();
                const paste = (event.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                if (paste.length >= 6) {
                    for (let i = 0; i < 6; i++) {
                        this.digits[i] = paste[i];
                        if (this.$refs['digit' + i]) this.$refs['digit' + i].value = paste[i];
                    }
                    this.$refs.digit5.focus();
                }
            },

            fillOtp(code) {
                const str = String(code);
                for (let i = 0; i < 6; i++) {
                    this.digits[i] = str[i] || '';
                    if (this.$refs['digit' + i]) this.$refs['digit' + i].value = str[i] || '';
                }
                this.$refs.digit5.focus();
            }
        }">
            <!-- Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100 relative overflow-hidden">
                <!-- Header Icon -->
                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-6 text-3xl shadow-sm">
                    🔐
                </div>

                <h2 class="text-2xl font-bold text-center text-[#202124] mb-1">Verifikasi Kode OTP</h2>
                <p class="text-sm text-center text-gray-500 mb-6">
                    Kode verifikasi 6-digit telah dikirimkan ke email<br>
                    <strong class="text-[#202124] font-semibold">{{ $email }}</strong>
                </p>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 text-xs rounded-xl text-center">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl text-center">
                        {{ session('info') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl text-center font-medium">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- OTP Form -->
                <form action="{{ route('auth.otp.verify') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- 6 Digit Input Boxes -->
                    <div class="flex justify-center items-center gap-2 sm:gap-3" @paste="handlePaste($event)">
                        <template x-for="(digit, index) in digits" :key="index">
                            <input type="text"
                                   :x-ref="'digit' + index"
                                   name="otp_digits[]"
                                   maxlength="1"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   class="otp-input"
                                   @input="handleInput(index, $event)"
                                   @keydown="handleKeyDown(index, $event)"
                                   required>
                        </template>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 rounded-xl font-semibold text-sm btn-google">
                        Verifikasi & Masuk
                    </button>
                </form>

                <!-- Resend OTP Section -->
                <div class="mt-6 text-center border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-500 mb-2">Tidak menerima kode?</p>
                    <form action="{{ route('auth.otp.resend') }}" method="POST">
                        @csrf
                        <button type="submit"
                                :disabled="!canResend"
                                class="text-xs font-semibold text-[#4285F4] hover:text-[#3367D6] disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <span x-show="!canResend">Kirim Ulang dalam <strong x-text="timer"></strong> detik</span>
                            <span x-show="canResend" class="underline">Kirim Ulang Kode OTP</span>
                        </button>
                    </form>
                </div>

                <!-- Back to Home -->
                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                        ← Batal & Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
