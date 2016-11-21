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
				array(
					'label'    => 'Balise title :',
					'required' => false,
					'attr'     => array(
						'class' => 'wh-count-chars',
					),
				)
			)
			->add(
				'description',
				TextType::class,
				array(
					'label'    => 'Meta description :',
					'required' => false,
					'attr'     => array(
						'class' => 'wh-count-chars',
					),
				)
			)
			->add(
				'robots',
				ChoiceType::class,
				array(
					'label'       => 'Meta robots :',
					'empty_value' => false,
					'empty_data'  => 'index,follow',
					'required'    => false,
					'choices'     => array(
						'index,follow'     => 'Indexer l\'url et suivre les liens (index,follow)',
						'index,nofollow'   => 'Indexer l\'url et ne pas suivre les liens (index,nofollow)',
						'noindex,follow'   => 'Ne pas indexer l\'url et suivre les liens (noindex,follow)',
						'noindex,nofollow' => 'Ne pas indexer l\'url et ne pas suivre les liens (noindex,nofollow)',
					),
				)
			);
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class' => 'WH\SeoBundle\Entity\Metas',
			)
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
