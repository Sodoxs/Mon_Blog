<?php
namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\Verification;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ArticleController
 * @package App\Controller
 *
 * @Route("/blog")
 */

class ArticleController extends AbstractController
{
    /**
     * @Route("/",
     *     name = "accueil-blog"
     * )
     */
    public function accueilBlogController()
    {
        return $this->render('accueil_blog.html.twig');
    }

    /**
     * @Route("/list/{page}",
     *     defaults = {"page" : "1"},
     *     requirements = {"page" : "^[1-9]+[0-9]*"},
     *     name = "list-article"
     * )
     */
    public function listAction($page)
    {
        $nbArticles = $this->getParameter('nb_articles_by_page');
        $em = $this->getDoctrine()->getManager();
        /* @var $articleRepository ArticleRepository */
        $articleRepository = $em->getRepository('App:Article');
        $articles = $articleRepository->myFindAllWithPaging($page,
            $nbArticles);

        $nbTotalPages = intval(ceil(count($articles) / $nbArticles));
        if($page> $nbTotalPages)
        {
            throw $this->createNotFoundException("Page non trouvé");
        }
        return $this->render('list.html.twig', array('page'=>$page, 'articles'=>$articles,
            'totalpage'=>$nbTotalPages));
    }

    /**
     * @Route("/article/slug/{slug}",
     *     name = "vue-slug-article"
     * )
     */
    public function viewSlugAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $articleRepository ArticleRepository */
        $articleRepository = $em->getRepository('App:Article');
        $article = $articleRepository->findSlugWithCategories($slug);
        if(!$article)
        {
            throw $this->createNotFoundException("Article non trouvé");
        }
        /*$articleViews = $articleRepository->find($slug);
        if(!$articleViews)
        {
            throw $this->createNotFoundException("Article non trouvé");
        }
        $articleViews->setNbViews($articleViews->getNbViews() + 1);*/
        $em->persist($article);
        $em->flush();
        return $this->render('view_slug.html.twig', array('article'=>$article, 'slug'=>$slug));
    }

    /**
     * @Route("/article/{id}",
     *     requirements={"id" : "-?\d+"},
     *     name = "vue-article"
     * )
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $articleRepository ArticleRepository */
        $articleRepository = $em->getRepository('App:Article');
        $article = $articleRepository->findWithCategories($id);

        if(!$article)
        {
            throw $this->createNotFoundException("Article non trouvé");
        }
        $articleViews = $articleRepository->find($id);
        if(!$articleViews)
        {
            throw $this->createNotFoundException("Article non trouvé");
        }
        $articleViews->setNbViews($articleViews->getNbViews() + 1);
        $em->persist($article);
        $em->flush();
        return $this->render('view.html.twig', array('article'=>$article, 'id'=>$id, 'nbviews'=>$articleViews->getNbViews()));
    }

    /**
     * @Route("/article/slug/add",
     *     name = "add-slug-article")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function addSlugAction()
    {
        $em = $this->getDoctrine()->getManager();
        $article = new Article();
        $article->setTitle('Slug')->setContent('Slug Test')->setAuthor('SluggerMan')
            ->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime())->setNbViews(3)->setPublished(1);
        $em->persist($article);
        $em->flush();
        return new Response('<body></body>');
    }

    /**
     * @Route("/article/add",
     *     name = "add-article")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Verification $antispam)
    {
        $em = $this->getDoctrine()->getManager();
        //$article = new Article();
        //$article->setTitle('test')->setContent('contenttest')->setAuthor('testauthor')
            //->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime())->setNbViews(3)->setPublished(1);

        //$em->persist($article);
        //$em->flush();
        $article = new Article();
        $article->setCreatedAt(new \DateTime());
        $article->setUpdatedAt(new \DateTime());
        $article->setNbViews(1);
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('Ajouter', SubmitType::class, array('label' => 'addArticle'));
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($antispam->Spam($article->getTitle()) or
            $antispam->Spam($article->getContent()) or
            $antispam->Spam($article->getAuthor()))
            {
                $this->addFlash('info', "Spam detected");
                return $this->render('add.html.twig', array('form' => $form->createView()));
            }
            else
            {
                try
                {
                    $em->persist($article);
                    $em->flush();
                    $this->addFlash('info', "article added");
                }
                catch(\Exception $e)
                {
                    $this->addFlash('info', "Added fail");
                    #$msg = $this->get('translator')->trans('article.fail');
                    #$this->addFlash('info', $msg);
                }
                return $this->redirectToRoute('accueil');
            }
        }
        return $this->render('add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/edit/{num}",
     *     requirements={"num" : "-?[0-9]+"},
     *     name = "edit-article"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction($num, Request $request, Verification $antispam)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->findOneBy(array('id'=>$num));
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('Editer', SubmitType::class, array('label' => 'article.edit'));
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if($antispam->Spam($article->getTitle()) or
                $antispam->Spam($article->getContent()) or
                $antispam->Spam($article->getAuthor()))
            {
                $this->addFlash('info', "Spam detected");
                return $this->render('add.html.twig', array('form' => $form->createView()));
            }
            else
            {
                try
                {
                    $em->flush();
                    $this->addFlash('info', "article édité");
                } catch (\Exception $e) {
                    $this->addFlash('info', "Erreur lors de la modification");
                }
                return $this->redirectToRoute('vue-article', array('id' => $num));
            }
        }
        return $this->render('edit.html.twig', array('id' => $num, 'form' => $form->createView()));
    }

    /**
     * @Route("/article/delete/{id}",
     *     requirements={"id" : "-?[0-9]+"},
     *     name = "delete-article"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);

        if(!$article)
        {
            throw $this->createNotFoundException("Article non trouvé");
        }

        $em->remove($article);
        $em->flush();
        if(true)
        {
            $this->addFlash('info', "article supprimé");
            return $this->redirectToRoute('list-article');
        }
        return $this->redirectToRoute('accueil');
    }

    /**
     * @Route("/category/{id}",
     *     defaults = {"id" : "1"},
     *     requirements = {"page" : "-?\d+"},
     *     name = "list-by-category"
     * )
     */
    public function listByCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $articleRepository ArticleRepository */
        $articleRepository = $em->getRepository('App:Article');
        $articles = $articleRepository->findByCategory($id);

        $categoryRepository = $em->getRepository('App:Category');
        $category = $categoryRepository->find($id);
        if (!$category)
        {
            throw $this->createNotFoundException("Page non trouvé");
        }
        return $this->render('list_by_category.html.twig', array('articles' => $articles, 'category' => $category));
    }
}