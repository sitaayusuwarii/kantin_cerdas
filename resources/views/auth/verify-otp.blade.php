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
                    },
                    animation: {
                        'fade-up':   'fade-up 0.45s ease both',
                        'fade-up-1': 'fade-up 0.45s 0.05s ease both',
                        'fade-in':   'fade-in 0.4s ease both',
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
            background-image: radial-gradient(circle, #d1d5db 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .input-field { transition: border-color .15s, box-shadow .15s; }
        .input-field:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
        }
        .input-field.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
        }

        /* OTP box inputs */
        .otp-box {
            width: 48px; height: 56px;
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #f9fafb;
            transition: border-color .15s, box-shadow .15s, background .15s;
            caret-color: #f97316;
        }
        .otp-box:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
            background: #fff;
        }
        .otp-box.filled {
            border-color: #fb923c;
            background: #fff7ed;
            color: #ea580c;
        }
        .otp-box.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
            animation: shake 0.35s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25%       { transform: translateX(-4px); }
            75%       { transform: translateX(4px); }
        }

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

    <!-- Ambient blobs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-orange-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200 rounded-full opacity-25 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md animate-fade-up">

        <!-- Brand -->
        <div class="flex justify-center mb-6">
            <span class="text-gray-800 font-bold text-lg tracking-tight">SmartCanteen</span>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/80 border border-gray-100 p-8 animate-fade-up-1">

            <!-- Header -->
            <div class="mb-7">
                <div class="flex items-center gap-3 mb-5">
                    <img src="{{ asset('images/canteen.png') }}"
                         alt="Logo SmartCanteen"
                         class="w-12 h-12 object-contain flex-shrink-0">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Verifikasi OTP</h1>
                        <p class="text-gray-500 text-sm mt-0.5 leading-relaxed">
                            Kode dikirim ke Telegram
                            <strong class="text-gray-700">+62 {{ session('otp_phone', '8123456xxx') }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Step indicator -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-orange-100 text-orange-500 border-2 border-orange-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400">Nomor HP</span>
                    </div>
                    <div class="flex-1 h-px bg-orange-300"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-orange-500 text-white">2</div>
                        <span class="text-xs font-medium text-orange-600">Verifikasi OTP</span>
                    </div>
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-gray-100 text-gray-400 border-2 border-gray-200">3</div>
                        <span class="text-xs font-medium text-gray-400">Password Baru</span>
                    </div>
                </div>
            </div>

            <!-- Success banner -->
            @if(session('success'))
            <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm animate-fade-in">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <!-- Error alert -->
            @if ($errors->any())
            <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm animate-fade-in">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <ul class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.verify-otp') }}" id="otp-form">
                @csrf

                <!-- OTP Boxes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Masukkan 6 digit kode OTP</label>

                    <!-- Hidden input yang dikirim ke server -->
                    <input type="hidden" name="otp" id="otp-hidden">

                    <!-- Visual OTP boxes -->
                    <div class="flex justify-center gap-2" id="otp-boxes">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-box {{ $errors->has('otp') ? 'error' : '' }}"
                            data-index="0" autocomplete="one-time-code">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-box {{ $errors->has('otp') ? 'error' : '' }}"
                            data-index="1">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-box {{ $errors->has('otp') ? 'error' : '' }}"
                            data-index="2">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-box {{ $errors->has('otp') ? 'error' : '' }}"
                            data-index="3">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-box {{ $errors->has('otp') ? 'error' : '' }}"
                            data-index="4">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-box {{ $errors->has('otp') ? 'error' : '' }}"
                            data-index="5">
                    </div>

                    <!-- Countdown + resend -->
                    <div class="flex items-center justify-between mt-4">
                        <p class="text-xs text-gray-400">
                            Berlaku: <span id="countdown" class="font-semibold text-orange-500">05:00</span>
                        </p>
                        <button type="button" id="resend-trigger" disabled
                            class="text-xs font-semibold text-gray-400 hover:text-orange-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            Kirim ulang OTP
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" id="submit-btn"
                    class="btn-primary w-full py-2.5 px-4 bg-orange-600 hover:bg-orange-700 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-orange-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                    Verifikasi Kode OTP
                </button>
            </form>
             <!-- Hidden resend form -->
                <form id="resend-form" method="POST" action="{{ route('password.resend') }}" class="hidden">
                    @csrf
                </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-100"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-xs text-gray-400 font-medium">atau</span>
                </div>
            </div>

            <p class="text-center text-sm text-gray-500">
                <a href="{{ route('login') }}"
                   class="text-orange-600 hover:text-orange-700 font-semibold hover:underline transition-colors">
                    Kembali ke Login
                </a>
            </p>
        </div>

        <p class="text-center text-xs text-gray-400 mt-5">
            &copy; {{ date('Y') }} SmartCanteen
        </p>
    </div>

    <script>
        const boxes = Array.from(document.querySelectorAll('.otp-box'));
        const hiddenInput = document.getElementById('otp-hidden');
        const submitBtn = document.getElementById('submit-btn');

        function syncHidden() {
            hiddenInput.value = boxes.map(b => b.value).join('');
            const filled = boxes.every(b => b.value !== '');
            submitBtn.disabled = !filled;
        }

        boxes.forEach((box, i) => {
            box.addEventListener('input', (e) => {
                // Hanya angka
                box.value = box.value.replace(/\D/g, '').slice(-1);

                if (box.value) {
                    box.classList.add('filled');
                    box.classList.remove('error');
                    // Fokus ke box berikutnya
                    if (i < boxes.length - 1) boxes[i + 1].focus();
                } else {
                    box.classList.remove('filled');
                }
                syncHidden();
            });

            box.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !box.value && i > 0) {
                    boxes[i - 1].value = '';
                    boxes[i - 1].classList.remove('filled');
                    boxes[i - 1].focus();
                    syncHidden();
                }
                // Arrow keys
                if (e.key === 'ArrowLeft' && i > 0) boxes[i - 1].focus();
                if (e.key === 'ArrowRight' && i < boxes.length - 1) boxes[i + 1].focus();
            });

            // Handle paste (misal paste "123456")
            box.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                pasted.split('').slice(0, 6).forEach((char, j) => {
                    if (boxes[j]) {
                        boxes[j].value = char;
                        boxes[j].classList.add('filled');
                    }
                });
                const nextEmpty = boxes.find(b => !b.value);
                (nextEmpty || boxes[boxes.length - 1]).focus();
                syncHidden();
            });
        });

        // Fokus box pertama saat load
        boxes[0].focus();
        syncHidden();

        // Countdown 5 menit
        const countdownEl   = document.getElementById('countdown');
        const resendTrigger = document.getElementById('resend-trigger');
        let timeLeft = 60;

        const timer = setInterval(() => {
            timeLeft--;
            const mins = Math.floor(timeLeft / 60).toString().padStart(2, '0');
            const secs = (timeLeft % 60).toString().padStart(2, '0');
            countdownEl.textContent = `${mins}:${secs}`;

            if (timeLeft <= 0) {
                clearInterval(timer);
                countdownEl.textContent = 'Expired';
                countdownEl.classList.replace('text-orange-500', 'text-red-500');
                resendTrigger.disabled = false;
                resendTrigger.classList.remove('text-gray-400');
                resendTrigger.classList.add('text-orange-600');
            }
        }, 1000);

        resendTrigger.addEventListener('click', () => {
            document.getElementById('resend-form').submit();
        });

        // Shake animation on error (kalau ada error dari server)
        @if ($errors->has('otp'))
        boxes.forEach(b => {
            b.classList.add('error');
            setTimeout(() => b.classList.remove('error'), 400);
        });
        @endif
    </script>
</body>
</html>