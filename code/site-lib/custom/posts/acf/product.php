<?php

namespace Site\Custom\Posts\Acf;

use Site\Custom\Custom_Type;

/**
 * Class Product
 * @package Site\Custom\Post
 */
class Product extends Custom_Type
{

	/**
	 * Product constructor.
	 */
	public function __construct()
	{
		parent::__construct( 'acf/init' );
	}


	/**
	 * @return void
	 */
	public function register()
	{
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( array(
			'key' => 'group_a9e96f1397e23',
			'title' => 'Product Settings',
			'fields' => array(
				array(
					'return_format' => 'array',
					'product' => 'all',
					'min_size' => 0,
					'max_size' => 0,
					'mime_types' => '',
					'key' => 'field_75329cd159ab5',
					'label' => 'Choose File',
					'name' => 'product_file',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_21efca0994a0e',
								'operator' => '==',
								'value' => 'File',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
				),
				array(
					'return_format' => 'array',
					'library' => 'all',
					'min_size' => 0,
					'max_size' => 0,
					'mime_types' => '',
					'key' => 'field_57d03521c20cd',
					'label' => 'Choose Library File',
					'name' => 'library_item_file',
					'type' => 'file',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_57d034f4c20cc',
								'operator' => '==',
								'value' => 'File',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
				),
				array(
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => 'e.g. http://www.vimeo.com/1234567',
					'prepend' => '',
					'append' => '',
					'key' => 'field_57d03541c20ce',
					'label' => 'Video URL',
					'name' => 'library_item_video_url',
					'type' => 'text',
					'instructions' => 'Please enter the full Vimeo URL',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_57d034f4c20cc',
								'operator' => '==',
								'value' => 'Video',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'formatting' => 'html',
				),

				array(
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => 'e.g. http://www.domain.com/file.pdf',
					'prepend' => '',
					'append' => '',
					'key' => 'field_57f404c125d4d',
					'label' => 'Link URL',
					'name' => 'link_url',
					'type' => 'text',
					'instructions' => 'please enter the full URL',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_57d034f4c20cc',
								'operator' => '==',
								'value' => 'Link',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'formatting' => 'html',
				),
				array(
					'allow_multiple' => 0,
					'allow_null' => 0,
					'key' => 'field_57d03594c20cf',
					'label' => 'Select Form',
					'name' => 'library_item_form',
					'type' => 'gravity_forms_field',
					'instructions' => 'S' . 'elect a form from the list. NOTE: You must create the form before it can be selected.',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_57d034f4c20cc',
								'operator' => '==',
								'value' => 'Form',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'multiple' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'product',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(),
			'active' => 1,
			'description' => '',
		)
		);

	}

}

//new Product();