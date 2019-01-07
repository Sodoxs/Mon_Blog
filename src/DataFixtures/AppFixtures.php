<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $faker;
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @Param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <=10; $i++)
        {
            $article = $manager->getRepository('App:Article')
                ->find(rand(1,4));
            $comment = new Comment();
            $comment->setTitle("testTitleFixture");
            $comment->setAuthor("testAuthorFixture");
            $comment->setMessage("testMessageFixture");
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);
            $manager->persist($comment);
        }
        $manager->flush();
    }
}
