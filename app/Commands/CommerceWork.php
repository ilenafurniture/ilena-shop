<?php

namespace App\Commands;

use App\Services\CommerceJobQueue;
use App\Services\MidtransCheckoutJobHandler;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CommerceWork extends BaseCommand
{
    protected $group = 'Ilena Commerce';
    protected $name = 'commerce:work';
    protected $description = 'Process durable commerce jobs outside customer web requests.';
    protected $usage = 'commerce:work [--once]';

    public function run(array $params)
    {
        $once = in_array('--once', $params, true);
        $queue = new CommerceJobQueue();
        do {
            try {
                $job = $queue->claim();
                if (!$job) {
                    if ($once) break;
                    usleep(500000);
                    continue;
                }
                if (($job['job_type'] ?? '') === 'order.payment_notification') {
                    $payload = json_decode((string) ($job['payload'] ?? ''), true) ?: [];
                    $order = db_connect()->table('pemesanan')->where('id_midtrans', (string) $job['order_id'])->get()->getRowArray();
                    $sent = $order && (new \App\Services\OrderPaymentNotificationService())->send(
                        $order, (string) ($payload['status'] ?? ''), !empty($payload['include_admin'])
                    );
                    if ($sent) $queue->complete((int) $job['id']);
                    else $queue->retryOrPark($job, 'Payment notification email failed; retry is scheduled.', true);
                    CLI::write('Commerce notification job ' . (int) $job['id'] . ': ' . ($sent ? 'completed' : 'retry scheduled'));
                } elseif (($job['job_type'] ?? '') === 'midtrans.charge') {
                    $result = (new MidtransCheckoutJobHandler())->handle($job);
                    if ($result['done']) $queue->complete((int) $job['id']);
                    else $queue->retryOrPark($job, (string) $result['message'], (bool) $result['retrySafe']);
                    CLI::write('Commerce job ' . (int) $job['id'] . ': ' . ($result['done'] ? 'completed' : ($result['retrySafe'] ? 'retry scheduled' : 'parked for reconciliation')));
                } else {
                    $queue->retryOrPark($job, 'Unsupported commerce job type.', false);
                }
            } catch (\Throwable $e) {
                log_message('error', 'Commerce worker error: {message}', ['message' => $e->getMessage()]);
                if ($once) break;
                usleep(1000000);
            }
            if ($once) break;
        } while (true);
    }
}
