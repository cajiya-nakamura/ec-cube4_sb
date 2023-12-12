<?php

namespace Plugin\SimpleBlog42\Controller;

use Eccube\Controller\AbstractController;

use Plugin\SimpleBlog42\Repository\BlogRepository;
use Plugin\SimpleBlog42\Entity\Blog;
use Plugin\SimpleBlog42\Repository\CategoryRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\Component\Pager\PaginatorInterface;
// use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\Query\Expr\Join;

class BlogController extends AbstractController
{

  /**
   * @var BlogRepository
   */
  protected $blogRepository;

  /**
   * @var CategoryRepository
   */
  protected $categoryRepository;

  /**
   * BlogController constructor.
   *
   * @param BlogRepository $blogRepository
   * @param CategoryRepository $categoryRepository
   */
  public function __construct(
    BlogRepository $blogRepository,
    CategoryRepository $categoryRepository
  )
  {
      $this->blogRepository = $blogRepository;
      $this->categoryRepository = $categoryRepository;
  }


  /**
   * ブログ一覧画面.
   *
   * @Route( "/blog" , name="blog_index" )
   * @Template("SimpleBlog42/index.twig")
   */
  public function index( Request $request, PaginatorInterface $paginator )
  {
    // handleRequestは空のqueryの場合は無視するため
    if ($request->getMethod() === 'GET') {
        $request->query->set('pageno', $request->query->get('pageno', '1'));
    }


    $qb = $this->blogRepository->getQueryBuilderPublished();


    // 値の確認 int or false
    $category_id = filter_var( $request->query->get('category_id') , FILTER_VALIDATE_INT);

    $Category = null;
    if ($category_id !== null) {

      $Category = $this->categoryRepository->findOneBy(['id' => $category_id ]);

      if( $Category !== null ){
        $qb->join('Plugin\SimpleBlog42\Entity\BlogCategory', 'bc', Join::WITH, 'bc.Blog = n.id')
          ->andWhere('bc.Category = :category_id')
          ->setParameter('category_id', $category_id);
      }
    }
  
    $query = $qb->getQuery();

    /** @var SlidingPagination $pagination */
    $pagination = $paginator->paginate(
        $query,
        $request->query->get('pageno', '1')
    );

    $Categories = $this->categoryRepository->getList();

    return [
      'Categories' => $Categories,
      'Category' => $Category,
      'pagination' => $pagination,
    ];
  }

  /**
   * ブログ詳細画面.
   *
   * @Route("/blog/{id}" , name="blog_detail" )
   * @Template("SimpleBlog42/detail.twig")
   * @ParamConverter("Blog", options={"id" = "id"})
   */
  public function detail( Request $request, Blog $Blog )
  {
    
    if ( !$this->checkVisibility($Blog) ) {
      throw new NotFoundHttpException();
    }

    $BlogUrl = $Blog->getUrl();

    if ( $BlogUrl !== null ){
      return new RedirectResponse( $BlogUrl );
    }
    
    $Categories = $Blog->getCategories();

    return [
      'Categories' => $Categories,
      'blog' => $Blog,
    ];
  }

  /**
   * 閲覧可能なブログかどうかを判定
   *
   * @param Blog $Blog
   *
   * @return boolean 閲覧可能な場合はtrue
   */
  protected function checkVisibility(Blog $Blog)
  {
      $is_admin = $this->session->has('_security_admin');

      $date = time();
      
      // 管理ユーザの場合はステータスやオプションにかかわらず閲覧可能.
      if (!$is_admin) {
          // 公開ステータスでない商品は表示しない.
          if ( $Blog->isVisible() === false ) {
            return false;
          }elseif( $Blog->getPublishDate()->getTimestamp() >= $date ) {
            return false;
          }else{
            return true;
          }
      }

      return true;
  }
}
