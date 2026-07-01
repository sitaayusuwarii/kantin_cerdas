<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Smart Canteen</title>
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
                        'slide-in': {
                            '0%':   { opacity: '0', transform: 'translateX(-12px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                    },
                    animation: {
                        'fade-up':   'fade-up 0.5s ease both',
                        'fade-up-1': 'fade-up 0.5s 0.05s ease both',
                        'fade-up-2': 'fade-up 0.5s 0.10s ease both',
                        'fade-up-3': 'fade-up 0.5s 0.15s ease both',
                        'fade-up-4': 'fade-up 0.5s 0.20s ease both',
                        'fade-up-5': 'fade-up 0.5s 0.25s ease both',
                        'fade-up-6': 'fade-up 0.5s 0.30s ease both',
                        'slide-in':  'slide-in 0.4s ease both',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ✅ DIGANTI: dot pattern abu seperti login */
        .dot-pattern {
            background-image: radial-gradient(circle, #d1d5db 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .input-field { transition: border-color .15s, box-shadow .15s; }

        /* ✅ DIGANTI: focus orange seperti login */
        .input-field:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
        }
        .input-field.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.10);
        }

        .btn-register {
            position: relative;
            overflow: hidden;
        }
        .btn-register::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.18) 50%, transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .btn-register:hover::after { transform: translateX(100%); }
    </style>
</head>
<body class="min-h-screen bg-gray-100 dot-pattern flex items-center justify-center p-4">

    {{-- ✅ DIGANTI: blob orange seperti login --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-orange-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-200 rounded-full opacity-25 blur-3xl"></div>
    </div>

    {{-- ✅ DIGANTI: max-w-md sama seperti login --}}
    <div class="relative w-full max-w-md animate-fade-up">

        {{-- ✅ DIGANTI: brand mark sama seperti login --}}
        <div class="flex justify-center mb-6 animate-fade-up">
            <div class="flex items-center gap-2.5">
                <span class="text-gray-800 font-bold text-lg tracking-tight">SmartCanteen</span>
            </div>
        </div>

        {{-- ✅ DIGANTI: card style sama seperti login, hapus accent bar hijau --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/80 border border-gray-100 p-8">

            {{-- ✅ DIGANTI: header dengan logo opsi 1 (logo + teks sejajar) --}}
            <div class="mb-7 animate-fade-up-1">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/canteen.png') }}"
                         alt="Logo SmartCanteen"
                         class="w-12 h-12 object-contain flex-shrink-0">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Buat Akun Baru</h1>
                        <p class="text-gray-500 text-sm mt-0.5 leading-relaxed">Daftar untuk mulai menggunakan aplikasi</p>
                    </div>
                </div>
            </div>

            {{-- Global error alert --}}
            @if ($errors->any())
            <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm animate-slide-in">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <div>
                    <p class="font-semibold mb-1">Periksa kembali form Anda:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
                @csrf

                {{-- Username --}}
                <div class="animate-fade-up-2">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 {{ $errors->has('username') ? 'text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </span>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username Anda"
                            autocomplete="username"
                            required
                            class="input-field w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl placeholder-gray-400
                                {{ $errors->has('username') ? 'border-red-400 error' : 'border-gray-200' }}"
                        >
                    </div>
                    @error('username')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Nomor HP --}}
                <div class="animate-fade-up-3">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nomor HP
                    </label>
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
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="animate-fade-up-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 {{ $errors->has('password') ? 'text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Buat password yang kuat"
                            autocomplete="new-password"
                            required
                            class="input-field w-full pl-10 pr-11 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl placeholder-gray-400
                                {{ $errors->has('password') ? 'border-red-400 error' : 'border-gray-200' }}"
                        >
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 transition-colors"
                            aria-label="Toggle password visibility">
                            <svg id="eye-show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                            <svg id="eye-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Password strength meter --}}
                    <div class="mt-2">
                        <div class="flex gap-1 mb-1">
                            <div id="str-1" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                            <div id="str-2" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                            <div id="str-3" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                            <div id="str-4" class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                        </div>
                        <p id="str-label" class="text-xs text-gray-400"></p>
                    </div>

                    @error('password')
                    <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="animate-fade-up-5">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 {{ $errors->has('password_confirmation') ? 'text-red-400' : 'text-gray-400' }}"
                                fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                            </svg>
                        </span>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            required
                            class="input-field w-full pl-10 pr-11 py-2.5 text-sm text-gray-900 bg-gray-50 border rounded-xl placeholder-gray-400
                                {{ $errors->has('password_confirmation') ? 'border-red-400 error' : 'border-gray-200' }}"
                        >
                        <button type="button" id="toggle-password-confirm"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600">
                            <svg id="eye-show-confirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                            <svg id="eye-hide-confirm" class="w-4 h-4 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5m0 0c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5m0 0c4.756 0 8.773 3.162 10.065 7.498M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="animate-fade-up-6">
                    {{-- ✅ DIGANTI: bg-orange-600 --}}
                    <button type="submit" id="submit-btn"
                        class="btn-register w-full py-2.5 px-4 bg-orange-600 hover:bg-orange-700 active:scale-[0.98] text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-orange-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0zM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                        </svg>
                        <span id="btn-label">Daftar</span>
                    </button>
                </div>

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

            {{-- Login link --}}
            {{-- ✅ DIGANTI: text-orange-600 --}}
            <p class="text-center text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ url('/login') }}"
                   class="text-orange-600 hover:text-orange-700 font-semibold hover:underline transition-colors ml-1">
                    Login sekarang
                </a>
            </p>

        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-400 mt-5">
            &copy; {{ date('Y') }} SmartCanteen
        </p>
    </div>

    <script>
        // Toggle password
        const toggleBtn = document.getElementById('toggle-password');
        const pwInput   = document.getElementById('password');
        const eyeShow   = document.getElementById('eye-show');
        const eyeHide   = document.getElementById('eye-hide');

        toggleBtn.addEventListener('click', () => {
            const show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            eyeShow.classList.toggle('hidden', show);
            eyeHide.classList.toggle('hidden', !show);
        });

        // Toggle konfirmasi password
        document.getElementById('toggle-password-confirm').addEventListener('click', () => {
            const inp = document.getElementById('password_confirmation');
            const show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            document.getElementById('eye-show-confirm').classList.toggle('hidden', show);
            document.getElementById('eye-hide-confirm').classList.toggle('hidden', !show);
        });

        // ✅ DIGANTI: strength meter warna orange
        const strBars  = [1,2,3,4].map(i => document.getElementById('str-' + i));
        const strLabel = document.getElementById('str-label');
        const levels   = [
            { color: 'bg-red-400',    label: 'Sangat lemah', text: 'text-red-500' },
            { color: 'bg-orange-300', label: 'Lemah',        text: 'text-orange-400' },
            { color: 'bg-yellow-400', label: 'Cukup',        text: 'text-yellow-600' },
            { color: 'bg-orange-500', label: 'Kuat',         text: 'text-orange-600' },
        ];

        pwInput.addEventListener('input', () => {
            const v = pwInput.value;
            let score = 0;
            if (v.length >= 8)           score++;
            if (/[A-Z]/.test(v))         score++;
            if (/[0-9]/.test(v))         score++;
            if (/[^A-Za-z0-9]/.test(v))  score++;

            strBars.forEach((bar, i) => {
                bar.className = 'h-1 flex-1 rounded-full transition-all duration-300 ' +
                    (v.length === 0 ? 'bg-gray-200' : i < score ? levels[score - 1].color : 'bg-gray-200');
            });

            if (v.length === 0) {
                strLabel.textContent = '';
            } else {
                const lvl = levels[score - 1] || levels[0];
                strLabel.className = 'text-xs ' + lvl.text;
                strLabel.textContent = 'Kekuatan: ' + lvl.label;
            }
        });

        // Validasi client-side
        document.querySelector('form').addEventListener('submit', function (e) {
            let hasError = false;
            const fields = ['username', 'phone', 'password'];

            fields.forEach(name => {
                const input = this.elements[name];
                if (!input || !input.value.trim()) {
                    if (input) {
                        input.classList.add('error', 'border-red-400');
                        input.classList.remove('border-gray-200');
                    }
                    hasError = true;
                }
            });

            const hp = this.elements['phone'];
            if (hp && hp.value.trim() && !/^[0-9]{8,13}$/.test(hp.value.replace(/\s/g, ''))) {
                hp.classList.add('error', 'border-red-400');
                hp.classList.remove('border-gray-200');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return;
            }

            const btn   = document.getElementById('submit-btn');
            const label = document.getElementById('btn-label');
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            label.textContent = 'Mendaftarkan...';
        });

        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('input', function () {
                if (this.value.trim()) {
                    this.classList.remove('error', 'border-red-400');
                    this.classList.add('border-gray-200');
                }
            });
        });

        document.getElementById('phone').addEventListener('keypress', function (e) {
            if (!/[0-9]/.test(e.key)) e.preventDefault();
        });
    </script>
</body>
</html>