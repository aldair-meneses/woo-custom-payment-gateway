<?php
/**
 * Plugin Name: Mercado Pago - Assinaturas
 * Plugin URI: http://github.com/aldair-meneses/woo-custom-payment-gateway
 * Description: Plugin para receber atualizações de pedidos com base no status de pagamento do link de assinatura.
 * Version: 0.1
 * Author: Aldrick
 * Author URI: https://github.com/ældair-meneses
 * Text Domain: mercado-pago-assinatura
 * Requires at least: 6.3
 * Requires PHP: 7.4
 *
 * @package Mercado pago Assinaturas
 */

define( 'PLUGIN_BASE_URL', plugin_dir_url( __FILE__ ) );
define ( 'PLUGIN_BASE_PATH', plugin_dir_url( __FILE__ ) );

add_filter('woocommerce_payment_gateways', function ($gateways) {
    $gateways[] = 'WC_Gateway_Mercado_Pago_Assinatura';
    return $gateways;
});

add_action('plugins_loaded', function () {
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('admin_notices', function () {
            echo '<div class="error"><p>Necessário que o Plugin WooCommerce esteja instalado para utilização do plugin Mercado Pago Assinaturas.</p></div>';
        });
        return;
    }

    class WC_Gateway_Mercado_Pago_Assinatura extends WC_Payment_Gateway {

        public function __construct()
        {
            $this->id = 'mercado-pago-assinatura';
            $this->has_fields = true;
            $this->method_title = 'Mercado Pago - Assinatura';
            $this->icon = apply_filters('woocommerce_cheque_icon', '');
            $this->method_description = 'Receba atualizações de pedidos com base no status de pagamento do link de assinatura.';
            $this->supports = array(
                'products',
            );

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Habilitar/Desabilitar',
                    'type' => 'checkbox',
                    'label' => 'Habilitar Mercado Pago Assinatura',
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'safe_text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'Assinatura do Mercado Pago',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => 'Descrição',
                    'type' => 'textarea',
                    'description' => 'Descrição do método que vai aparecer na página de checkout',
                    'default' => 'Realize o pagamento através de um link de assinatura do Mercado Pago.',
                    'desc_tip' => true,
                ),
            );
        }

        public function validate_fields()
        {

            if (empty($_POST['billing_first_name'])) {
                wc_add_notice('First name is required!', 'error');
                return false;
            }
            return true;

        }

        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            $redirect_url = 'http://localhost';
            return array(
                'result' => 'success',
                'redirect' => $redirect_url,
            );
        }
    }

    add_action(
        'woocommerce_blocks_loaded',
        function () {
    
            if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
                return;
            }
    
            require_once __DIR__ . '/src/blocks/assinatura/assinatura-block.php';
    
            add_action(
                'woocommerce_blocks_payment_method_type_registration',
                function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
                    $payment_method_registry->register( new WC_Gateway_Mercado_Pago_Assinatura_Blocks_Support );
                }
            );
        }
    );
}
);

add_action( 'before_woocommerce_init', function() {
    if( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                'cart_checkout_blocks',
                __FILE__,
                false
            );
    }
} );

