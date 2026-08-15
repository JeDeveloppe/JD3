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
        $labels = [];
        $datas = [];
        $technologyData = [];

        foreach ($technologiesFamilies as $technologyFamily) {
            $technologies = $technologyFamily->getTechnologies();

            foreach ($technologies as $technology) {
                $technologyData[] = [
                        'name' => $technology->getName(),
                        'knowledgeRate' => $technology->getKnowledgeRate(),
                    ];
            }
        }

        //? Maintenant, triez le tableau $technologyData par 'knowledgeRate' en ASC
        usort($technologyData, function($a, $b) {
            return $a['knowledgeRate'] <=> $b['knowledgeRate'];
        });

        //? Ensuite, extrayez les valeurs 'name' et 'knowledgeRate' de chaque élément du tableau
        foreach($technologyData as $data) {
            $labels[] = $data['name'];
            $datas[] = $data['knowledgeRate'];
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