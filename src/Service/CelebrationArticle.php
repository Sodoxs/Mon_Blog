<?php
namespace App\Service;

use App\Entity\Article;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CelebrationArticle
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

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if(!$entity instanceof Article)
        {
            return;
        }

        if($entity->getNbViews()%20 != 0)
        {
            return;
        }
        $this->articleMailer->sendCelebrationMail($entity);
    }

}