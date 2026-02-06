<?php
/**
 * Plugin Name: Player For SoundCloud
 * Description: Embed your soundCloud tracks with a beautiful Gutenberg block .
 * Version: 1.0.11
 * Author: bPlugins
 * Author URI: http://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: sound-cloud
 */

// ABS PATH
if (!defined('ABSPATH')) {exit;}

if ( function_exists( 'esb_fs' ) ) {
    esb_fs()->set_basename( false, __FILE__ );
} else {

    // Constant
    define( 'SCB_PLUGIN_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.11' );
    define( 'SCB_ASSETS_DIR', plugin_dir_url(__FILE__) . 'assets/' );
    define( 'SCB_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define( 'SCB_DIR_URL', plugin_dir_url(__FILE__));

    if ( !function_exists( 'esb_fs' ) ) {
        // Create a helper function for easy SDK access.
        function esb_fs() {
            global $esb_fs;
            if ( !isset( $esb_fs ) ) {
                // Include Freemius SDK.
                // if ( TTP_IS_PRO ) {
                //     require_once dirname( __FILE__ ) . '/freemius/start.php';
                // } else {
                    require_once dirname( __FILE__ ) . '/freemius-lite/start.php';
                // }
                $scbConfig = array(
                    'id'                  => '20175',
                    'slug'                => 'embed-soundcloud-block',
                    'premium_slug'        => 'embed-soundcloud-block-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_0e9c655b2dcb4460b4f2f8b935720',
                    'is_premium'          => false,
                    'premium_suffix'      => 'Pro',
                    // If your plugin is a serviceware, set this option to false.
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    // Automatically removed in the free version. If you're not using the
                    // auto-generated free version, delete this line before uploading to wp.org.
                    'menu'                => array(
                        'slug'           => 'embed-soundcloud-block',
                        'first-path'     =>  'edit.php?post_type=scb_sound_cloud',
                        'support'        => false,
                    ),
                );
            	$esb_fs = fs_lite_dynamic_init( $scbConfig );
            }
            return $esb_fs;
        }
        // // Init Freemius.
        esb_fs();
        // Signal that SDK was initiated.
        do_action( 'esb_fs_loaded' );
    }

    require_once dirname( __FILE__ ) . '/includes/admin-menu.php';

    class SCB_SoundCloud {

        private static $instance;

        private function __construct() {
            
            $this->load_classes();

            add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
            add_action('init', [$this, 'onInit']);
            add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        }

        public static function get_instance() {
            if(self::$instance) {
                return self::$instance;
            }

            self::$instance = new self();
            return self::$instance;
        }

        public function load_classes() {
            require_once SCB_PLUGIN_PATH . 'includes/post-type/shortcode.php';
            new SCB_SOUNDCLOUD\ShortCode();
        }

        public function enqueueBlockAssets(){
            wp_register_script('soundCloud', SCB_ASSETS_DIR . 'js/soundCloud.api.js', [], SCB_PLUGIN_VERSION);
        }

        // Short code style
        public function admin_enqueue_scripts($hook)
        {
            if ('edit.php' === $hook || 'post.php' === $hook) {
                wp_enqueue_style('scbAdmin', SCB_ASSETS_DIR . 'css/admin.css', [], SCB_PLUGIN_VERSION);
                wp_enqueue_script('scbAdmin', SCB_ASSETS_DIR . 'js/admin.js', ['wp-i18n'], SCB_PLUGIN_VERSION, true);
            }
        }

        public function onInit() {
            register_block_type( __DIR__ . '/build' );
        }
    }
    SCB_SoundCloud::get_instance();
}