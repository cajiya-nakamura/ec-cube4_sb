<?php

namespace Plugin\SimpleBlog42\Controller\Admin\Content;

use Plugin\SimpleBlog42\Entity\Category;
use Plugin\SimpleBlog42\Form\Type\Admin\BlogCategoryType;
use Plugin\SimpleBlog42\Repository\CategoryRepository;

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/content/blog_category", name="admin_content_blog_category", methods={"GET", "POST"})
     * @Template("@admin/SimpleBlog42/tag.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request)
    {
        $Category = new Category();
        $Categories = $this->categoryRepository->getList();

        /**
         * 新規登録用フォーム
         **/
        $builder = $this->formFactory
            ->createBuilder(BlogCategoryType::class, $Category);

        // $event = new EventArgs(
        //     [
        //         'builder' => $builder,
        //         'Category' => $Category,
        //     ],
        //     $request
        // );

        // $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_INITIALIZE);

        $form = $builder->getForm();

        /**
         * 編集用フォーム
         */
        $forms = [];
        foreach ($Categories as $EditCategory) {
            $id = $EditCategory->getId();
            $forms[$id] = $this
                ->formFactory
                ->createNamed('category_'.$id, ProductTag::class, $EditCategory);
        }

        if ('POST' === $request->getMethod()) {
            /*
             * 登録処理
             */
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->categoryRepository->save($form->getData());

                $this->dispatchComplete($request, $form, $form->getData());

                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_content_blog_category');
            }
            /*
             * 編集処理
             */
            foreach ($forms as $editForm) {
                $editForm->handleRequest($request);
                if ($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->categoryRepository->save($editForm->getData());

                    $this->dispatchComplete($request, $editForm, $editForm->getData());

                    $this->addSuccess('admin.common.save_complete', 'admin');

                    return $this->redirectToRoute('admin_content_blog_category');
                }
            }
        }

        $formViews = [];
        foreach ($forms as $key => $value) {
            $formViews[$key] = $value->createView();
        }

        return [
            'form' => $form->createView(),
            'Tag' => $Category,
            'Tags' => $Categories,
            'forms' => $formViews,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/blog_category/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_blog_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $Category)
    {
        $this->isTokenValid();

        log_info('カテゴリー削除開始', [$Category->getId()]);

        try {
            $this->categoryRepository->delete($Category);

            // $event = new EventArgs(
            //     [
            //         'Tag' => $Category,
            //     ], $request
            // );
            // $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_TAG_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('カテゴリー削除完了', [$Category->getId()]);
        } catch (\Exception $e) {
            log_info('カテゴリー削除エラー', [$Category->getId(), $e]);

            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Category->getName()]);
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_content_blog_category');
    }

    /**
     * @Route("/%eccube_admin_route%/product/tag/sort_no/move", name="admin_content_blog_category_sort_no_move", methods={"POST"})
     */
    public function moveSortNo(Request $request)
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $categoryId => $sortNo) {
                /* @var $Tag \Eccube\Entity\Tag */
                $Category = $this->categoryRepository
                    ->find($categoryId);
                $Category->setSortNo($sortNo);
                $this->entityManager->persist($Category);
            }
            $this->entityManager->flush();
        }

        return new Response();
    }

    protected function dispatchComplete(Request $request, FormInterface $form, Category $Category)
    {
        // $event = new EventArgs(
        //     [
        //         'form' => $form,
        //         'Category' => $Category,
        //     ],
        //     $request
        // );
        // $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_PRODUCT_TAG_INDEX_COMPLETE);
    }
}
