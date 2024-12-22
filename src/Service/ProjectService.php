<?php

namespace App\Service;

use DateTimeZone;
use DateTimeImmutable;
use League\Csv\Reader;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProjectService
{
    public function __construct(
        private ProjectRepository $repository,
        private TechnologyRepository $technologyRepository,
        private EntityManagerInterface $em,
        )
    {}

    public function importTable(SymfonyStyle $io): void
    {
        $io->title('Importation des projets');

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
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/_table_project.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateEntity(array $arrayCsv): Project
    {
        $entity = $this->repository->findOneByName($arrayCsv['name']);

        if(!$entity){
            $entity = new Project();
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));

        //"id","name","description","started_at","end_at","url","image_name","updated_at","is_online"
        $entity
            ->setName($arrayCsv['name'])
            ->setDescription($arrayCsv['description'])
            ->setStartedAt(new \DateTimeImmutable($arrayCsv['started_at'], new DateTimeZone('Europe/Paris')))
            ->setEndAt(new \DateTimeImmutable($arrayCsv['end_at'], new DateTimeZone('Europe/Paris')))
            ->setUrl($arrayCsv['url'])
            ->setImageName($arrayCsv['image_name'])
            ->setIsOnline(intval($arrayCsv['is_online']))
            ;

        return $entity;
    }

    public function addTechnologiesInProject(SymfonyStyle $io): void
    {
        $io->title('Importation des technologies par projet');

            $relations = $this->readCsvFileRelationProjectTechnologies();
        
            $io->progressStart(count($relations));

            foreach($relations as $relation){

                $io->progressAdvance();
                $project = $this->createOrUpdateRelationProjectTechnologies($relation);

                $this->em->persist($project);

            }
            
            $this->em->flush();

            $io->progressFinish();
        

        $io->success('Importation terminée');
    }

    private function readCsvFileRelationProjectTechnologies(): Reader
    {
        $csv = Reader::createFromPath('%kernel.root.dir%/../import/_table_project_technology.csv','r');
        $csv->setHeaderOffset(0);

        return $csv;
    }

    private function createOrUpdateRelationProjectTechnologies(array $arrayRelation): Project
    {
        $project = $this->repository->findOneBy(['id' => $arrayRelation['project_id']]);
        $technology = $this->technologyRepository->findOneBy(['id' => $arrayRelation['technology_id']]);

        $project->addTechnology($technology);

        return $project;
    }
}