<?php

namespace App\Command;

use App\Service\CategoryService;
use App\Service\LegalInformationService;
use App\Service\ProjectService;
use App\Service\TechnologyFamilyService;
use App\Service\TechnologyService;
use App\Service\TrainingService;
use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:initdatabase1')]

class InitDataBase1 extends Command
{
    public function __construct(
            private LegalInformationService $legalInformationService,
            private UserService $userService,
            private CategoryService $categoryService,
            private TechnologyFamilyService $technologyFamilyService,
            private TechnologyService $technologyService,
            private TrainingService $trainingService,
            private ProjectService $projectService
        )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // ini_set('memory_limit', '2048M');
        ini_set("memory_limit", -1);

        $io = new SymfonyStyle($input,$output);

        //on créer l'admin
        $this->userService->initForProd_adminUser($io);
        //on crer les information legale
        $this->legalInformationService->creationLegalInformation($io);
        //on crer les categories
        $this->categoryService->importTable($io);
        //on crer les familles technologiques
        $this->technologyFamilyService->importTable($io);
        //on crer les technologies
        $this->technologyService->importTable($io);
        //on crer les formations
        $this->trainingService->importTable($io);
        //on crer les projets
        // $this->projectService->importTable($io); //TODO DateTimeImmutable problems
        //on met les technologies dans les projets
        // $this->projectService->addTechnologiesInProject($io);

        return Command::SUCCESS;
    }

}