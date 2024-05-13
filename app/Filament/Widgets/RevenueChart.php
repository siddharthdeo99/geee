<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaction;
use App\Models\AdPromotion;
use App\Models\Promotion;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue by Ad Upgrade -  Last 12 Months';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $labels = [];
        $datasets = [];
        $promotionTypes = Promotion::select('name')->distinct()->get()->pluck('name')->toArray();

        // Create labels (for example, last 12 months)
        for ($i = 11; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subMonths($i)->format('M Y');
        }

        foreach ($promotionTypes as $type) {
            $data = [];

            foreach ($labels as $label) {
                $date = Carbon::createFromFormat('M Y', $label);
                $total = AdPromotion::whereHas('promotion', function ($query) use ($type) {
                    $query->where('name', $type);
                })
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('price');

                $data[] = $total;
            }

            $datasets[] = [
                'label' => $type,
                'data' => $data,
                'fill' => 'start',
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
}
