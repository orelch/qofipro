<?php
/*FIXME header
 *
 */


class Order extends OrderCore {
    /**
     * Get customer orders
     *
     * @param integer $id_customer Customer id
     * @param boolean $showHiddenStatus Display or not hidden order statuses
     * @return array Customer orders
     */
    public static function getCustomerOrders($id_customer, $showHiddenStatus = false, Context $context = null)
    {
        $ps_orders = parent::getCustomerOrders($id_customer, $showHiddenStatus, $context);

        $evolubat_orders = array();
        $cmd = array();
        $cmd["carrier_tax_rate"]= 4;
        $cmd["conversion_rate"]= 4;
        $cmd["current_state"]= 4;
        $cmd["date_add"]= 4;
        $cmd["date_upd"]= 4;
        $cmd["delivery_date"]= 4;
        $cmd["delivery_number"]= 4;
        $cmd["gift"]= 4;
        $cmd["gift_message"]= 4;
        $cmd["id_address_delivery"]= 4;
        $cmd["id_address_invoice"]= 4;
        $cmd["id_carrier"]= 4;
        $cmd["id_cart"]= 4;
        $cmd["id_currency"]= 4;
        $cmd["id_customer"]= 4;
        $cmd["id_lang"]= 4;
        $cmd["id_order"]= -4;
        $cmd["id_order_state"]= 1;
        $cmd["id_shop"]= 4;
        $cmd["id_shop_group"]= 4;
        $cmd["invoice"]= 3;
        $cmd["invoice_date"]= 4;
        $cmd["invoice_number"]= 4;
        $cmd["mobile_theme"]= 4;
        $cmd["module"]= 4;
        $cmd["nb_products"]= 69;
        $cmd["order_state"]= 2;
        $cmd["order_state_color"]= 5;
        $cmd["payment"]= 4;
        $cmd["recyclable"]= 4;
        $cmd["reference"]= "Toto";
        $cmd["secure_key"]= 4;
        $cmd["shipping_number"]= 4;
        $cmd["total_discounts"]= 4;
        $cmd["total_discounts_tax_excl"]= 4;
        $cmd["total_discounts_tax_incl"]= 4;
        $cmd["total_paid"]= 4;
        $cmd["total_paid_real"]= 4;
        $cmd["total_paid_tax_excl"]= 4;
        $cmd["total_paid_tax_incl"]= 4;
        $cmd["total_products"]= 4;
        $cmd["total_products_wt"]= 4;
        $cmd["total_shipping"]= 4;
        $cmd["total_shipping_tax_excl"]= 4;
        $cmd["total_shipping_tax_incl"]= 4;
        $cmd["total_wrapping"]= 4;
        $cmd["total_wrapping_tax_excl"]= 4;
        $cmd["total_wrapping_tax_incl"]= 4;
        $cmd["valid"]= 4;
        $ps_orders[$cmd["reference"]] = $cmd;
        return $ps_orders;
    }
    /**
     * Return a unique reference like : GWJTHMZUN#2
     *-
     * With multishipping, order reference are the same for all orders made with the same cart
     * in this case this method suffix the order reference by a # and the order number
     *-
     * @since 1.5.0.14
     */
    public static function getUniqReferenceOf($id_order)
    {
        if ($id_order < 0) {
           return "Commande en boutique";
        }
        $order = new Order($id_order);
        return $order->getUniqReference();
    }

}


?>
