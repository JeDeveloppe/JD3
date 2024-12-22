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
                dd($e->getDebug());
            }

    }

}