<?php

namespace WH\SeoBundle\Form\Backend;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MetasType
 *
 * @package WH\SeoBundle\Form\Backend
 */
class MetasType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label'    => 'Meta title :',
                    'required' => false,
                    'attr'     => [
                        'class' => 'wh-count-chars',
                    ],
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label'    => 'Meta description :',
                    'required' => false,
                    'attr'     => [
                        'class' => 'wh-count-chars',
                    ],
                ]
            )
            ->add(
                'robots',
                ChoiceType::class,
                [
                    'label'       => 'Meta robots :',
                    'empty_value' => false,
                    'empty_data'  => 'index,follow',
                    'required'    => false,
                    'choices'     => [
                        'index,follow'     => 'index,follow',
                        'index,nofollow'   => 'index,nofollow',
                        'noindex,follow'   => 'noindex,follow',
                        'noindex,nofollow' => 'noindex,nofollow',
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'WH\SeoBundle\Entity\Metas',
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wh_seobundle_metas';
    }

}
