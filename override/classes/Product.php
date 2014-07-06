<?php

class Product extends ProductCore
{

    /**
     * Price calculation / Get product price
     *
     * @param integer $id_shop Shop id
     * @param integer $id_product Product id
     * @param integer $id_product_attribute Product attribute id
     * @param integer $id_country Country id
     * @param integer $id_state State id
     * @param integer $id_currency Currency id
     * @param integer $id_group Group id
     * @param integer $quantity Quantity Required for Specific prices : quantity discount application
     * @param boolean $use_tax with (1) or without (0) tax
     * @param integer $decimals Number of decimals returned
     * @param boolean $only_reduc Returns only the reduction amount
     * @param boolean $use_reduc Set if the returned amount will include reduction
     * @param boolean $with_ecotax insert ecotax in price output.
     * @param variable_reference $specific_price_output
     * 	If a specific price applies regarding the previous parameters, this variable is filled with the corresponding SpecificPrice object
     * @return float Product price
     **/

    public static function priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $zipcode, $id_currency,
        $id_group, $quantity, $use_tax, $decimals, $only_reduc, $use_reduc, $with_ecotax, &$specific_price, $use_group_reduction,
        $id_customer = 0, $use_customer_price = true, $id_cart = 0, $real_quantity = 0)
    {
        $specific_price = SpecificPrice::getSpecificPrice(
            (int)$id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );
        StockAvailable::setQuantity($id_product, $id_product_attribute, 123, $id_shop);
        if ($id_customer > 0 && $use_reduc) {
            return 69;
        }
        if ($id_customer > 0 && $only_reduc) {
            return 10;
        }
        return 100;
    }


}

?>
