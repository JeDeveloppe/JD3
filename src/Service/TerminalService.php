<?php

namespace App\Service;

use App\Kernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class TerminalService
{
    public function __construct(
        private KernelInterface $kernel
    )
    {}

    public function importSymfonyUxIcon(string $commande)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $commande
        ]);
        
        // Use the NullOutput class instead of BufferedOutput.
        $output = new NullOutput();

        $application->run($input, $output);

        return new Response("Command succesfully executed from the controller");
    }
}