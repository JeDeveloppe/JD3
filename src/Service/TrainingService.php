<?php

namespace App\Service;

use App\Entity\Training;
use League\Csv\Reader;

use App\Repository\TechnologyFamilyRepository;
use App\Repository\TrainingRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TrainingService
{
    public function __construct(
        private TrainingRepository $repository,
        private EntityManagerInterface $em,
        private UtilitiesService $utilitiesService
        )
    {}

    public function importTable(SymfonyStyle $io): void
    {
        $io->title('Importation des formations');

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
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/_table_training.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateEntity(array $arrayCsv): Training
    {
        $entity = $this->repository->findOneByName($arrayCsv['name']);

        if(!$entity){
            $entity = new Training();
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
        //"id","name","description","started_at","end_at","diplome_name","is_online"

        $entity
            ->setName($arrayCsv['name'])
            ->setDescription($arrayCsv['description'])
            ->setStartedAt($now)
            ->setEndAt($now)
            ->setDiplomeName($arrayCsv['diplome_name'])
            ->setIsOnline($arrayCsv['is_online'])
            ;

        return $entity;
    }

}