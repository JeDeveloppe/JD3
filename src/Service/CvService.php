<?php

namespace App\Service;

use App\Service\MailerService;
use App\Repository\CvRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cv; // Assurez-vous d'importer votre entité
use Symfony\Component\HttpFoundation\RequestStack; // Importation de RequestStack

class CvService
{
    // Clé de session utilisée pour marquer que la vue a été comptée
    private const SESSION_KEY_COUNTED = 'cv_view_counted';

    public function __construct(
        private CvRepository $repository,
        private EntityManagerInterface $em,
        private RequestStack $requestStack, // Injection de la RequestStack
        private MailerService $mailerService
        )
    {}

    /**
     * Récupère l'entité Cv (la crée si elle n'existe pas)
     * et incrémente le compteur si ce n'est pas déjà fait pour cette session.
     * @return int Le nombre de vues actuel (après potentielle incrémentation).
     */
    public function returnNumberOfViewsAndAddNewView($cvName): int
    {
        // Récupérer l'objet Session
        $session = $this->requestStack->getSession();

        // 1. Tenter de trouver l'enregistrement existant
        // On trie par 'id' ascendant pour garantir qu'on prend le premier (et unique)
        $entity = $this->repository->findOneBy(['name' => $cvName]);
        
        // 2. Si l'entité n'existe PAS, la créer
        if (!$entity) {
            $entity = new Cv();
            $entity->setNumberOfView(0); // Initialiser à 0
            $entity->setName($cvName);
            // Si votre entité CV a d'autres champs obligatoires, vous devez les définir ici
        }

        $currentViews = $entity->getNumberOfView();

        // 3. Vérifier si la vue a déjà été comptée pour cette session
        if ($session->get(self::SESSION_KEY_COUNTED) !== true) {
            // Incrémenter le compteur
            $entity->setNumberOfView($currentViews + 1);
            
            // 4. Marquer la session pour ne pas compter à nouveau
            $session->set(self::SESSION_KEY_COUNTED, true);

            // 5. Persister et enregistrer dans la base de données (uniquement si incrémenté)
            $this->em->persist($entity);
            $this->em->flush();

            // 6. Optionnel: envoyer une alerte par email (si vous avez un service mail configuré)
            $this->mailerService->sendCvViewAlert($currentViews + 1, $cvName);
            
            // Retourner le nouveau nombre de vues
            return $currentViews + 1;
        }

        // Si la vue a déjà été comptée pour cette session, retourner le compte actuel
        return $currentViews;
    }
}