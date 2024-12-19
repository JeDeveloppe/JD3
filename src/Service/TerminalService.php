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
        exec($commande);
    }
}