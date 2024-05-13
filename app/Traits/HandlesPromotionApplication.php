<?php

namespace App\Traits;

use App\Models\{Promotion, AdPromotion, OrderUpgrade, OrderPromotion};
use Carbon\Carbon;

trait HandlesPromotionApplication
{
    /**
     * Apply promotions to an ad and return redirection parameters.
     */
    protected function applyPromotionsWithRedirect($orderData)
    {
        $this->applyPromotions($orderData);

        return $this->preparePromotionRedirectionParameters($orderData);
    }

    /**
     * Apply promotions to an ad.
     */
    protected function applyPromotions($orderData)
    {
         // Decode the JSON string into an array
         $decodedData = json_decode($orderData->data, true);

         // Now you can access the data
         $promotionIds = $decodedData['promotionIds'];

         $userId = $decodedData['user_id'];

         $adId = $decodedData['ad_id'];

         $total = $decodedData['total'];

         $subtotal = $decodedData['subtotal'];

         $tax = $decodedData['tax'];

        $promotions = Promotion::whereIn('id', $promotionIds)->get();
        $orderUpgrade = OrderUpgrade::create([
            'total_value' => $total, // calculate total value
            'subtotal_value' => $subtotal, // calculate subtotal value
            'taxes_value' => $tax, // calculate taxes value
            'user_id' => $userId,
            'status' => 'completed',
            'payment_method' => 'stripe'
        ]);

        foreach ($promotions as $promotion) {
            $this->applyPromotion($promotion, $adId, $orderUpgrade);
        }
    }

    /**
     * Apply a single promotion and create associated records.
     *
     * @param Promotion $promotion
     * @param int $adId
     * @param OrderUpgrade $orderUpgrade
     */
    protected function applyPromotion(Promotion $promotion, $adId, $orderUpgrade)
    {
        $existingPromotion = AdPromotion::where('ad_id', $adId)
        ->where('promotion_id', $promotion->id)
        ->first();

        if (!$existingPromotion || $existingPromotion->end_date->isPast()) {
            $adPromotion = AdPromotion::create([
                'ad_id' => $adId,
                'promotion_id' => $promotion->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($promotion->duration),
                'price' => $promotion->price,
            ]);

            OrderPromotion::create([
                'order_upgrade_id' => $orderUpgrade->id,
                'ad_promotion_id' => $adPromotion->id,
            ]);

            // Update totals in OrderUpgrade
            $orderUpgrade->update([
                'total_value' => $orderUpgrade->total_value + $promotion->price,
                'subtotal_value' => $orderUpgrade->subtotal_value + $promotion->price,
            ]);
        }
    }

     /**
     * Prepare redirection parameters after applying promotions.
     */
    private function preparePromotionRedirectionParameters($orderData)
    {
        $decodedData = json_decode($orderData->data, true);
        $adId = $decodedData['ad_id'];

        $orderData->delete();

        return ['ad_id' => $adId];
    }
}
