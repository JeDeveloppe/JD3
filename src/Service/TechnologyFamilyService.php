<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\TechnologyFamily;
use App\Repository\TechnologyFamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TechnologyFamilyService
{
    public function __construct(
        private TechnologyFamilyRepository $repository,
        private EntityManagerInterface $em
        )
    {}

    public function importTable(SymfonyStyle $io): void
    {
        $io->title('Importation des familles technologiques');

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
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/_table_technology_family.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateEntity(array $arrayCsv): TechnologyFamily
    {
        $entity = $this->repository->findOneByName($arrayCsv['name']);

        if(!$entity){
            $entity = new TechnologyFamily();
        }

        // "id","name","description","order_of_appearance"
        $entity
            ->setName($arrayCsv['name'])
            ->setDescription($arrayCsv['description'])
            ->setOrderOfAppearance($arrayCsv['order_of_appearance'])
            ;

        return $entity;
    }

}