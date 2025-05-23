<?php
/**
 * Plugin Name: Player For SoundCloud
 * Description: Embed your soundCloud tracks with a beautiful Gutenberg block .
 * Version: 1.0.10
 * Author: bPlugins
 * Author URI: http://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: sound-cloud
 */

// ABS PATH
if (!defined('ABSPATH')) {exit;}

if (!function_exists('scb_init')) {
    function scb_init()
    {
        global $scb_bs;
        require_once plugin_dir_path(__FILE__) . 'bplugins_sdk/init.php';
        $scb_bs = new BPlugins_SDK(__FILE__);
    }
    scb_init();
} else {
    global $scb_bs;
    $scb_bs->uninstall_plugin(__FILE__);
}

// SoundCloud Directory
class SCB_SoundCloud {

    private static $instance;

    private function __construct() {
        $this->define_constants();
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


    public function define_constants() {
        // Constant
        define( 'SCB_PLUGIN_VERSION', isset( $_SERVER['HTTP_HOST'] ) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.10' );
        define( 'SCB_ASSETS_DIR', plugin_dir_url(__FILE__) . 'assets/' );
        define( 'SCB_PLUGIN_PATH', plugin_dir_path(__FILE__));

    }

    public function load_classes() {
        require_once SCB_PLUGIN_PATH . 'includes/post-type/shortcode.php';
        new SCB_SOUNDCLOUD\ShortCode();
    }

    public function has_reusable_block($block_name)
    {
        if (get_the_ID()) {
            if (has_block('block', get_the_ID())) {
                // Check reusable blocks
                $content = get_post_field('post_content', get_the_ID());
                $blocks = parse_blocks($content);

                if (!is_array($blocks) || empty($blocks)) {
                    return false;
                }

                foreach ($blocks as $block) {
                    if ($block['blockName'] === 'core/block' && !empty($block['attrs']['ref'])) {
                        if (has_block($block_name, $block['attrs']['ref'])) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function enqueueBlockAssets()
    {
        
        wp_register_script('soundCloud', SCB_ASSETS_DIR . 'js/soundCloud.api.js', [], SCB_PLUGIN_VERSION);

        wp_register_script('scb-sound-cloud-script', plugins_url('dist/script.js', __FILE__), ['react', 'react-dom', 'soundCloud'], SCB_PLUGIN_VERSION);

        wp_register_style('scb-sound-cloud-style', plugins_url('dist/style.css', __FILE__), [], SCB_PLUGIN_VERSION); // Frontend Style

        // if ($this->has_reusable_block('scb/sound-cloud') || has_block('scb/sound-cloud', get_the_ID())) {
            wp_enqueue_style('scb-sound-cloud-style');
            wp_enqueue_script('scb-sound-cloud-script');
        // }
    }

    // Short code style
    public function admin_enqueue_scripts($hook)
    {
        if ('edit.php' === $hook || 'post.php' === $hook) {
            wp_enqueue_style('scbAdmin', SCB_ASSETS_DIR . 'css/admin.css', [], SCB_PLUGIN_VERSION);
            wp_enqueue_script('scbAdmin', SCB_ASSETS_DIR . 'js/admin.js', ['wp-i18n'], SCB_PLUGIN_VERSION, true);
        }
    }

    public function onInit()
    {
        wp_register_style('scb-sound-cloud-editor-style', plugins_url('dist/editor.css', __FILE__), ['wp-edit-blocks', 'scb-sound-cloud-style'], SCB_PLUGIN_VERSION); // Backend Style

        register_block_type(__DIR__, [
            'editor_style' => 'scb-sound-cloud-editor-style',
            'render_callback' => [$this, 'render'],
        ]); // Register Block

        wp_set_script_translations('scb-sound-cloud-editor-script', 'sound-cloud', plugin_dir_path(__FILE__) . 'languages'); // Translate
    }

    public function render($attributes)
    {
        extract($attributes);


        $className = $className ?? '';
        $scbBlockClassName = 'wp-block-scb-sound-cloud ' . $className . ' align' . $align;

        wp_enqueue_style('scb-sound-cloud-style');
        wp_enqueue_script('scb-sound-cloud-script');

        ob_start();?>
		<div class='<?php echo esc_attr($scbBlockClassName); ?>' id='scbSoundCloud-<?php echo esc_attr($cId) ?>' data-attributes='<?php echo esc_attr(wp_json_encode($attributes)); ?>'></div>

		<?php return ob_get_clean();
    } // Render    

}
SCB_SoundCloud::get_instance();
