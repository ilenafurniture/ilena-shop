<?php
namespace App\Services;

/** Session + actor-scoped cart; legacy shared table is never read or truncated. */
final class SessionMarketCart
{
    private function key(): string { return 'market_cart_'.hash('sha256',strtolower((string)session()->get('email'))); }
    public function items(): array { return (array)session()->get($this->key()); }
    public function add(string $id, string $variant): void
    {
        $items=$this->items(); $key=hash('sha256',$id."\0".mb_strtolower($variant));
        $items[$key] ??= ['id'=>$key,'id_barang'=>$id,'varian'=>$variant,'jumlah'=>0];
        $items[$key]['jumlah']=min(10000,(int)$items[$key]['jumlah']+1);
        session()->set($this->key(),$items);
    }
    public function reduce(string $key): void
    {
        $items=$this->items();
        if (isset($items[$key]) && --$items[$key]['jumlah']<=0) unset($items[$key]);
        session()->set($this->key(),$items);
    }
    public function clear(): void { session()->remove($this->key()); }
}
