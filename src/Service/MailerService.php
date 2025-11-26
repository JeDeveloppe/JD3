<?php

namespace App\Service;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\LegalInformationRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private LegalInformationRepository $legalInformationRepository,
        ){
    }

    /**
     * Envoie un email de la page de contact.
     */
    public function sendMailFromContactPage(string $subject, array $donnees)
    {

        $legales = $this->legalInformationRepository->findOneBy([]);
        $template = 'contact';
        $donnees['legales'] = $legales;

        $email = (new TemplatedEmail())
            ->from('noreply@je-developpe.fr')
            ->to(new Address($_ENV['ADMIN_EMAIL']))
            ->replyTo(new Address($donnees['mail']))
            ->subject($subject)
            ->htmlTemplate('email/templates/'.$template.'.html.twig')
            ->context($donnees);

            try{
    
                $this->mailer->send($email);
                
            } catch (TransportExceptionInterface $e) {
                // Pour éviter d'arrêter le processus en production, il est préférable 
                // de logger l'erreur plutôt que de faire un dd()
                // dd($e->getDebug()); 
                error_log('MailerService error (Contact Page): ' . $e->getMessage());
            }
    }
    
    /**
     * Envoie une alerte lorsque le CV est consulté par un nouvel utilisateur (nouvelle session).
     * @param int $totalViews Le nouveau nombre total de vues.
     */
    public function sendCvViewAlert(int $totalViews): void
    {
        $subject = "👀 Nouvelle consultation de votre CV en ligne ! (#{$totalViews})";
        
        $email = (new TemplatedEmail())
            // Utilisez une adresse d'envoi vérifiée
            ->from('noreply@je-developpe.fr') 
            // Adresse du destinataire (votre adresse email)
            ->to(new Address($_ENV['ADMIN_EMAIL'])) 
            ->subject($subject)
            // Vous pouvez créer un template Twig spécifique pour cette alerte (ex: 'email/templates/cv_view_alert.html.twig')
            // Sinon, utilisez un corps de message simple en HTML ou texte.
            ->html(
                '<h1>Alerte CV: Nouvelle Vue !</h1>' .
                '<p>Quelqu\'un vient de consulter votre CV en ligne à l\'adresse: <a href="https://je-developpe.fr/cv-manager">je-developpe.fr/cv-manager</a></p>' .
                '<p>C\'est la <strong>' . $totalViews . ($totalViews === 1 ? 'ère' : 'ème') . '</strong> vue unique enregistrée !</p>' .
                '<p>Bonne chance pour le recrutement !</p>'
            );

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // Loggez l'erreur pour le débogage si l'envoi échoue
            error_log('MailerService error (CV View Alert): ' . $e->getMessage());
        }
    }
}