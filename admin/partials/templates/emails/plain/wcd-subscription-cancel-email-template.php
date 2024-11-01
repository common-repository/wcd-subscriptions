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
	exit;
}
//@credit Inspired by code from the "wpswings" plugin.
echo esc_html( $email_heading ) . "\n\n"; // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
<?php /* translators: %s: subscription ID */ ?>
<p><?php printf( esc_html__( 'A subscription [#%s] has been Cancelled. Their subscription\'s details are as follows:', 'wcd-subscriptions' ), esc_html( $wcd_subs ) ); ?></p>

<?php
$product_name = get_post_meta( $wcd_subs, 'product_name', true );
$product_qty = get_post_meta( $wcd_subs, 'quantity', true );

?>
<table>
	<tr>
		<td><?php esc_html_e( 'Product', 'wcd-subscriptions' ); ?></td>
		<td><?php echo esc_html( $wcd_product_name ); ?> </td>
	</tr>
	<tr>
		<td> <?php esc_html_e( 'Quantity', 'wcd-subscriptions' ); ?> </td>
		<td> <td><?php echo esc_html( $product_qty ); ?> </td> </td>
	</tr>
	<tr>
		<td> <?php esc_html_e( 'Price', 'wcd-subscriptions' ); ?> </td>
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
</table>
<?php
echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped
