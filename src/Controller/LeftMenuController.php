<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LeftMenuController
 * @package App\Controller
 *
 * @Route("/blog")
 */

class LeftMenuController extends AbstractController
{

    /**
     * @Route("/article/last", name = "last-article")
     */
    public function lastArticlesAction($max)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('App:Article')->findBy(
            array('published' => '1'),
            array('created_at' => 'desc'),
            $max
        );
        return $this->render('last_articles.html.twig', array('max' => $max, 'articles'=>$articles));
    }

    /**
     * @Route("/list/categories", name = "list-categories")
     */
    public function categoryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('App\Entity\Category')->findAll();
        return $this->render('categories.html.twig', array('categories'=>$categories));
    }
}