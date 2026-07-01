<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru — Smart Canteen</title>
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
        .input-field.match {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.10);
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
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Password Baru</h1>
                        <p class="text-gray-500 text-sm mt-0.5 leading-relaxed">
                            OTP terverifikasi. Buat password baru kamu.
                        </p>
                    </div>
                </div>

                <!-- Step indicator — step 3 aktif, step 1 & 2 done -->
                <div class="flex items-center gap-2">
                    <!-- Step 1 done -->
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-orange-100 text-orange-500 border-2 border-orange-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400">Nomor HP</span>
                    </div>
                    <div class="flex-1 h-px bg-orange-300"></div>
                    <!-- Step 2 done -->
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-orange-100 text-orange-500 border-2 border-orange-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-400">Verifikasi OTP</span>
                    </div>
                    <div class="flex-1 h-px bg-orange-300"></div>
                    <!-- Step 3 aktif -->
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-orange-500 text-white">3</div>
                        <span class="text-xs font-medium text-orange-600">Password Baru</span>
                    </div>
                </div>
            </div>

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
            <form method="POST" action="{{ route('password.reset') }}" class="space-y-5">
                @csrf

                <!-- Password Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                            </svg>
                        </span>
                        <input id="password" type="password" name="password" required
                            class="input-field w-full pl-10 pr-11 py-2.5 text-sm bg-gray-50 border rounded-xl placeholder-gray-400
                                {{ $errors->has('password') ? 'border-red-400 error' : 'border-gray-200' }}"
                            placeholder="Minimal 8 karakter">
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Strength meter -->
                    <div class="mt-2 flex gap-1" id="strength-meter">
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-200 transition-all duration-300"></div>
                    </div>
                    <p id="str-label" class="text-xs mt-1"></p>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                            </svg>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="input-field w-full pl-10 pr-11 py-2.5 text-sm bg-gray-50 border rounded-xl placeholder-gray-400
                                {{ $errors->has('password_confirmation') ? 'border-red-400 error' : 'border-gray-200' }}"
                            placeholder="Ulangi password baru">
                        <button type="button" id="toggle-confirm-password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Match indicator -->
                    <p id="match-label" class="text-xs mt-1.5 hidden"></p>
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="btn-primary w-full py-2.5 px-4 bg-orange-600 hover:bg-orange-700 active:scale-[0.98] text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-orange-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
                    </svg>
                    Simpan Password Baru
                </button>
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
        // Toggle password
        const pwInput = document.getElementById('password');
        document.getElementById('toggle-password').addEventListener('click', () => {
            pwInput.type = pwInput.type === 'password' ? 'text' : 'password';
        });

        // Toggle konfirmasi
        const confirmInput = document.getElementById('password_confirmation');
        document.getElementById('toggle-confirm-password').addEventListener('click', () => {
            confirmInput.type = confirmInput.type === 'password' ? 'text' : 'password';
        });

        // Strength meter
        const bars   = document.querySelectorAll('#strength-meter div');
        const strLabel = document.getElementById('str-label');
        const levels = [
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

            bars.forEach((b, i) => {
                b.className = 'h-1 flex-1 rounded-full transition-all duration-300 ' +
                    (v.length === 0 ? 'bg-gray-200' : i < score ? levels[score - 1].color : 'bg-gray-200');
            });

            if (v.length === 0) {
                strLabel.textContent = '';
            } else {
                const lvl = levels[score - 1] || levels[0];
                strLabel.className = 'text-xs mt-1 ' + lvl.text;
                strLabel.textContent = 'Kekuatan: ' + lvl.label;
            }

            checkMatch();
        });

        // Password match indicator
        const matchLabel = document.getElementById('match-label');

        function checkMatch() {
            const pw = pwInput.value;
            const cf = confirmInput.value;
            if (!cf) {
                matchLabel.classList.add('hidden');
                confirmInput.classList.remove('match', 'error', 'border-green-400', 'border-red-400');
                confirmInput.classList.add('border-gray-200');
                return;
            }
            matchLabel.classList.remove('hidden');
            if (pw === cf) {
                matchLabel.textContent = '✓ Password cocok';
                matchLabel.className = 'text-xs mt-1.5 text-green-600';
                confirmInput.classList.remove('error', 'border-red-400');
                confirmInput.classList.add('match', 'border-green-400');
            } else {
                matchLabel.textContent = '✗ Password tidak cocok';
                matchLabel.className = 'text-xs mt-1.5 text-red-500';
                confirmInput.classList.remove('match', 'border-green-400');
                confirmInput.classList.add('error', 'border-red-400');
            }
        }

        confirmInput.addEventListener('input', checkMatch);

        // Clear error on input
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('input', function () {
                if (this.value.trim()) {
                    this.classList.remove('error', 'border-red-400');
                    if (this.id !== 'password_confirmation') {
                        this.classList.add('border-gray-200');
                    }
                }
            });
        });
    </script>
</body>
</html>