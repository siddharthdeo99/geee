<?php

namespace App\Livewire\Payment;

use App\Models\WebhookPackage;
use Livewire\Component;
use App\Models\WebhookUpgrade;
use Stripe\StripeClient;
use App\Settings\StripeSettings;

class StripePayment extends Component
{
    public $id;

    public $total;

    public $tax;

    public $type;

    public $data;

    public $subtotal;

    public $public_key;

    public $payment_gateway_params = [];

     /**
     * Mount the component and set the properties if an ad ID is provided.
     */
    public function mount($id)
    {
        $this->id = $id;
        $this->public_key = app(StripeSettings::class)->public_key;
        $this->processPayment();
    }

    public function processPayment()
    {
        $total = $this->total;

        // Initialize Stripe
        $stripe = new StripeClient(app(StripeSettings::class)->secret_key); // Replace with your Stripe secret key

        // Create a payment intent
        $intent = $stripe->paymentIntents->create([
            'amount' => $total * 100, // total in cents
            'currency' => app(StripeSettings::class)->currency, // Replace with your desired currency
            'payment_method_types' => ['card']
        ]);


        $this->payment_gateway_params['client_secret'] = $intent->client_secret;

        $this->dispatch('post-created');
        $this->handleWebhookUpgrade($intent);

    }

    protected function handleWebhookUpgrade($intent)
    {
        try {
            if($this->type == 'PKG') {
                WebhookPackage::create([
                    'data' => json_encode($this->data),
                    'payment_id' => $intent->id,
                    'payment_method' => 'stripe',
                    'status' => 'pending'
                ]);
            } else {
                WebhookUpgrade::create([
                    'data' => json_encode($this->data),
                    'payment_id' => $intent->id,
                    'payment_method' => 'stripe',
                    'status' => 'pending'
                ]);
            }
        } catch (\Throwable $th) {
            // Handle any exceptions
        }
    }

    public function render()
    {
        return view('livewire.payment.stripe-payment');
    }
}
