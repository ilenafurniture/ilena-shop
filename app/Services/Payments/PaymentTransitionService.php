<?php
namespace App\Services\Payments;

/** Apply only provider-authenticated responses or validated notifications. */
final class PaymentTransitionService
{
    public function __construct(private $db) {}

    public function apply(array $body, string $environment, callable $paidEffects): array
    {
        $this->db->transBegin();
        try {
            $order=PaymentSafety::lockedOrder($this->db,(string)($body['order_id']??''));
            if (!$order) throw new \RuntimeException('Order not found');
            $mid=json_decode($order['data_mid']??'{}',true)?:[];
            if (!isset($body['gross_amount']) || !is_numeric($body['gross_amount'])
                || abs((float)$body['gross_amount']-(float)($mid['gross_amount']??-1))>0.001
                || (!empty($mid['transaction_id']) && !empty($body['transaction_id']) && $mid['transaction_id']!==$body['transaction_id'])) throw new \RuntimeException('Provider response does not match order');
            $new=PaymentSafety::notificationStatus($body); $old=(string)$order['status'];
            $transition=PaymentSafety::canTransition($old,$new);
            $recover=PaymentSafety::needsFirstPaidEffects($order,$new);
            if (!$transition && !$recover && $new!==$old) {
                $this->db->transRollback(); return ['order'=>$order,'applied'=>false];
            }
            if ($new===null) { $this->db->transRollback(); return ['order'=>$order,'applied'=>false]; }
            // Provider payloads must never overwrite application tokens or effect markers.
            foreach ($body as $key=>$value) if (!str_starts_with((string)$key,'_app_')) $mid[$key]=$value;
            $mid['_app_payment_environment']=$environment;
            $order['status']=$transition?$new:$old; $order['data_mid']=json_encode($mid);
            if (($transition||$recover) && $new==='Proses') {
                if ($environment!=='sandbox') $paidEffects($order);
                $mid['_app_payment_effects_applied']=true;
            }
            if (in_array($new,['Kadaluarsa','Ditolak','Gagal','Dibatalkan'],true)) (new StockReservationService($this->db))->release((string)$order['id_midtrans']);
            if (!$this->db->table('pemesanan')->where('id_midtrans',$order['id_midtrans'])->update(['status'=>$order['status'],'data_mid'=>json_encode($mid)]) || !$this->db->transStatus()) throw new \RuntimeException('Payment effects failed');
            $this->db->transCommit(); $order['data_mid']=json_encode($mid);
            return ['order'=>$order,'applied'=>$transition||$recover];
        } catch (\Throwable $e) { $this->db->transRollback(); throw $e; }
    }
}
