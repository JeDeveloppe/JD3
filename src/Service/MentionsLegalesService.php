<?php

namespace App\Service;

use App\Entity\LegalInformation;
use Symfony\Component\Routing\RouterInterface;

class MentionsLegalesService
{
    public function __construct(
        private RouterInterface $routerInterface
    )
    {

    }
    public function mentionsParagraphs(LegalInformation $legales){

        $site = $legales->getCompanyName();

        $paragraphs = [
            [
            'title' => 'PROPRIÉTÉ INTELLECTUELLE',
            'text' => 'Sauf mention contraire, l’ensemble du contenu du site '.$site.' (textes, code, illustrations, mise en page) est la propriété de son éditeur et est protégé par le droit de la propriété intellectuelle.<br/>
                    Toute reproduction ou réutilisation, totale ou partielle, est soumise à autorisation préalable. Les articles peuvent être cités et partagés en indiquant la source et un lien vers la page d’origine.'
            ]
            ,
            [
            'title' => 'RESPONSABILITÉ',
            'text' => 'Les contenus sont publiés à titre d’information et peuvent évoluer ou comporter des inexactitudes. L’éditeur du site '.$site.' ne saurait être tenu responsable des conséquences de leur utilisation, ni de l’indisponibilité temporaire du site.<br/>
                    Le site peut renvoyer vers des sites tiers dont l’éditeur ne maîtrise pas le contenu.'
            ]
            ,
            [
            'title' => 'DONNÉES PERSONNELLES',
            'text' => 'Les données transmises via le formulaire de contact (adresse email, message) sont utilisées exclusivement pour répondre à votre demande et ne sont ni cédées, ni vendues à des tiers.<br/>
            Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez d’un droit d’accès, de rectification et de suppression des données vous concernant.<br/>
            Pour exercer ce droit, vous pouvez écrire à '.$legales->getEmailCompany().'.<br/>
            Le formulaire de contact est protégé par Google reCAPTCHA, soumis à la <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">politique de confidentialité</a> et aux <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">conditions d’utilisation</a> de Google.'
            ]
            ,
            [
            'title' => 'COOKIES',
            'text' => 'Le site utilise Google Analytics afin de mesurer sa fréquentation. Ce service dépose un cookie de mesure d’audience uniquement après votre consentement, recueilli via le bandeau affiché lors de votre première visite.<br/>
            Vous pouvez à tout moment modifier votre choix en cliquant sur « Gérer les cookies » en bas de page.<br/>
            Aucun cookie publicitaire ou de traçage tiers n’est utilisé sur ce site.'
            ]

        ];

        return $paragraphs;
    }
}
