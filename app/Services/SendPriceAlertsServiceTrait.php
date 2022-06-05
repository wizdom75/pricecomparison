<?php

namespace App\Services;

use App\Alert;
use App\Price;
use Illuminate\Support\Facades\Mail;

trait SendPriceAlertsServiceTrait
{
    public function sendPriceAlerts()
    {
        foreach (Alert::all() as $alert) {
            if (!$this->compareProductPrice($alert->product_id, $alert->target_price)) {
                continue;
            }
            $poroductTitle  = $alert->product->title;
            $productPrice   = $alert->product->min_price;
            $productUrl     = env('APP_URL').'/compare/'.$alert->product->slug.'/prices';
            echo "Price alert for $poroductTitle sent to $alert->email target price is £$alert->target_price\n";

            $arrayEmails = [$alert->email];
            $emailSubject = 'Price drop alert';
            $emailBody = "Product ($poroductTitle) price is now £$productPrice.";

            $this->imagePath = env('APP_URL').'/'.$alert->product->images->path;
            Mail::send('emails.alerts',
                ['msg' => $emailBody, 'product_image' => $this->imagePath, 'url' => $productUrl],
                function($message) use ($arrayEmails, $emailSubject) {
                    $message->to($arrayEmails)
                    ->subject($emailSubject);
                    // ->embed(public_path() .'/'.$this->imagePath);
                }
            );
        }
    }

    protected function compareProductPrice(int $product_id, float $target_price) : bool
    {
        if (!$price = Price::where('product_id', $product_id)->orderBy('amount', 'ASC')->first()) {
            return false;
        }

        if ($price->amount <= $target_price) {
            return true;
        }

        return false;
    }
}
