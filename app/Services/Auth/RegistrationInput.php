<?php

namespace App\Services\Auth;

use App\Services\SpamAccountCleanupService;

/** The server owns validation; browser attributes are only immediate feedback. */
final class RegistrationInput
{
    public static function normalize(array $post): array
    {
        $data = [];
        foreach (['nama', 'email', 'nohp', 'sandi', 'validasi-syarat'] as $field) {
            $data[$field] = is_string($post[$field] ?? null) ? $post[$field] : '';
        }
        $data['nama'] = trim($data['nama']);
        $data['email'] = strtolower(trim($data['email']));
        $data['nohp'] = trim($data['nohp']);
        return $data;
    }

    public function errors(array $data, ?callable $isDisposable = null): array
    {
        $validation = \Config\Services::validation(null, false);
        $validation->setRules([
            'nama' => [
                'rules' => 'required|max_length[100]|regex_match[/^[^\x00-\x1F\x7F<>]+$/uD]',
                'errors' => ['required' => 'Nama lengkap harus diisi.', 'max_length' => 'Nama lengkap maksimal 100 karakter.', 'regex_match' => 'Nama lengkap mengandung karakter yang tidak valid.'],
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[191]|regex_match[/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/D]',
                'errors' => ['required' => 'Email harus diisi.', 'valid_email' => 'Format email tidak valid.', 'max_length' => 'Email maksimal 191 karakter.', 'regex_match' => 'Format email tidak valid.'],
            ],
            'nohp' => [
                'rules' => 'required|regex_match[/^[0-9]{10,15}$/D]',
                'errors' => ['required' => 'Nomor HP harus diisi.', 'regex_match' => 'Nomor HP harus berupa 10–15 digit angka, tanpa spasi atau tanda baca.'],
            ],
            'sandi' => [
                'rules' => 'required|min_length[12]|max_length[72]|regex_match[/^[^\x00]+$/D]',
                'errors' => ['required' => 'Kata sandi harus diisi.', 'min_length' => 'Kata sandi minimal 12 karakter.', 'max_length' => 'Kata sandi maksimal 72 byte.', 'regex_match' => 'Kata sandi mengandung karakter yang tidak valid.'],
            ],
            'validasi-syarat' => [
                'rules' => 'required|in_list[1]',
                'errors' => ['required' => 'Setujui syarat dan ketentuan terlebih dahulu.', 'in_list' => 'Persetujuan syarat dan ketentuan tidak valid.'],
            ],
        ]);
        $validation->run($data);
        $errors = $validation->getErrors();
        // PASSWORD_DEFAULT currently uses bcrypt: do not silently truncate UTF-8 passwords.
        if (strlen($data['sandi']) > 72) $errors['sandi'] = 'Kata sandi maksimal 72 byte; kurangi panjangnya jika memakai simbol atau emoji.';
        if (!isset($errors['email'])) {
            $isDisposable ??= static fn(string $email): bool => (new SpamAccountCleanupService())->isDisposableEmail($email);
            if ($isDisposable($data['email'])) $errors['email'] = 'Email sementara/tempmail tidak dapat digunakan. Gunakan email pribadi yang aktif.';
        }
        return $errors;
    }
}
