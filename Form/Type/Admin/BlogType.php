<?php

namespace Plugin\SimpleBlog42\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Plugin\SimpleBlog42\Entity\Blog;
use Plugin\SimpleBlog42\Entity\Category;
use Plugin\SimpleBlog42\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class BlogType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(
        EccubeConfig $eccubeConfig,
        CategoryRepository $categoryRepository
    )
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publish_date', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'years' => range($this->eccubeConfig['eccube_news_start_year'], date('Y') + 3),
                'with_seconds' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range([
                        'min'=> '0003-01-01',
                        'minMessage' => 'form_error.out_of_range',
                    ]),
                ],
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_mtext_len']]),
                ],
            ])
            ->add('url', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Url(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_mtext_len']]),
                ],
            ])
            ->add('link_method', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.content.news.new_window',
                'value' => '1',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'purify_html' => true,
                'attr' => [
                    'rows' => 8,
                ],
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            ->add('visible', ChoiceType::class, [
                'label' => false,
                'choices' => ['admin.content.news.display_status__show' => true, 'admin.content.news.display_status__hide' => false],
                'required' => true,
                'expanded' => false,
            ])
            ->add('Category', ChoiceType::class, [
                'choice_label' => 'Name',
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $this->categoryRepository->getList(null, true),
                'choice_value' => function (Category $Category = null) {
                    return $Category ? $Category->getId() : null;
                },
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_blog';
    }
}
