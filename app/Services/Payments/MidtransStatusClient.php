<?php
namespace App\Services\Payments;

final class MidtransStatusClient
{
    public function __construct(private $client = null) {}
    public function fetch(string $orderId, string $environment, string $key): array
    {
        if ($key==='' || !in_array($environment,['sandbox','production'],true)) throw new \RuntimeException('Payment provider configuration missing');
        $client=$this->client ?? \Config\Services::curlrequest([],null,null,false);
        $base=$environment==='sandbox'?'https://api.sandbox.midtrans.com/v2/':'https://api.midtrans.com/v2/';
        $response=$client->get($base.rawurlencode($orderId).'/status',[
            'headers'=>['Accept'=>'application/json','Authorization'=>'Basic '.base64_encode($key.':')],
            'connect_timeout'=>3,'timeout'=>12,'verify'=>true,'http_errors'=>false,'allow_redirects'=>false,
        ]);
        $body=json_decode((string)$response->getBody(),true);
        if ($response->getStatusCode()!==200 || !is_array($body) || (string)($body['order_id']??'')!==$orderId || !isset($body['gross_amount'])) throw new \RuntimeException('Provider status could not be confirmed');
        return $body;
    }
}
