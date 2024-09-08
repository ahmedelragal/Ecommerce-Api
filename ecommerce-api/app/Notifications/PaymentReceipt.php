<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceipt extends Notification
{
    use Queueable;

    protected $order;
    protected $paymentIntent;

    public function __construct($order, $paymentIntent)
    {
        $this->order = $order;
        $this->paymentIntent = $paymentIntent;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Receipt')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your payment for order #' . $this->order->id . ' was successful.')
            ->line('Payment Amount: $' . number_format($this->order->total_price, 2))
            ->line('Payment ID: ' . $this->paymentIntent->id)
            ->line('Thank you for your purchase!');
    }
}
