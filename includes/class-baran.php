 <?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://avaysi.ir
 * @since      1.0.0
 *
 * @package    Baran
 * @subpackage Baran/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Baran
 * @subpackage Baran/includes
 * @author     ابوالفضل ویسی <vaysi.erfan@gmail.com>
 */
class Baran
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Baran_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('BARAN_VERSION')) {
            $this->version = BARAN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'baran';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Baran_Loader. Orchestrates the hooks of the plugin.
     * - Baran_i18n. Defines internationalization functionality.
     * - Baran_Admin. Defines all hooks for the admin area.
     * - Baran_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-baran-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-baran-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-baran-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-baran-public.php';

        $this->loader = new Baran_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Baran_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Baran_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Baran_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Baran_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
        $this->setMenus();
        add_action('admin_init', [$this, 'setSettings']);
        add_action( 'woocommerce_payment_complete', [$this,'paymentEvent'] );
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Baran_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Add Menu To Dashboard Page
     *
     * @return    void
     */
    public function setMenus()
    {
        add_action('admin_menu', [$this, 'addMenu']);
    }

    public function addMenu()
    {
        add_menu_page('حسابداری باران', 'حسابداری باران', 'administrator', 'baran', 'mainTemplate', 'dashicons-desktop');
    }

    public function setSettings()
    {
        $settings = [
            [
                'optionGroup' => 'baran_options',
                'optionName' => 'baranApiKey',
                'callback' => [$this, 'it']
            ],
            [
                'optionGroup' => 'baran_options',
                'optionName' => 'minCount',
                'callback' => [$this, 'it']
            ]
        ];
        $sections = [
            [
                'id' => 'baran_general',
                'title' => 'تنظیمات کلی',
                'callback' => '',
                'page' => 'baran'
            ]
        ];
        $fields = [
            [
                'id' => 'baranApiKey',
                'title' => 'کلید وبسرویس',
                'callback' => [$this, 'inputApi'],
                'section' => 'baran_general',
                'page' => 'baran',
                'args' => [
                    'label_for' => 'baranApiKey',
                ]
            ],
            [
                'id' => 'minCount',
                'title' => 'حداقل موجودی محصولات برای نمایش در وبسایت',
                'callback' => [$this, 'minCountInput'],
                'section' => 'baran_general',
                'page' => 'baran',
                'args' => [
                    'label_for' => 'minCount',
                ]
            ]
        ];
        foreach ($settings as $setting) {
            register_setting($setting['optionGroup'], $setting['optionName'], $this->isDefined($setting['callback']));
        }
        foreach ($sections as $section) {
            add_settings_section($section['id'], $section['title'], $this->isDefined($section['callback']), $section['page']);
        }
        foreach ($fields as $field) {
            add_settings_field($field['id'], $field['title'], $this->isDefined($field['callback']), $field['page'], $field['section'], $this->isDefined($field['args']));
        }
    }

    public function it($var)
    {
        return $var;
    }

    public function inputApi()
    {
        $val = esc_attr(get_option('baranApiKey'));
        echo "<input type='text' class='regular-text' id='baranApiKey' name='baranApiKey' value='$val' style='width: 100%;' placeholder='کلید وبسرویس باران را وارد کنید'>";
    }

    public function minCountInput()
    {
        $val = esc_attr(get_option('minCount'));
        echo "<input type='text' class='regular-text' id='minCount' name='minCount' value='$val' style='width: 100%;' placeholder='فقط عدد وارد کنید'>";
    }


    public function isDefined($var)
    {
        return isset($var) ? $var : '';
    }

    public function paymentEvent($order_id)
    {
        $order = wc_get_order( $order_id );
        $products = $order->get_items();

        $orderedItems = [];
        foreach($products as $id => $prod){
            $product = new WC_Product(intval($id));
            if($product){
                $apiProduct = get_post_meta($product->get_id(),'baranProductId');
                if(isset($apiProduct[0]) && is_numeric($apiProduct[0])){
                    $apiId = intval($apiProduct[0]);
                    $orderedItems[] = (object)[
                        'Code' => $apiId,
                        'Count' => $prod->get_quantity(),
                        'SellPrice' => $product->get_price(),
                        'DiscountPercent' => '0',
                        'Comment' => $product->get_data()
                    ];
                }
            }
        }
        $cli = new BaranApi(get_option('baranApiKey') ?? 'Baran3547' );
        $args = [
            'Date' => date('Y-m-d H:i:s'),
            'CustomerName' => $order->get_user()->first_name . '' . $order->get_user()->last_name,
            'CustomerPhone' => '09161111111',
            'CustomerAddress' => $order->get_address(),
            'DiscountAmount' => $order->get_discount_total(),
            'TaxAmount' => $order->get_total_tax(),
            'DeliveryAmount' => $order->get_shipping_total(),
            'FinalAmount' => $order->get_total(),
            'DeliveryType' => 0,
            'DeliveryDate' => new DateTime(),
            'PayType' => 0,
            'Comment' => $order->get_data(),
            'OrderItems' => $orderedItems
        ];
        $cli->save($args);
    }
}