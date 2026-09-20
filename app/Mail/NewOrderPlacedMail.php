<?php

namespace App\Mail;

use App\Models\Orders;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlacedMail extends Mailable implements ShouldQueue
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
        return $this->subject('New Order Received - ' ."(". $this->order->order_number.")")
            ->view('emails.new-order');
    }
}
