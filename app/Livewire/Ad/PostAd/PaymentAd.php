<?php

namespace App\Livewire\Ad\PostAd;

use Filament\Forms\Components\Radio;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use App\Settings\StripeSettings;
use App\Settings\PaypalSettings;
use App\Settings\PaymentSettings;
use App\Models\Promotion;
use App\Settings\FlutterwaveSettings;

class PaymentAd extends Component implements HasForms
{
    use InteractsWithForms;

    public $currentPayment;

    public $payment_method;

    public $promotionIds;
    public $type = 'UG';

    public $promotions;
    public $subtotal = 0;
    public $tax = 0; // Define tax rate if applicable
    public $total = 0;

    public $isDifferentRate = false;
    public $convertedTotal = 0;
    public $defaultCurrency;

    protected $paymentGateways = [
        'stripe' => 'payment.stripe-payment',
        'paypal' => 'paypal-payment',
        'flutterwave' => 'flutterwave-payment',
    ];

    // Ad ID property
    public $id;

    /**
     * Mount the component and set the properties if an ad ID is provided.
     */
    public function mount($id)
    {
        $this->id = $id;
        $this->initializePaymentOptions();
        $this->updatePaymentData();
    }

    protected function updatePaymentData()
    {
        $this->promotions = Promotion::whereIn('id', $this->promotionIds)->get();
        $this->subtotal = $this->promotions->sum('price');

        // Accessing PaymentSettings
        $paymentSettings = app(PaymentSettings::class);

        if ($paymentSettings->enable_tax) {
            if ($paymentSettings->tax_type === 'percentage') {
                // Calculate tax as a percentage of the subtotal
                $this->tax = ($this->subtotal * $paymentSettings->tax_rate) / 100;
            } else if ($paymentSettings->tax_type === 'fixed') {
                // Apply a fixed tax rate
                $this->tax = $paymentSettings->tax_rate;
            }
        } else {
            // No tax applied
            $this->tax = 0;
        }

        // Add tax calculation logic here if necessary
        $this->total = $this->subtotal + $this->tax;


        $this->defaultCurrency =  $paymentSettings->currency;
        $paymentGatewayRate = $this->getPaymentGatewayRate();
        $systemExchangeRate = app(PaymentSettings::class)->exchange_rate;

        $this->isDifferentRate = $paymentGatewayRate != 1.0 && $paymentGatewayRate != $systemExchangeRate;
        $this->convertedTotal = $this->total * $paymentGatewayRate / $systemExchangeRate;
    }

    /**
     * Method to get payment data in the specified format.
     *
     * @return array
     */
    public function getPaymentDataProperty()
    {
        return [
            'user_id' => auth()->id(),
            'ad_id' => $this->id,
            'promotionIds' => $this->promotionIds,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total
        ];
    }

    private function getPaymentGatewayRate()
    {
        return match($this->payment_method) {
            'stripe' => app(StripeSettings::class)->exchange_rate,
            'paypal' => app(PaypalSettings::class)->exchange_rate,
            'flutterwave' => app(FlutterwaveSettings::class)->exchange_rate,
            default => 1.0
        };
    }

    protected function initializePaymentOptions()
    {
        $paymentOptions = [];

        if (app(StripeSettings::class)->status) {
            $paymentOptions['stripe'] = 'Stripe';
        }

        if (app('filament')->hasPlugin('paypal') && app(PaypalSettings::class)->status) {
            $paymentOptions['paypal'] = 'PayPal';
        }

        if (app('filament')->hasPlugin('flutterwave') && app(FlutterwaveSettings::class)->status) {
            $paymentOptions['flutterwave'] = 'Flutterwave';
        }

        // Set default payment method if only one option is enabled
        if (count($paymentOptions) === 1) {
            $defaultMethod = array_key_first($paymentOptions);
            $this->payment_method = $defaultMethod;
            $this->currentPayment = $this->paymentGateways[$defaultMethod];
        }

        return $paymentOptions;
    }

    /**
     * Define the form for the website URL input.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        $paymentOptions = $this->initializePaymentOptions();

        return $form
            ->schema([
                Radio::make('payment_method')
                ->hiddenLabel()
                ->live()
                ->options($paymentOptions)
                ->afterStateUpdated(function ($state) {
                    $this->currentPayment = $this->paymentGateways[$state] ?? null;
                    $this->updatePaymentData();
                }),
            ]);
    }


    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.ad.post-ad.payment-ad');
    }
}
