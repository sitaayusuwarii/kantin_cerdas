<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — Smart Canteen</title>
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
                        'fade-up-2':  'fade-up 0.45s 0.10s ease both',
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

        .btn-primary {
            position: relative;
            overflow: hidden;
        }
        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.18) 50%, transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .btn-primary:hover::after { transform: translateX(100%); }
    </style>
</head>
<body class="min-h-screen bg-gray-100 dot-pattern flex items-center justify-center p-4">

    {{-- Ambient blobs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-40 -left-32  w-96 h-96 bg-purple-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-32 w-96 h-96 bg-violet-100 rounded-full opacity-35 blur-3xl"></div>
        <div class="absolute top-1/3 right-1/4 w-48 h-48 bg-fuchsia-100 rounded-full opacity-25 blur-2xl"></div>
    </div>

    <div class="relative w-full max-w-md">

        {{-- Brand mark --}}
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

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden animate-fade-up-1">

            {{-- Top accent --}}
            <div class="h-1 w-full bg-gradient-to-r from-purple-500 via-violet-500 to-fuchsia-500"></div>

            <div class="p-8">

                {{-- Step indicator --}}
                <div class="flex items-center gap-2 mb-7 animate-fade-up-1">
                    {{-- Step 1 (active) --}}
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-purple-600 text-white">
                            1
                        </div>
                        <span class="text-xs font-medium text-purple-700">Nomor HP</span>
                    </div>

                    {{-- Connector --}}
                    <div class="flex-1 h-px bg-gray-200"></div>

                    {{-- Step 2 (inactive) --}}
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-gray-100 text-gray-400 border-2 border-gray-200">
                            2
                        </div>
                        <span class="text-xs font-medium text-gray-400">Verifikasi</span>
                    </div>
                </div>

                {{-- Header --}}
                <div class="mb-6 animate-slide-right">
                    <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2.5 py-1 rounded-full mb-3">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                        </svg>
                        Langkah 1 dari 2
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Reset Password</h1>
                    <p class="text-gray-500 text-sm mt-1.5 leading-relaxed">
                        Masukkan nomor HP untuk menerima kode OTP via
                        <span class="inline-flex items-center gap-1 text-blue-500 font-semibold">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                            Telegram
                        </span>
                    </p>
                </div>

                {{-- Success message --}}
                @if (session('status'))
                <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm animate-fade-in">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                    {{ session('status') }}
                </div>
                @endif

                {{-- Error alert --}}
                @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
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

                {{-- Form --}}
                <form method="POST" action="{{ route('password.phone') }}" class="space-y-5" novalidate>
                    @csrf

                    {{-- Nomor HP --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor HP</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <svg class="w-4 h-4 {{ $errors->has('phone') ? 'text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                                </svg>
                            </span>
                            <span class="absolute inset-y-0 left-9 flex items-center pointer-events-none">
                                <span class="text-xs font-semibold text-gray-400 border-r border-gray-200 pr-2.5 leading-none">+62</span>
                            </span>
                            <input
                                id="phone"
                                type="tel"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="8xx xxxx xxxx"
                                autocomplete="tel"
                                required
                                class="input-field w-full pl-20 pr-4 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl placeholder-gray-400
                                    {{ $errors->has('phone') ? 'border-red-400 error' : 'border-gray-200' }}"
                            >
                        </div>
                        @error('phone')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Info box --}}
                    <div class="flex items-start gap-3 p-3.5 bg-purple-50 border border-purple-100 rounded-xl">
                        <svg class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-9-3.75h.008v.008H12V8.25z"/>
                        </svg>
                        <p class="text-xs text-purple-700 leading-relaxed">
                            Pastikan nomor HP sudah terhubung ke akun Telegram Anda. Kode OTP berlaku selama <strong>5 menit</strong>.
                        </p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="send-btn"
                        class="btn-primary w-full py-2.5 px-4 bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-purple-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12zm0 0h7.5"/>
                        </svg>
                        <span id="send-label">Kirim OTP</span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-3 text-xs text-gray-400 font-medium">atau</span>
                    </div>
                </div>

                {{-- Back to login --}}
                <p class="text-center text-sm text-gray-500">
                    Ingat password Anda?
                    <a href="{{ route('login') }}"
                       class="text-purple-600 hover:text-purple-700 font-semibold hover:underline transition-colors ml-1">
                        Kembali Login
                    </a>
                </p>

            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-5">
            &copy; {{ date('Y') }} SmartCanteen · SMA Negeri 1
        </p>
    </div>

    <script>
        // ── Phone only numbers ────────────────────────────────────────────────
        const hpInput = document.getElementById('phone');
        if (hpInput) {
            hpInput.addEventListener('keypress', e => { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
        }

        // ── Send OTP loading state ────────────────────────────────────────────
        const sendBtn   = document.getElementById('send-btn');
        const sendLabel = document.getElementById('send-label');
        if (sendBtn) {
            sendBtn.closest('form').addEventListener('submit', function (e) {
                const hp = this.elements['phone'];
                if (!hp.value.trim() || !/^[0-9]{8,13}$/.test(hp.value)) {
                    hp.classList.add('error', 'border-red-400');
                    hp.classList.remove('border-gray-200');
                    e.preventDefault();
                    return;
                }
                sendBtn.disabled = true;
                sendBtn.classList.add('opacity-80');
                sendLabel.textContent = 'Mengirim OTP...';
            });
        }

        // ── Clear error state on input ────────────────────────────────────────
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('input', function () {
                if (this.value.trim()) {
                    this.classList.remove('error', 'border-red-400');
                    this.classList.add('border-gray-200');
                }
            });
        });
    </script>
</body>
</html>