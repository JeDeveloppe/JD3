<?php

namespace App\Service;

use League\Csv\Reader;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategoryService
{
    public function __construct(
        private CategoryRepository $repository,
        private EntityManagerInterface $em
        )
    {}

    public function importTable(SymfonyStyle $io): void
    {
        $io->title('Importation des categories');

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
        $csvClients = Reader::createFromPath('%kernel.root.dir%/../import/_table_category.csv','r');
        $csvClients->setHeaderOffset(0);

        return $csvClients;
    }

    private function createOrUpdateEntity(array $arrayCsv): Category
    {
        $entity = $this->repository->findOneByName($arrayCsv['name']);

        if(!$entity){
            $entity = new Category();
        }

        // "id","name","command_line_in_terminal","render_icon_string_without_parentheses","created_at","updated_at","slug"
        $entity
            ->setName($arrayCsv['name'])
            ->setCommandLineInTerminal($arrayCsv['command_line_in_terminal'])
            ->setRenderIconStringWithoutParentheses($arrayCsv['render_icon_string_without_parentheses'])
            ->setCreatedAt(new \DateTimeImmutable($arrayCsv['created_at']))
            ->setUpdatedAt(new \DateTimeImmutable($arrayCsv['updated_at']))
            ->setSlug($arrayCsv['slug']);

        return $entity;
    }

}