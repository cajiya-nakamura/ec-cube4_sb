<?php

namespace Plugin\SimpleBlog42\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Plugin\SimpleBlog42\Entity\Blog;
use Plugin\SimpleBlog42\Form\Type\Admin\BlogType;
use Plugin\SimpleBlog42\Repository\BlogRepository;
use Eccube\Util\CacheUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @var BlogRepository
     */
    protected $blogRepository;

    /**
     * BlogController constructor.
     *
     * @param BlogRepository $blogRepository
     */
    public function __construct(BlogRepository $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * ブログ一覧を表示する。
     *
     * @Route("/%eccube_admin_route%/content/blog", name="admin_content_blog", methods={"GET"})
     * @Route("/%eccube_admin_route%/content/blog/page/{page_no}", requirements={"page_no" = "\d+"}, name="admin_content_blog_page", methods={"GET"})
     * @Template("@admin/SimpleBlog42/blog.twig")
     *
     * @param Request $request
     * @param int $page_no
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function index(Request $request, PaginatorInterface $paginator, $page_no = 1)
    {
        $qb = $this->blogRepository->getQueryBuilderAll();

        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            $this->eccubeConfig->get('eccube_default_page_count')
        );

        return [
            'pagination' => $pagination,
        ];
    }

    /**
     * ブログを登録・編集する。
     *
     * @Route("/%eccube_admin_route%/content/blog/new", name="admin_content_blog_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/content/blog/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_blog_edit", methods={"GET", "POST"})
     * @Template("@admin/SimpleBlog42/blog_edit.twig")
     *
     * @param Request $request
     * @param null $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, CacheUtil $cacheUtil, $id = null)
    {
        if ($id) {
            $Blog = $this->blogRepository->find($id);
            if (!$Blog) {
                throw new NotFoundHttpException();
            }
        } else {
            $Blog = new Blog();
            $Blog->setPublishDate(new \DateTime());
        }

        $builder = $this->formFactory
            ->createBuilder(BlogType::class, $Blog);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$Blog->getUrl()) {
                $Blog->setLinkMethod(false);
            }
            $this->blogRepository->save($Blog);

            $this->addSuccess('admin.common.save_complete', 'admin');

            // キャッシュの削除
            $cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('admin_content_news_edit', ['id' => $Blog->getId()]);
        }

        return [
            'form' => $form->createView(),
            'News' => $Blog,
        ];
    }

    /**
     * 指定したブログを削除する。
     *
     * @Route("/%eccube_admin_route%/content/blog/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_blog_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Blog $Blog
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, Blog $Blog, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        log_info('ブログ削除開始', [$Blog->getId()]);

        try {
            $this->blogRepository->delete($Blog);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('ブログ削除完了', [$Blog->getId()]);

            // キャッシュの削除
            $cacheUtil->clearDoctrineCache();
        } catch (\Exception $e) {
            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Blog->getTitle()]);
            $this->addError($message, 'admin');

            log_error('ブログ削除エラー', [$Blog->getId(), $e]);
        }

        return $this->redirectToRoute('admin_content_news');
    }
}
