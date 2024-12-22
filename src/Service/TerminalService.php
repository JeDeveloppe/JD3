<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\HttpKernel\KernelInterface;

class TerminalService
{
    public function __construct(
        private KernelInterface $kernel
    )
    {}

    public function importSymfonyUxIcon(string $commande)
    {
        // $process = new Process([
        //     'php',
        //     'bin/console',
        //     'app:datawarehouse:tempsFacturable',
        //     $date->format('Y-m-d'),
        // ], $params->get('kernel.project_dir'), null, null, null);
        // $process->start();
        // $process->wait();

        $process = new Process([
            'symfony',
            'console',
            $commande
        ], $this->kernel->getProjectDir(), null, null, null);
        $process->start();
        $process->wait();
    }
}