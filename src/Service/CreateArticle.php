<?php
namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CreateArticle
{
    /** @var Mailer */
    private $articleMailer;

    /**
     * Creation constructor
     * @param Mailer $articleMailer
     */
    public function __construct(Mailer $articleMailer)
    {
        $this->articleMailer = $articleMailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if(!$entity instanceof Article)
        {
            return;
        }

        $this->articleMailer->sendCreateMail($entity);
    }

}