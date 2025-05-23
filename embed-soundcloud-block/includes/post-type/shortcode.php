<?php
namespace SCB_SOUNDCLOUD;
if (!defined('ABSPATH')) {exit;}

class ShortCode {
    public $post_type = 'scb_sound_cloud';
    public function __construct(){
		add_action( 'init', [$this, 'onInit'], 20 );
		add_shortcode( 'scb-sound-cloud', [$this, 'onAddShortcode'], 20 );
		add_filter( 'manage_scb_sound_cloud_posts_columns', [$this, 'manageLPBPostsColumns'], 10 );
		add_action( 'manage_scb_sound_cloud_posts_custom_column', [$this, 'manageBSBPostsCustomColumns'], 10, 2 );
		add_action( 'use_block_editor_for_post', [$this, 'useBlockEditorForPost'], 999, 2 );	
	}

	function onInit(){
		$menuIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="#fff" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M639.8 298.6c-1.3 23.1-11.5 44.8-28.4 60.5s-39.2 24.4-62.3 24.1h-218c-4.8 0-9.4-2-12.8-5.4s-5.3-8-5.3-12.8V130.2c-.2-4 .9-8 3.1-11.4s5.3-6.1 9-7.7c0 0 20.1-13.9 62.3-13.9c25.8 0 51.1 6.9 73.3 20.1c17.3 10.2 32.3 23.8 44.1 40.1s20 34.8 24.2 54.4c7.5-2.1 15.3-3.2 23.1-3.2c11.7-.1 23.3 2.2 34.2 6.7S606.8 226.6 615 235s14.6 18.3 18.9 29.3s6.3 22.6 5.9 34.3zm-354-153.5c.1-1 0-2-.3-2.9s-.8-1.8-1.5-2.6s-1.5-1.3-2.4-1.7s-1.9-.6-2.9-.6s-2 .2-2.9 .6s-1.7 1-2.4 1.7s-1.2 1.6-1.5 2.6s-.4 1.9-.3 2.9c-6 78.9-10.6 152.9 0 231.6c.2 1.7 1 3.3 2.3 4.5s3 1.8 4.7 1.8s3.4-.6 4.7-1.8s2.1-2.8 2.3-4.5c11.3-79.4 6.6-152 0-231.6zm-44 27.3c-.2-1.8-1.1-3.5-2.4-4.7s-3.1-1.9-5-1.9s-3.6 .7-5 1.9s-2.2 2.9-2.4 4.7c-7.9 67.9-7.9 136.5 0 204.4c.3 1.8 1.2 3.4 2.5 4.5s3.1 1.8 4.8 1.8s3.5-.6 4.8-1.8s2.2-2.8 2.5-4.5c8.8-67.8 8.8-136.5 .1-204.4zm-44.3-6.9c-.2-1.8-1-3.4-2.3-4.6s-3-1.8-4.8-1.8s-3.5 .7-4.8 1.8s-2.1 2.8-2.3 4.6c-6.7 72-10.2 139.3 0 211.1c0 1.9 .7 3.7 2.1 5s3.1 2.1 5 2.1s3.7-.7 5-2.1s2.1-3.1 2.1-5c10.5-72.8 7.3-138.2 .1-211.1zm-44 20.6c0-1.9-.8-3.8-2.1-5.2s-3.2-2.1-5.2-2.1s-3.8 .8-5.2 2.1s-2.1 3.2-2.1 5.2c-8.1 63.3-8.1 127.5 0 190.8c.2 1.8 1 3.4 2.4 4.6s3.1 1.9 4.8 1.9s3.5-.7 4.8-1.9s2.2-2.8 2.4-4.6c8.8-63.3 8.9-127.5 .3-190.8zM109 233.7c0-1.9-.8-3.8-2.1-5.1s-3.2-2.1-5.1-2.1s-3.8 .8-5.1 2.1s-2.1 3.2-2.1 5.1c-10.5 49.2-5.5 93.9 .4 143.6c.3 1.6 1.1 3.1 2.3 4.2s2.8 1.7 4.5 1.7s3.2-.6 4.5-1.7s2.1-2.5 2.3-4.2c6.6-50.4 11.6-94.1 .4-143.6zm-44.1-7.5c-.2-1.8-1.1-3.5-2.4-4.8s-3.2-1.9-5-1.9s-3.6 .7-5 1.9s-2.2 2.9-2.4 4.8c-9.3 50.2-6.2 94.4 .3 144.5c.7 7.6 13.6 7.5 14.4 0c7.2-50.9 10.5-93.8 .3-144.5zM20.3 250.8c-.2-1.8-1.1-3.5-2.4-4.8s-3.2-1.9-5-1.9s-3.6 .7-5 1.9s-2.3 2.9-2.4 4.8c-8.5 33.7-5.9 61.6 .6 95.4c.2 1.7 1 3.3 2.3 4.4s2.9 1.8 4.7 1.8s3.4-.6 4.7-1.8s2.1-2.7 2.3-4.4c7.5-34.5 11.2-61.8 .4-95.4z"/></svg>';

		register_post_type( $this->post_type, array(
			'labels'				=> array(
				'name'			=> __( 'SoundCloud', 'sound-cloud' ),
				'singular_name'	=> __( 'Shortcode', 'sound-cloud' ),
				'add_new'		=> __( 'Add New', 'sound-cloud' ),
				'add_new_item'	=> __( 'Add New', 'sound-cloud' ),
				'edit_item'		=> __( 'Edit', 'sound-cloud' ),
				'new_item'		=> __( 'New', 'sound-cloud' ),
				'view_item'		=> __( 'View', 'sound-cloud' ),
				'search_items'	=> __( 'Search', 'sound-cloud'),
				'not_found'		=> __( 'Sorry, we couldn\'t find the that you are looking for.', 'sound-cloud' ),
                'item_updated'          => 'SoundCloud Update',
                'item_published'        => 'SoundCloud Publish'
            ),
			'public'				=> false,
			'show_ui'				=> true,	
			'show_in_rest'			=> true,					
			'publicly_queryable'	=> false,
			'exclude_from_search'	=> true,
			'menu_position'			=> 14,
			'menu_icon'				=> 'data:image/svg+xml;base64,' . base64_encode( $menuIcon ),		
			'has_archive'			=> false,
			'hierarchical'			=> false,
			'capability_type'		=> 'page',
			'supports'				=> [ 'title', 'editor' ],
			'template'				=> [ ['scb/sound-cloud'] ],
			'template_lock'			=> 'all',
		)); // Register Post Type
	}

	public function onAddShortcode( $atts ) {
        $post_id = $atts['id'];
        $post = get_post( $post_id );
        if ( !$post ) {
            return '';
        }
        if ( post_password_required( $post ) ) {
            return get_the_password_form( $post );
        }
        switch ( $post->post_status ) {
            case 'publish':
                return $this->displayContent( $post );
            case 'private':
                if (current_user_can('read_private_posts')) {
                    return $this->displayContent( $post );
                }
                return '';
            case 'draft':
            case 'pending':
            case 'future':
                if ( current_user_can( 'edit_post', $post_id ) ) {
                    return $this->displayContent( $post );
                }
                return '';
            default:
                return '';
        }
    }

    public function displayContent( $post ){
        $blocks = parse_blocks( $post->post_content );
        return render_block( $blocks[0] );
    }

	function manageLPBPostsColumns( $defaults ) {
		unset( $defaults['date'] );
		$defaults['shortcode'] = 'ShortCode';
		$defaults['date'] = 'Date';
		return $defaults;
	}
    

	function manageBSBPostsCustomColumns( $column_name, $post_ID ) {
		if ( $column_name == 'shortcode' ) {
			echo "<div class='scbFrontShortcode' id='scbFrontShortcode-$post_ID'>
				<input value='[scb-sound-cloud id=$post_ID]' onclick='scb_handle_shortcode( $post_ID )'>
				<span class='tooltip'>Copy To Clipboard</span>
			</div>";
		}
	}

	function useBlockEditorForPost($use, $post){
		if ($this->post_type === $post->post_type) {
			return true;
		}
		return $use;
	}
}