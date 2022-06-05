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
            echo "Price alert for $poroductTitle sent to $alert->email target price is £$alert->target_price\n";

            $arrayEmails = [$alert->email];
            $emailSubject = 'Price drop alert';
            $emailBody = "Hello,<br> Product ($poroductTitle) price is now £productPrice.";

            Mail::send('emails.price_alerts',
                ['msg' => $emailBody],
                function($message) use ($arrayEmails, $emailSubject) {
                    $message->to($arrayEmails)
                    ->subject($emailSubject);
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
