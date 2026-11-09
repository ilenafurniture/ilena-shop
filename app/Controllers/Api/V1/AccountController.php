<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use App\Services\Auth\CustomerApiTokenService;
use CodeIgniter\HTTP\ResponseInterface;

/** Customer-owned account resources exposed to the mobile app. */
final class AccountController extends BaseController
{
    public function addresses(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $email = strtolower((string) $auth['user']['email']);
        $row = $this->profileRow($email);
        if (!$row) return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
        $addresses = $this->decodeAddresses($row['alamat'] ?? null);
        if ($addresses === null) return $this->error('profile_data_invalid', 'Saved addresses need support review.', 500);

        return $this->json(['data' => array_map(fn (array $address, int $index): array => $this->presentAddress($address, $index), $addresses, array_keys($addresses))]);
    }

    public function createAddress(): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        $body = $this->body();
        if ($body instanceof ResponseInterface) return $body;
        if (array_key_exists('is_default', $body) && !is_bool($body['is_default'])) return $this->error('invalid_address', 'is_default must be true or false.', 422);
        $address = $this->validatedAddress($body, (string) $auth['user']['email']);
        if ($address instanceof ResponseInterface) return $address;

        $addresses = $this->lockAddresses((string) $auth['user']['email']);
        if ($addresses instanceof ResponseInterface) return $addresses;
        if (count($addresses) >= 50) return $this->rollbackError('address_limit', 'You can save up to 50 addresses.', 422);

        $address['utama'] = !empty($body['is_default']) || $addresses === [];
        if ($address['utama']) $addresses = $this->clearDefaults($addresses);
        $addresses[] = $address;
        return $this->persistAddresses((string) $auth['user']['email'], $addresses, count($addresses) - 1);
    }

    public function updateAddress(int $addressId): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        if ($addressId < 0 || $addressId >= 50) return $this->error('address_not_found', 'Address not found.', 404);
        $body = $this->body();
        if ($body instanceof ResponseInterface) return $body;
        if (array_key_exists('is_default', $body) && !is_bool($body['is_default'])) return $this->error('invalid_address', 'is_default must be true or false.', 422);
        $address = $this->validatedAddress($body, (string) $auth['user']['email']);
        if ($address instanceof ResponseInterface) return $address;

        $email = (string) $auth['user']['email'];
        $addresses = $this->lockAddresses($email);
        if ($addresses instanceof ResponseInterface) return $addresses;
        if (!array_key_exists($addressId, $addresses) || !is_array($addresses[$addressId])) return $this->rollbackError('address_not_found', 'Address not found.', 404);

        $wasDefault = !empty($addresses[$addressId]['utama']);
        $address['utama'] = array_key_exists('is_default', $body) ? (bool) $body['is_default'] : $wasDefault;
        $addresses[$addressId] = $address;
        $addresses = array_values($addresses);
        if ($address['utama']) $addresses = $this->clearDefaults($addresses, $addressId);
        elseif (!$this->hasDefault($addresses) && $addresses !== []) $addresses[0]['utama'] = true;
        return $this->persistAddresses($email, $addresses, $addressId);
    }

    public function deleteAddress(int $addressId): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        if ($addressId < 0 || $addressId >= 50) return $this->error('address_not_found', 'Address not found.', 404);
        $email = (string) $auth['user']['email'];
        $addresses = $this->lockAddresses($email);
        if ($addresses instanceof ResponseInterface) return $addresses;
        if (!array_key_exists($addressId, $addresses)) return $this->rollbackError('address_not_found', 'Address not found.', 404);

        $wasDefault = !empty($addresses[$addressId]['utama']);
        array_splice($addresses, $addressId, 1);
        if (($wasDefault || !$this->hasDefault($addresses)) && $addresses !== []) $addresses[0]['utama'] = true;
        return $this->persistAddresses($email, $addresses, null);
    }

    public function setDefaultAddress(int $addressId): ResponseInterface
    {
        $auth = $this->authenticated();
        if ($auth instanceof ResponseInterface) return $auth;
        if ($addressId < 0 || $addressId >= 50) return $this->error('address_not_found', 'Address not found.', 404);
        $email = (string) $auth['user']['email'];
        $addresses = $this->lockAddresses($email);
        if ($addresses instanceof ResponseInterface) return $addresses;
        if (!array_key_exists($addressId, $addresses)) return $this->rollbackError('address_not_found', 'Address not found.', 404);
        $addresses = $this->clearDefaults($addresses, $addressId);
        return $this->persistAddresses($email, $addresses, $addressId);
    }

    private function validatedAddress(array $body, string $email): array|ResponseInterface
    {
        $name = $this->stringField($body, 'recipient_name');
        $phone = preg_replace('/\D+/', '', $this->stringField($body, 'phone')) ?? '';
        $line = trim($this->stringField($body, 'address_line'));
        if ($name === '' || mb_strlen($name) > 100 || preg_match('/[<>\x00-\x1F\x7F]/u', $name)
            || !preg_match('/\A[0-9]{10,15}\z/D', $phone)
            || $line === '' || mb_strlen($line) > 250 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', $line)) {
            return $this->error('invalid_address', 'Check the recipient, phone number, and address fields.', 422);
        }

        $provinceId = $this->positiveId($body['province_id'] ?? null);
        $cityId = $this->positiveId($body['city_id'] ?? null);
        $districtId = $this->positiveId($body['district_id'] ?? null);
        $villageId = $this->positiveId($body['village_id'] ?? null);
        if (!$provinceId || !$cityId || !$districtId || !$villageId) return $this->error('invalid_address', 'Choose a complete delivery location.', 422);

        $db = db_connect();
        $province = $db->table('provinsi')->where('id', $provinceId)->get(1)->getRowArray();
        $city = $db->table('kabupaten')->where('id', $cityId)->where('provinsi_id', $provinceId)->get(1)->getRowArray();
        $district = $db->table('kecamatan')->where('id', $districtId)->where('provinsi_id', $provinceId)->where('kabupaten_id', $cityId)->get(1)->getRowArray();
        $village = $db->table('kelurahan')->where('id', $villageId)->where('provinsi_id', $provinceId)->where('kabupaten_id', $cityId)->where('kecamatan_id', $districtId)->get(1)->getRowArray();
        if (!$province || !$city || !$district || !$village) return $this->error('invalid_address', 'The selected delivery location does not match. Choose it again.', 422);

        $postalCode = trim((string) ($village['kodepos'] ?? ''));
        $full = implode(', ', array_filter([$line, (string) $village['label'], (string) $district['label'], (string) $city['label'], (string) $province['label'], $postalCode]));
        return [
            'email_pemesan' => strtolower(trim($email)),
            'nama_penerima' => $name,
            'nohp_penerima' => $phone,
            'prov_id' => (string) $provinceId,
            'prov' => (string) $province['label'],
            'kab_id' => (string) $cityId,
            'kab' => (string) $city['label'],
            'kec_id' => (string) $districtId,
            'kec' => (string) $district['label'],
            'desa_id' => (string) $villageId,
            'desa' => (string) $village['label'],
            'kodepos' => $postalCode,
            'alamat_tambahan' => $line,
            'alamat_lengkap' => $full,
        ];
    }

    private function lockAddresses(string $email): array|ResponseInterface
    {
        $db = db_connect();
        $db->transBegin();
        try {
            $row = $db->query('SELECT alamat FROM pembeli WHERE LOWER(TRIM(email)) = ? LIMIT 1 FOR UPDATE', [strtolower(trim($email))])->getRowArray();
        } catch (\Throwable) {
            $db->transRollback();
            return $this->error('service_unavailable', 'Addresses are temporarily unavailable.', 503);
        }
        if (!$row) {
            $db->transRollback();
            return $this->error('profile_not_found', 'Customer profile is unavailable.', 404);
        }
        $addresses = $this->decodeAddresses($row['alamat'] ?? null);
        if ($addresses === null) {
            $db->transRollback();
            return $this->error('profile_data_invalid', 'Saved addresses need support review.', 500);
        }
        return $addresses;
    }

    private function persistAddresses(string $email, array $addresses, ?int $resultIndex): ResponseInterface
    {
        $db = db_connect();
        $updated = $db->table('pembeli')->where('LOWER(TRIM(email))', strtolower(trim($email)))
            ->update(['alamat' => json_encode(array_values($addresses), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
        if (!$updated || !$db->transStatus()) {
            $db->transRollback();
            return $this->error('address_save_failed', 'The address was not saved. Please try again.', 500);
        }
        $db->transCommit();
        return $this->json(['data' => ['addresses' => array_map(fn (array $address, int $index): array => $this->presentAddress($address, $index), array_values($addresses), array_keys(array_values($addresses))), 'address_id' => $resultIndex]]);
    }

    private function decodeAddresses(mixed $value): ?array
    {
        if (!is_string($value) || trim($value) === '') return [];
        $addresses = json_decode($value, true);
        if (!is_array($addresses) || !array_is_list($addresses) || count($addresses) > 50) return null;
        foreach ($addresses as $address) if (!is_array($address)) return null;
        return $addresses;
    }

    private function presentAddress(array $address, int $index): array
    {
        return [
            'address_id' => $index,
            'recipient_name' => (string) ($address['nama_penerima'] ?? ''),
            'phone' => (string) ($address['nohp_penerima'] ?? ''),
            'province_id' => (string) ($address['prov_id'] ?? ''),
            'province' => (string) ($address['prov'] ?? ''),
            'city_id' => (string) ($address['kab_id'] ?? ''),
            'city' => (string) ($address['kab'] ?? ''),
            'district_id' => (string) ($address['kec_id'] ?? ''),
            'district' => (string) ($address['kec'] ?? ''),
            'village_id' => (string) ($address['desa_id'] ?? ''),
            'village' => (string) ($address['desa'] ?? ''),
            'postal_code' => (string) ($address['kodepos'] ?? ''),
            'address_line' => (string) ($address['alamat_tambahan'] ?? ''),
            'full_address' => (string) ($address['alamat_lengkap'] ?? ''),
            'is_default' => !empty($address['utama']),
        ];
    }

    private function clearDefaults(array $addresses, ?int $except = null): array
    {
        foreach ($addresses as $index => &$address) if (is_array($address)) $address['utama'] = $except !== null && $index === $except;
        unset($address);
        return $addresses;
    }

    private function hasDefault(array $addresses): bool
    {
        foreach ($addresses as $address) if (!empty($address['utama'])) return true;
        return false;
    }

    private function positiveId(mixed $value): ?int
    {
        if (!is_scalar($value) || !preg_match('/\A[1-9][0-9]{0,9}\z/', (string) $value)) return null;
        return (int) $value;
    }

    private function stringField(array $body, string $key): string
    {
        return is_string($body[$key] ?? null) ? trim($body[$key]) : '';
    }

    private function body(): array|ResponseInterface
    {
        try { $body = $this->request->getJSON(true); } catch (\Throwable) { $body = null; }
        return is_array($body) ? $body : $this->error('invalid_request', 'Send a valid JSON request.', 400);
    }

    private function authenticated(): array|ResponseInterface
    {
        $auth = (new CustomerApiTokenService())->authenticate($this->request->getHeaderLine('Authorization'));
        return $auth ?? $this->error('unauthorized', 'Sign in again to continue.', 401);
    }

    private function rollbackError(string $code, string $message, int $status): ResponseInterface
    {
        db_connect()->transRollback();
        return $this->error($code, $message, $status);
    }

    private function json(array $payload, int $status = 200): ResponseInterface
    {
        $payload['meta'] = ['api_version' => 'v1'];
        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')->setJSON($payload);
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->json(['error' => ['code' => $code, 'message' => $message]], $status);
    }
}
