<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\ChartWidget;

class SiteVisitsChart extends ChartWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Заходы на сайт';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn (int $i) => today()->subDays($i));

        $counts = SiteVisit::query()
            ->whereBetween('date', [$days->first()->toDateString(), $days->last()->toDateString()])
            ->pluck('count', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Заходы',
                    'data' => $days->map(fn ($day) => (int) ($counts[$day->toDateString()] ?? 0))->all(),
                ],
            ],
            'labels' => $days->map(fn ($day) => $day->format('d.m'))->all(),
        ];
    }
}
