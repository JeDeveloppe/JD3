<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\Technology;
use App\Repository\TechnologyFamilyRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TechnologyService
{
    public function __construct(
        private TechnologyRepository $repository,
        private EntityManagerInterface $em,
        private TechnologyFamilyRepository $technologyFamilyRepository
        )
    {}

    public function importTable(SymfonyStyle $io): void
    {
        $io->title('Importation des technologies');

        $donneesFromCsv = $this->readCsvFile();
        
        $io->progressStart(count($donneesFromCsv));

        foreach($donneesFromCsv as $arrayCsv){
            $io->progressAdvance();
            $entity = $this->createOrUpdateEntity($arrayCsv);
            $this->em->persist($entity);
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('Importation terminée');
    }

    //lecture des fichiers exportes dans le dossier import
    private function readCsvFile(): Reader
    {
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/_table_technology.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateEntity(array $arrayCsv): Technology
    {
        $entity = $this->repository->findOneBy(['name' => $arrayCsv['name']]);

        if(!$entity){
            $entity = new Technology();
        }

        // "id","name","knowledge_rate","command_line_in_terminal","render_icon_string_without_parentheses","order_of_appearance","family_id","description"
        $entity
            ->setName($arrayCsv['name'])
            ->setKnowledgeRate($arrayCsv['knowledge_rate'])
            ->setCommandLineInTerminal($arrayCsv['command_line_in_terminal'])
            ->setRenderIconStringWithoutParentheses($arrayCsv['render_icon_string_without_parentheses'])
            ->setOrderOfAppearance($arrayCsv['order_of_appearance'])
            ->setFamily($this->technologyFamilyRepository->find($arrayCsv['family_id']))
            ->setDescription($arrayCsv['description'])
            ;

        return $entity;
    }

}