<?php

namespace App\Service; 

use Symfony\Component\Process\Process;

class TerminalService
{
    public function importSymfonyUxIcone(string $command): void
    {
        $process = new Process(
            [$command]
        );
        $process->setWorkingDirectory(__DIR__ ."./../../");
        
        $process->start();
    }
}