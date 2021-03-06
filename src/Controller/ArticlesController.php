<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Repository\ArticleCategoryRepository;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends AbstractController
{
    public function list(
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $articles = $articleRepository->findBy(['active' => true], ['id' => 'desc']);

        $paginatorArticles = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            30
        );

        $categories = $articleCategoryRepository->findBy(['active' => true], ['id' => 'asc']);

        return $this->render('articles.html.twig', [
            'articles' => $paginatorArticles,
            'categories' => $categories,
            'selectedCategory' => 'Всички',
        ]);
    }

    public function listCategory(
        ArticleCategory $category,
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $articles = $articleRepository->findBy(['articleCategory' => $category, 'active' => true], ['id' => 'desc']);

        $paginatorArticles = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            30
        );

        $categories = $articleCategoryRepository->findBy(['active' => true], ['id' => 'asc']);

        return $this->render('articles.html.twig', [
            'articles' => $paginatorArticles,
            'categories' => $categories,
            'selectedCategory' => $category->getName(),
        ]);
    }

    public function show(Article $article, ArticleRepository $articleRepository): Response
    {
        $similarArticles = $articleRepository->findSimilar($article, 4);

        return $this->render('article.html.twig', [
            'article' => $article,
            'similarArticles' => $similarArticles,
        ]);
    }

    public function showOld(Article $article): RedirectResponse
    {
        return $this->redirectToRoute('articles_show', ['slug' => $article->getSlug()], 301);
    }
}
