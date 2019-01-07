<?php
namespace App\Service;

use App\Entity\Article;

class Mailer
{
    /** @var \Swift_Mailer */
    private $mailer;

    /**
     * @Mailer constructor
     * @param $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendCreateMail(Article $article)
    {
        $message = new \Swift_Message(
            "Article {$article->getTitle() } a été créer"
        );
        $message
            ->addTo('guillaume.garcia020@gmail.com')
            ->addFrom('guillaume.garcia020@gmail.com');

        $this->mailer->send($message);
    }

    public function sendCelebrationMail(Article $article)
    {
        //$mail = $this->getParameter('mail');
        $message = new \Swift_Message(
            "Article {$article->getTitle() } a atteint les {$article->getNbViews() } vues"
        );
        $message
            ->addTo('guillaume.garcia020@gmail.com')
            ->addFrom('guillaume.garcia020@gmail.com');

        $this->mailer->send($message);
    }
}