import { decodeEntities } from '@wordpress/html-entities';

const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { getSetting } = window.wc.wcSettings

const settings = getSetting( 'mercado-pago-assinatura_data', {} );
const label = decodeEntities( settings.title ) || 'Assinatura Mercado Pago';
console.log( settings );

const Content = () => {
	return decodeEntities( settings.description || '' )
}

const Label = ( props ) => {
	const { PaymentMethodLabel } = props.components
	return <PaymentMethodLabel text={ label } />
}

registerPaymentMethod( 
    {
        name: "mercado-pago-assinatura",
        label: <Label />,
        content: < Content />,
        edit: <Content />,
        canMakePayment: () => true,
        ariaLabel: label,
        supports: {
            features: settings.supports,
        },
    }
 );
