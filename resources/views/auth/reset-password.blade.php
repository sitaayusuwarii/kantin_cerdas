<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP — Smart Canteen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    keyframes: {
                        'fade-up': {
                            '0%':   { opacity: '0', transform: 'translateY(16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        'fade-in': {
                            '0%':   { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        'slide-right': {
                            '0%':   { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                    },
                    animation: {
                        'fade-up':    'fade-up 0.45s ease both',
                        'fade-up-1':  'fade-up 0.45s 0.05s ease both',
                        'fade-in':    'fade-in 0.4s ease both',
                        'slide-right':'slide-right 0.4s ease both',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .dot-pattern {
            background-image: radial-gradient(circle, #e9d5ff 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .input-field { transition: border-color .15s, box-shadow .15s; }
        .input-field:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
        }
        .input-field.error {
            border-color: #f87171;
            box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.12);
        }
        .otp-input { letter-spacing: 0.35em; font-variant-numeric: tabular-nums; }
        .btn-primary { position: relative; overflow: hidden; }
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.18) 50%, transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .btn-primary:hover::after { transform: translateX(100%); }
        #countdown { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="min-h-screen bg-gray-100 dot-pattern flex items-center justify-center p-4">

    <!-- Ambient blobs (Z-index lowered) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10" aria-hidden="true">
        <div class="absolute -top-40 -left-32 w-96 h-96 bg-purple-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-32 w-96 h-96 bg-violet-100 rounded-full opacity-35 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Brand mark -->
        <div class="flex justify-center mb-6 animate-fade-up">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center justify-center shadow-lg shadow-purple-200">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                    </svg>
                </div>
                <span class="text-gray-800 font-bold text-lg tracking-tight">SmartCanteen</span>
            </div>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden animate-fade-up-1">
            <div class="h-1 w-full bg-gradient-to-r from-purple-500 via-violet-500 to-fuchsia-500"></div>

            <div class="p-8">
                <!-- Step indicator -->
                <div class="flex items-center gap-2 mb-7">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-purple-100 text-purple-600 border-2 border-purple-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400">Nomor HP</span>
                    </div>
                    <div class="flex-1 h-px bg-purple-400"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-purple-600 text-white">2</div>
                        <span class="text-xs font-medium text-purple-700">Verifikasi</span>
                    </div>
                </div>

                <!-- Header -->
                <div class="mb-6 animate-slide-right">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Verifikasi OTP</h1>
                    <p class="text-gray-500 text-sm mt-1.5 leading-relaxed">
                        Kode OTP telah dikirim ke Telegram nomor 
                        <strong class="text-gray-700">+62 {{ session('otp_phone', '8123456xxx') }}</strong>
                    </p>
                </div>

                <!-- Status Banner -->
                @if(session('success'))
                <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm animate-fade-in">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                @if ($errors->any())
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-red-700">
        <p class="font-bold">Gagal menyimpan:</p>
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                <!-- Form Utama -->
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5" id="main-form">
                    @csrf
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700 mb-1.5">Kode OTP</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268"/>
                                </svg>
                            </span>
                            <input id="otp" type="text" name="otp" maxlength="6" inputmode="numeric" required
                                class="otp-input input-field w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border rounded-xl text-center font-mono {{ $errors->has('otp') ? 'border-red-400 error' : 'border-gray-200' }}"
                                placeholder="******">
                        </div>
                        
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-xs text-gray-400">Berlaku: <span id="countdown" class="font-semibold text-purple-600">05:00</span></p>
                            <!-- Tombol resend dipindah agar tidak nested form secara visual tetap di sini -->
                            <button type="button" id="resend-trigger" disabled
                                class="text-xs font-semibold text-gray-400 hover:text-purple-600 disabled:opacity-40 disabled:cursor-not-allowed">
                                Kirim ulang OTP
                            </button>
                        </div>
                    </div>
                                    
                <!-- Input Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                            class="input-field w-full pl-4 pr-11 py-2.5 text-sm bg-gray-50 border rounded-xl {{ $errors->has('password') ? 'border-red-400 error' : 'border-gray-200' }}"
                            placeholder="Minimal 8 karakter">
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 text-gray-400">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Strength meter cukup di password utama saja agar tidak penuh -->
                    <div class="mt-2 flex gap-1" id="strength-meter">
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                    </div>
                    <p id="str-label" class="text-[10px] mt-1 uppercase font-bold tracking-wider"></p>
                </div>

                <!-- Input Konfirmasi Password Baru -->
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="input-field w-full pl-4 pr-11 py-2.5 text-sm bg-gray-50 border rounded-xl {{ $errors->has('password_confirmation') ? 'border-red-400 error' : 'border-gray-200' }}"
                            placeholder="Ulangi password baru">
                        <button type="button" id="toggle-confirm-password" class="absolute inset-y-0 right-0 pr-3.5 text-gray-400">
                            <svg id="eye-icon-confirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full mt-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg shadow-purple-100 transition-all">
                    Simpan Password Baru
                </button>

                <!-- Hidden Resend Form (Mencegah Nested Form) -->
                <form id="resend-form" method="POST" action="{{ route('password.resend') }}" class="hidden">
                    @csrf
                </form>

                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-purple-600 font-medium"> Kembali ke Login </a>
                </div>
            </div>
        </div>
        <p class="text-center text-[10px] text-gray-400 mt-6 uppercase tracking-widest">&copy; 2026 SmartCanteen — SMAN 1</p>
    </div>

    <script>
        // 1. Logic OTP (Hanya Angka)
        const otpInput = document.getElementById('otp');
        otpInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Toggle Password Utama
        const pwInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-password');
        toggleBtn.addEventListener('click', () => {
            pwInput.type = pwInput.type === 'password' ? 'text' : 'password';
        });

        // Toggle Konfirmasi Password
        const confirmInput = document.getElementById('password_confirmation');
        const toggleConfirmBtn = document.getElementById('toggle-confirm-password');
        toggleConfirmBtn.addEventListener('click', () => {
            confirmInput.type = confirmInput.type === 'password' ? 'text' : 'password';
        });
        
        // 3. Password Strength
        const bars = document.querySelectorAll('#strength-meter div');
        const label = document.getElementById('str-label');
        pwInput.addEventListener('input', () => {
            const val = pwInput.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
            const texts = ['Lemah', 'Sedang', 'Cukup', 'Sangat Kuat'];
            
            bars.forEach((b, i) => {
                b.className = `h-1 flex-1 rounded-full transition-all ${i < score ? colors[score-1] : 'bg-gray-200'}`;
            });
            label.textContent = val ? texts[score-1] : '';
            label.className = `text-[10px] mt-1 uppercase font-bold tracking-wider ${val ? 'text-' + colors[score-1].split('-')[1] + '-500' : ''}`;
        });

        // 4. Countdown & Resend logic
        const countdownEl = document.getElementById('countdown');
        const resendTrigger = document.getElementById('resend-trigger');
        let timeLeft = 300; // 5 menit

        const timer = setInterval(() => {
            timeLeft--;
            const mins = Math.floor(timeLeft / 60).toString().padStart(2, '0');
            const secs = (timeLeft % 60).toString().padStart(2, '0');
            countdownEl.textContent = `${mins}:${secs}`;

            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.textContent = "Expired";
                countdownEl.classList.replace('text-purple-600', 'text-red-500');
                resendTrigger.disabled = false;
                resendTrigger.classList.replace('text-gray-400', 'text-purple-600');
            }
        }, 1000);

        resendTrigger.addEventListener('click', () => {
            document.getElementById('resend-form').submit();
        });
    </script>
</body>
</html>