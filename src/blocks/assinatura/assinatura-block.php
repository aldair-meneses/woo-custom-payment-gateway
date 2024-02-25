<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_Gateway_Mercado_Pago_Assinatura_Blocks_Support extends AbstractPaymentMethodType {

    // private $gateway;
    protected $name = 'mercado-pago-assinatura';

    public function initialize() {
        $this->settings = get_option( "woocommerce_{$this->name}_settings", array() );
        // $this->gateway = new WC_Gateway_Mercado_Pago_Assinatura();
    }

    public function is_active() {
		return ! empty( $this->settings[ 'enabled' ] ) && 'yes' === $this->settings[ 'enabled' ];
	}

    public function get_payment_method_script_handles() {

        $asset_path = PLUGIN_BASE_PATH . '/build/assinatura/assinatura.asset.php';
        $version = '0.1';
        $dependencies = array();
        if ( file_exists( $asset_path ) ) {
            $asset     = require $asset_path;
            $version   = is_array( $asset ) && isset( $asset['version'] ) ? $asset['version'] : $version;
            $dependencies = isset( $asset['dependencies'] ) ? $asset['dependencies'] : $dependencies;
        }

        wp_register_script(
            'mercado-pago-assinatura-integration',
            PLUGIN_BASE_URL . '/build/assinatura/assinatura.js',
            $dependencies,
            $version,
            true
        );

        return [ 'mercado-pago-assinatura-integration' ];
    }

    public function get_payment_method_data() {
        return array(
            'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
			'supports'    => $this->get_supported_features(),
        );
    }
}