<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/** Public, read-only Indonesian address catalog for native customer apps. */
final class RegionsController extends BaseController
{
    public function provinces(): ResponseInterface
    {
        return $this->list('provinsi', ['id', 'label'], [], 100);
    }

    public function cities(): ResponseInterface
    {
        $provinceId = $this->positiveId($this->request->getGet('province_id'));
        if ($provinceId === null) return $this->error('invalid_parent', 'A province_id is required.', 400);
        return $this->list('kabupaten', ['id', 'label'], ['provinsi_id' => $provinceId], 100);
    }

    public function districts(): ResponseInterface
    {
        $provinceId = $this->positiveId($this->request->getGet('province_id'));
        $cityId = $this->positiveId($this->request->getGet('city_id'));
        if ($provinceId === null || $cityId === null) return $this->error('invalid_parent', 'Choose a province and city first.', 400);
        return $this->list('kecamatan', ['id', 'label'], ['provinsi_id' => $provinceId, 'kabupaten_id' => $cityId], 100);
    }

    public function villages(): ResponseInterface
    {
        $provinceId = $this->positiveId($this->request->getGet('province_id'));
        $cityId = $this->positiveId($this->request->getGet('city_id'));
        $districtId = $this->positiveId($this->request->getGet('district_id'));
        if ($provinceId === null || $cityId === null || $districtId === null) {
            return $this->error('invalid_parent', 'Choose a province, city, and district first.', 400);
        }
        return $this->list('kelurahan', ['id', 'label', 'kodepos'], [
            'provinsi_id' => $provinceId, 'kabupaten_id' => $cityId, 'kecamatan_id' => $districtId,
        ], 100);
    }

    private function list(string $table, array $fields, array $parents, int $defaultPerPage): ResponseInterface
    {
        $page = $this->positiveInt($this->request->getGet('page'), 1, 10000);
        $perPage = $this->positiveInt($this->request->getGet('per_page'), $defaultPerPage, 100);
        $query = $this->request->getGet('q');
        if ($query !== null && (!is_string($query) || mb_strlen($query) > 80)) {
            return $this->error('invalid_query', 'Search text must be at most 80 characters.', 400);
        }
        $builder = db_connect()->table($table)->select(implode(',', $fields));
        foreach ($parents as $field => $value) $builder->where($field, $value);
        if (is_string($query) && trim($query) !== '') $builder->like('label', trim($query));
        $total = (clone $builder)->countAllResults();
        $rows = $builder->orderBy('label', 'ASC')->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();
        $data = array_map(static function (array $row) use ($fields): array {
            $item = ['id' => (int) $row['id'], 'label' => (string) $row['label']];
            if (in_array('kodepos', $fields, true)) $item['postal_code'] = (string) ($row['kodepos'] ?? '');
            return $item;
        }, $rows);
        return $this->response->setStatusCode(200)->setHeader('Cache-Control', 'public, max-age=900, s-maxage=900')
            ->setHeader('X-Content-Type-Options', 'nosniff')->setJSON([
                'data' => $data,
                'meta' => ['api_version' => 'v1', 'page' => $page, 'per_page' => $perPage, 'total' => $total, 'total_pages' => (int) ceil($total / $perPage)],
            ]);
    }

    private function positiveId(mixed $value): ?int
    {
        if (!is_scalar($value) || !preg_match('/\A[1-9][0-9]{0,9}\z/', (string) $value)) return null;
        return (int) $value;
    }

    private function positiveInt(mixed $value, int $default, int $max): int
    {
        if (!is_scalar($value) || filter_var($value, FILTER_VALIDATE_INT) === false) return $default;
        $number = (int) $value;
        return $number > 0 ? min($max, $number) : $default;
    }

    private function error(string $code, string $message, int $status): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setHeader('Cache-Control', 'no-store')
            ->setHeader('X-Content-Type-Options', 'nosniff')->setJSON([
                'error' => ['code' => $code, 'message' => $message], 'meta' => ['api_version' => 'v1'],
            ]);
    }
}
