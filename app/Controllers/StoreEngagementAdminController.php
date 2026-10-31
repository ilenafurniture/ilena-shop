<?php

namespace App\Controllers;

/** Moderation and case triage. Routes are protected by the global adminFilter. */
class StoreEngagementAdminController extends BaseController
{
    private function canManageCommerceCases(): bool
    {
        return (new \App\Services\AdminRbacService())->hasPermission((string) session()->get('email'), 'orders_online');
    }

    public function feedback()
    {
        if (! $this->canManageCommerceCases()) return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        $db = db_connect();
        if (! $db->tableExists('product_feedback')) return redirect()->to('/admin')->with('msg', 'Jalankan migrasi fitur commerce terlebih dahulu.');
        $rows = $db->table('product_feedback')->orderBy('created_at', 'DESC')->limit(200)->get()->getResultArray();
        return view('admin/commerceFeedback', ['rows' => $rows]);
    }

    public function moderate(int $id)
    {
        if (! $this->canManageCommerceCases()) return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        $db = db_connect();
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['approved', 'rejected'], true) || ! $db->tableExists('product_feedback')) return redirect()->back();
        $db->table('product_feedback')->where('id', $id)->update([
            'status' => $status,
            'moderated_by' => mb_substr((string) session()->get('email'), 0, 191),
            'moderated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->back()->with('msg', 'Status kiriman diperbarui.');
    }

    public function cases()
    {
        if (! $this->canManageCommerceCases()) return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        $db = db_connect();
        if (! $db->tableExists('customer_order_service_cases')) return redirect()->to('/admin')->with('msg', 'Jalankan migrasi fitur commerce terlebih dahulu.');
        return view('admin/commerceServiceCases', ['rows' => $db->table('customer_order_service_cases')->orderBy('created_at', 'DESC')->limit(200)->get()->getResultArray()]);
    }

    public function updateCase(int $id)
    {
        if (! $this->canManageCommerceCases()) return $this->response->setStatusCode(403)->setBody('Akses ditolak.');
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['reviewing', 'resolved', 'rejected'], true)) return redirect()->back();
        $db = db_connect();
        if ($db->tableExists('customer_order_service_cases')) {
            $db->table('customer_order_service_cases')->where('id', $id)->update([
                'status' => $status,
                'staff_note' => mb_substr(trim((string) $this->request->getPost('staff_note')), 0, 4000),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return redirect()->back()->with('msg', 'Status permintaan pelanggan diperbarui.');
    }
}
