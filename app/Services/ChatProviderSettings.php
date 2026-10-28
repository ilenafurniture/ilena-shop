<?php

namespace App\Services;

/** Stores an OpenAI-compatible provider profile for Dev-managed chat. */
final class ChatProviderSettings
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function tableReady(): bool
    {
        return $this->db->tableExists('ilena_chat_provider_settings');
    }

    public function encryptionReady(): bool
    {
        return strlen(trim((string) env('ILENA_CHAT_CREDENTIAL_KEY', ''))) >= 32;
    }

    /** Never return the decrypted key to a view or API response. */
    public function adminSummary(): array
    {
        $row = $this->getRow();
        if (! $row) {
            $envEndpoint = trim((string) env('ILENA_CHAT_AI_ENDPOINT', ''));
            $envModel = trim((string) env('ILENA_CHAT_AI_MODEL', ''));
            $envKey = trim((string) env('ILENA_CHAT_AI_API_KEY', ''));
            return [
                'enabled' => $envEndpoint !== '' && $envModel !== '' && $envKey !== '',
                'endpoint' => $envEndpoint,
                'model' => $envModel,
                'hasKey' => $envKey !== '',
                'source' => 'env',
            ];
        }

        return [
            'enabled' => (int) ($row['is_enabled'] ?? 0) === 1,
            'endpoint' => (string) ($row['endpoint'] ?? ''),
            'model' => (string) ($row['model'] ?? ''),
            'hasKey' => trim((string) ($row['api_key_encrypted'] ?? '')) !== '',
            'source' => 'admin',
        ];
    }

    /** Runtime credentials are used only by the background worker. */
    public function forWorker(): array
    {
        $row = $this->getRow();
        if ($row) {
            if ((int) ($row['is_enabled'] ?? 0) !== 1 || trim((string) ($row['api_key_encrypted'] ?? '')) === '') {
                return ['endpoint' => '', 'model' => '', 'apiKey' => ''];
            }

            try {
                $ciphertext = base64_decode((string) $row['api_key_encrypted'], true);
                if (! is_string($ciphertext) || $ciphertext === '') throw new \RuntimeException('Encrypted provider key is malformed.');
                $apiKey = $this->encrypter()->decrypt($ciphertext);
                return [
                    'endpoint' => (string) $row['endpoint'],
                    'model' => (string) $row['model'],
                    'apiKey' => is_string($apiKey) ? $apiKey : '',
                ];
            } catch (\Throwable $e) {
                log_message('error', 'Ilena chat provider key could not be decrypted.');
                return ['endpoint' => '', 'model' => '', 'apiKey' => ''];
            }
        }

        // Preserve the existing environment-based setup until a Dev saves a profile.
        return [
            'endpoint' => trim((string) env('ILENA_CHAT_AI_ENDPOINT', '')),
            'model' => trim((string) env('ILENA_CHAT_AI_MODEL', '')),
            'apiKey' => trim((string) env('ILENA_CHAT_AI_API_KEY', '')),
        ];
    }

    /** Save and encrypt a provider secret without ever returning it. */
    public function save(string $endpoint, string $model, string $apiKey, bool $enabled, bool $clearKey, string $updatedBy): array
    {
        if (! $this->tableReady()) return ['ok' => false, 'message' => 'Migrasi pengaturan provider belum dijalankan.'];

        $row = $this->getRow();
        $apiKey = trim($apiKey);
        $existingCiphertext = (string) ($row['api_key_encrypted'] ?? '');
        if ($clearKey) {
            $existingCiphertext = '';
        } elseif ($apiKey !== '') {
            try {
                $existingCiphertext = base64_encode($this->encrypter()->encrypt($apiKey));
            } catch (\Throwable $e) {
                log_message('error', 'Ilena chat provider key encryption failed: {message}', ['message' => $e->getMessage()]);
                return ['ok' => false, 'message' => 'Kunci enkripsi chat belum siap. Tambahkan ILENA_CHAT_CREDENTIAL_KEY di .env server, lalu coba lagi.'];
            }
        } elseif (! $row && trim((string) env('ILENA_CHAT_AI_API_KEY', '')) !== '') {
            // Migrate a legacy environment key into encrypted admin-managed storage on first save.
            try {
                $existingCiphertext = base64_encode($this->encrypter()->encrypt(trim((string) env('ILENA_CHAT_AI_API_KEY', ''))));
            } catch (\Throwable $e) {
                log_message('error', 'Ilena legacy provider key encryption failed: {message}', ['message' => $e->getMessage()]);
                return ['ok' => false, 'message' => 'Kunci enkripsi chat belum siap. Tambahkan ILENA_CHAT_CREDENTIAL_KEY di .env server, lalu coba lagi.'];
            }
        }

        if ($enabled && $existingCiphertext === '') {
            return ['ok' => false, 'message' => 'Isi API key sebelum mengaktifkan provider.'];
        }

        $data = [
            'endpoint' => $endpoint,
            'model' => $model,
            'api_key_encrypted' => $existingCiphertext ?: null,
            'is_enabled' => $enabled ? 1 : 0,
            'updated_by' => mb_substr($updatedBy, 0, 191),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($row) {
            $saved = $this->db->table('ilena_chat_provider_settings')->where('id', 1)->update($data);
        } else {
            $data['id'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $saved = $this->db->table('ilena_chat_provider_settings')->insert($data);
        }

        return $saved
            ? ['ok' => true, 'message' => $enabled ? 'Provider AI tersimpan dan aktif.' : 'Provider AI tersimpan dalam keadaan nonaktif.']
            : ['ok' => false, 'message' => 'Pengaturan provider gagal disimpan.'];
    }

    private function getRow(): ?array
    {
        if (! $this->tableReady()) return null;
        return $this->db->table('ilena_chat_provider_settings')->where('id', 1)->get(1)->getRowArray() ?: null;
    }

    private function encrypter()
    {
        $key = trim((string) env('ILENA_CHAT_CREDENTIAL_KEY', ''));
        if (strlen($key) < 32) throw new \RuntimeException('ILENA_CHAT_CREDENTIAL_KEY must contain at least 32 characters.');
        $config = clone config('Encryption');
        $config->key = $key;
        return service('encrypter', $config, false);
    }
}
