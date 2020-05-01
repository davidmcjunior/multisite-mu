<?php

namespace Site\Custom\Posts\Acf;

use Site\Custom\Custom_Type;

/**
 * Class Library_Item
 * @package Site\Custom\Post
 */
class Library_Item extends Custom_Type
{

	/**
	 * Library_Item constructor.
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
			'key' => 'group_58ab55d90a063',
			'title' => 'Library Item Settings',
			'fields' => array(
				array(
					'layout' => 'horizontal',
					'choices' => array(
						'File' => 'File',
						'Video' => 'Video',
						'Form' => 'Form',
						'Link' => 'Link',
						'Other' => 'Other',
					),
					'default_value' => '',
					'other_choice' => 0,
					'save_other_choice' => 0,
					'allow_null' => 0,
					'return_format' => 'value',
					'key' => 'field_57d034f4c20cc',
					'label' => 'What type of resource is this?',
					'name' => 'resource_type',
					'type' => 'radio',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
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
					'label' => 'Choose File',
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
					'key' => 'field_5d516f5496a73',
					'label' => 'Include in MedEd Video Library?',
					'name' => 'site_product_include_in_library',
					'type' => 'true_false',
					'instructions' => '',
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
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
				),
				array(
					'key' => 'field_5d516f3e96a72',
					'label' => 'Related Operative Technique',
					'name' => 'site_product_related_documents',
					'type' => 'post_object',
					'instructions' => 'Start typing to choose related documents (You may select multiple documents)',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field' => 'field_57d034f4c20cc',
								'operator' => '==',
								'value' => 'Video',
							),
							array(
								'field' => 'field_5d516f5496a73',
								'operator' => '==',
								'value' => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array(
						0 => 'product',
					),
					'taxonomy' => '',
					'allow_null' => 0,
					'multiple' => 1,
					'return_format' => 'object',
					'ui' => 1,
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
					'instructions' => 'Select a form from the list. NOTE: You must create the form before it can be selected.',
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
				array(
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => 'Lit. #',
					'append' => '',
					'key' => 'field_57d035d7c20d0',
					'label' => 'Literature #',
					'name' => 'library_item_literature_number',
					'type' => 'text',
					'instructions' => 'e.g. 733-12-456 or 0254H',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'formatting' => 'html',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'item',
					),
				),
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'product'
					)
				)
			),
			'menu_order' => 0,
			'position' => 'acf_after_title',
			'style' => 'seamless',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(),
			'active' => 1,
			'description' => '',
		));

	}

}

new Library_Item();