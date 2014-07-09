<?php

/**************************************************************************/
/*                                                                        */
/*  Copyright (C) 2014 Utix                                               */
/*                                                                        */
/*                                                                        */
/*  Should you receive a copy of this source code, you must check you     */
/*  have a proper, written authorization of Utix to hold it. If you       */
/*  don't have such an authorization, you must DELETE all source code     */
/*  files in your possession, and inform Utix of the fact you obtain      */
/*  these files. Should you not comply to these terms, you can be         */
/*  prosecuted in the extent permitted by applicable law.                 */
/*                                                                        */
/*   contact@utix.fr                                                      */
/*                                                                        */
/**************************************************************************/

if (!defined('_PS_VERSION_'))
    exit;

class Qofipro extends Module
{

    public function __construct()
    {
        $this->name = 'qofipro';
        $this->version = '1.0.0';
        $this->author = 'QOFIPro';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('QOFIPro first module');
        $this->description = $this->l('Test');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('actionOrderStatusPostUpdate'))
            return false;

        Evolubat::install();

        return true;
    }

    public function uninstall()
    {
        Evolubat::uninstall();
        return parent::uninstall();
    }



    /* Call just after a status order is updated
     *
     * params an array:
     *   * id_order: id of the order
     *   * newOrderStatus: the new order status [OrderState Object]
     *
     *
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        /* log $params */
        //error_log(print_r($params, true));

        $id_order = $params['id_order'];
        $order = new Order($id_order);
        $new_status = $params['newOrderStatus'];
        //error_log(print_r($new_status, true));
        Evolubat::createOrder($params['cart']);
        file_put_contents('/tmp/tutu.log', print_r($order->getCartProducts(), true));
        file_put_contents('/tmp/tata.log', print_r($order->getProducts(), true));

        /* TODO: trigger if status need it a creation into evolubat */

    }
}



/*
 *FIXME: comprendre order et cart
 * Que faut-il lier ?
 * Plusieurs livraisons créent plusieurs reference
 * Pour une même référence il y a plusieurs orders
 *
 *
 *
 *
 */

class Evolubat {

    public $ps_id; /* the id of the ps order */

    public $ev_id; /* the id of the evolubat order */


    /*
     *
     *
     * @param Cart   $ps_cart;
     *
     *
     */
    public static function createOrder($ps_cart)
    {
        $products = $ps_cart->getProducts();
        file_put_contents('/tmp/toto.log', print_r($products, true));

    }


    public static function getOrder()
    {
    }


    /* Create tables used to link evolubat and ps orders
     */
    public static function install()
    {
    }


    /* Delete all tables
     */
    public static function uninstall()
    {
    }
}
?>
