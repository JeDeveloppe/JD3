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
                    'backgroundColor' => '#3b868f',
                    'borderColor' => '#333333',
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
                    'color' => 'red',
                    'font' => [
                        'weight' => 'bold',
                    ],
                ],
                'legend' => [
                    'display' => false,
                ],
            ],
        //     'scales' => [
        //         'y' => [
        //             'suggestedMin' => 0,
        //             'suggestedMax' => 100,
        //         ],
        //     ],
        ]);

        return $chart;
    }

}