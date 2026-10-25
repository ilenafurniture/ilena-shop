<?php
namespace App\Services;

/** Browser-session receipts for safe retries of operator forms (CI sessions are locked). */
final class SessionOperation
{
    private static function key(string $scope): string { return 'operation_'.hash('sha256',$scope."\0".(string)session()->get('email')); }
    public static function issue(string $scope): string
    {
        $key=self::key($scope); $operations=(array)session()->get($key); $token=bin2hex(random_bytes(32));
        $operations[$token]=['issued_at'=>time(),'result'=>null]; session()->set($key,array_slice($operations,-50,null,true)); return $token;
    }
    public static function lookup(string $scope, $token): ?array
    {
        $operations=(array)session()->get(self::key($scope));
        if (!is_string($token)||preg_match('/^[a-f0-9]{64}$/D',$token)!==1||!isset($operations[$token])||time()-(int)$operations[$token]['issued_at']>7200) throw new \RuntimeException('Form kedaluwarsa. Muat ulang form sebelum mengirim pesanan.');
        return $operations[$token]['result'];
    }
    public static function complete(string $scope,string $token,array $result): void
    {
        self::lookup($scope,$token); $key=self::key($scope); $operations=(array)session()->get($key);
        $operations[$token]['result']=$result; session()->set($key,$operations);
    }
}
