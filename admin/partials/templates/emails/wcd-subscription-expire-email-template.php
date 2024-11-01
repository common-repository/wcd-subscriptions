<?php
/**
 * Expired Email template
 *
 * @link       https://webcuddle.com/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_Woocommerce
 * @subpackage Subscriptions_For_Woocommerce/email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @credit Inspired by code from the "wpswings" plugin.
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php /* translators: %s: susbcription ID */ ?>
<p><?php printf( esc_html__( 'A subscription [#%s] has been Expired. Their subscription\'s details are as follows:', 'wcd-subscriptions' ), esc_html( $wcd_subs ) ); ?></p>

<?php
$wcd_text_align = is_rtl() ? 'right' : 'left';

?>
<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $wcd_text_align ); ?>;"><?php esc_html_e( 'Product', 'wcd-subscriptions' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $wcd_text_align ); ?>;"><?php esc_html_e( 'Quantity', 'wcd-subscriptions' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $wcd_text_align ); ?>;"><?php esc_html_e( 'Price', 'wcd-subscriptions' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php
						$product_name = get_post_meta( $wcd_subs, 'product_name', true );
						echo esc_html( $product_name );
					?>
				 </td>
				<td>
					<?php
					$product_qty = get_post_meta( $wcd_subs, 'quantity', true );
					echo esc_html( $product_qty );
					?>
				</td>
				<td>
				<?php
					$period_span_number = get_post_meta( $wcd_subs, 'wcd_woo_sub_product_total_period_number', true );
					$period_span_type = get_post_meta( $wcd_subs, 'wcd_woo_sub_product_total_period_type', true );
					$price = get_post_meta( $wcd_subs, 'recurring_total', true );
					$susbcription = wc_get_order( $wcd_subs );

				if ( isset( $susbcription ) && ! empty( $susbcription ) ) {
					$ord_curr = $susbcription->get_currency();
					$price = $ord_curr . $price;
				}
				if ( $period_span_number > 1 ) {
					$period_span_type = interval_type( $period_span_type );
					/* translators: %s: susbcription interval */
					$price .= '' . sprintf( esc_html( ' / %s %s ' ), $period_span_number, $period_span_type ) . '';
				} else {
					/* translators: %s: susbcription interval */
					$price .= '' . sprintf( esc_html( ' / %s ' ), $period_span_type ) . '';
				}
					echo esc_html( $price );
				?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php

do_action( 'woocommerce_email_footer', $email );
