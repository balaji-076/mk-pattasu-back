<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedCustomerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $items;
    public $customer;

    public function __construct($order, $items, $customer)
    {
        $this->order = $order;
        $this->items = $items;
        $this->customer = $customer;
    }

    public function build()
    {
        return $this
            ->subject('Your order placed successfully')
            ->view('emails.order-placed-customer');
    }
}

