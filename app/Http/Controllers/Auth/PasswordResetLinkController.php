<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

   public function store(Request $request)
{
    $request->validate(['phone' => ['required', 'string']]);

    // Bersihkan nomor input (hapus + dan spasi)
    $inputPhone = preg_replace('/[^0-9]/', '', $request->phone);

    // Gunakan query yang lebih fleksibel jika nomor di DB mungkin tidak identik (misal ada spasi tersembunyi)
    $user = User::where('phone', 'LIKE', '%' . $inputPhone . '%')->first();

    if (!$user) {
        return back()->withErrors(['phone' => 'Nomor HP tidak terdaftar.']);
    }

    if (!$user->telegram_chat_id) {
        return back()->withErrors(['phone' => 'Akun ditemukan, tapi belum terhubung ke Bot Telegram.']);
    }

    // 1. Generate OTP 6 Digit
    $otp = rand(100000, 999999);

    // 2. Simpan ke tabel password_reset_tokens 
    DB::table('password_reset_tokens')->updateOrInsert(
    ['phone' => $user->phone], // Cari berdasarkan nomor HP
    [
        'token' => bcrypt($otp),
        'created_at' => now()
    ]
    );

    session([
        'otp_secret' => $otp,        // plain OTP untuk dicek dengan !=
        'otp_phone'  => $user->phone, // pakai phone dari DB, bukan input
        'otp_created_at' => now()->timestamp,
    ]);

    // 3. Kirim ke Telegram
    try {
        Telegram::sendMessage([
            'chat_id' => $user->telegram_chat_id,
            'text' => "Halo *{$user->name}*, kode reset password kamu adalah: *{$otp}*\n\nKode berlaku selama 60 detik.",
            'parse_mode' => 'Markdown'
        ]);
    } catch (\Exception $e) {
        return back()->withErrors(['phone' => 'Gagal mengirim pesan ke Telegram.']);
    }

    return redirect()->route('password.otp')
                 ->with('status', 'Kode OTP telah dikirim ke Telegram kamu!');
}

    public function handleTelegram(Request $request)
    {
        $message = $request->input('message');
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        if (str_contains($text, '/start')) {
            // Ambil nomor HP dan bersihkan dari karakter non-angka
            $phoneRaw = trim(str_replace('/start', '', $text));
            $phone = preg_replace('/[^0-9]/', '', $phoneRaw);
            
            $user = User::where('phone', $phone)->first();

            if ($user) {
                $user->update(['telegram_chat_id' => $chatId]);
                $pesan = "✅ Berhasil! ID {$chatId} tersambung dengan akun {$user->name}. Sekarang kamu bisa reset password via bot ini.";
            } else {
                $pesan = "❌ Gagal. Nomor [{$phone}] tidak terdaftar di sistem Kantin Cerdas.";
            }

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $pesan
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function resend(Request $request)
    {
        $phone = session('otp_phone');

        if (!$phone) {
            return redirect()->route('password.request')
                            ->withErrors(['phone' => 'Sesi habis, silakan masukkan nomor HP kembali.']);
        }

        return $this->store(new Request(['phone' => $phone]));
    }
}