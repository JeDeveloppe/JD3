<?php

namespace App\Service;

use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class ChartService
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder
        )
    {}

    public function generateRadarChart(array $technologiesFamilies): Chart
    {
        $familyData = [];

        foreach ($technologiesFamilies as $technologyFamily) {
            $usedTechnologies = array_filter(
                $technologyFamily->getTechnologies()->toArray(),
                fn ($technology) => $technology->isUsed()
            );

            if (0 === count($usedTechnologies)) {
                continue;
            }

            $total = 0;
            foreach ($usedTechnologies as $technology) {
                $total += $technology->getKnowledgeRate();
            }

            $familyData[] = [
                'name' => $technologyFamily->getName(),
                'averageRate' => (int) round($total / count($usedTechnologies)),
            ];
        }

        //? Triez les familles par niveau moyen ASC, pour un polygone plus lisible
        usort($familyData, function ($a, $b) {
            return $a['averageRate'] <=> $b['averageRate'];
        });

        $labels = [];
        $datas = [];
        foreach ($familyData as $data) {
            $labels[] = $data['name'];
            $datas[] = $data['averageRate'];
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_RADAR);

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => '',
                    'backgroundColor' => 'rgba(24, 188, 156, 0.25)',
                    'borderColor' => '#18bc9c',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => '#2c3e50',
                    'pointBorderColor' => '#fff',
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'data' => $datas,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'datalabels' => [
                    'display' => true,
                    'anchor' => 'end',
                    'align' => 'top',
                    'color' => '#2c3e50',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 10,
                    ],
                ],
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'suggestedMax' => 100,
                    'ticks' => [
                        'display' => false,
                        'stepSize' => 25,
                    ],
                    'grid' => [
                        'color' => 'rgba(44, 62, 80, 0.1)',
                    ],
                    'angleLines' => [
                        'color' => 'rgba(44, 62, 80, 0.12)',
                    ],
                    'pointLabels' => [
                        'font' => [
                            'size' => 11,
                            'weight' => '600',
                        ],
                        'color' => '#2c3e50',
                    ],
                ],
            ],
        ]);

        return $chart;
    }

}