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
        $this->author = 'Utix';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('QOFIPro first module');
        $this->description = $this->l('Test');
    }

/*{{{ Install and Uninstall*/

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('actionOrderStatusPostUpdate'))
        {
            return false;
        }

        Evolubat::install();

        return true;
    }

    public function uninstall()
    {
        Evolubat::uninstall();
        return parent::uninstall();
    }

/*}}}*/


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
        Evolubat::createOrder($params['cart'], $new_status);
        file_put_contents('/tmp/tutu.log', print_r($order->getCartProducts(), true));
        file_put_contents('/tmp/tata.log', print_r($order->getProducts(), true));

        /* TODO: trigger if status need it a creation into evolubat */

    }

/*{{{ Configuration */

    /*
     * build configuration form
     *
     */
    public function displayForm()
    {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Evolubat Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Webservices URL'),
                    'name' => 'EVOLUBAT_URL',
                    'size' => 80,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Webservices User'),
                    'name' => 'EVOLUBAT_USER',
                    'size' => 20,
                    'required' => false
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Webservices Password'),
                    'name' => 'EVOLUBAT_PWD',
                    'size' => 20,
                    'required' => false
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
                'back' => array(
                    'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Back to list')
                )
            );

        // Load current value
        $helper->fields_value['EVOLUBAT_URL'] = Configuration::get('EVOLUBAT_URL');
        $helper->fields_value['EVOLUBAT_USER'] = Configuration::get('EVOLUBAT_USER');
        $helper->fields_value['EVOLUBAT_PWD'] = Configuration::get('EVOLUBAT_PWD');

        return $helper->generateForm($fields_form);
    }


    /*
     * function to trigger configuration page on backoffice
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name))
        {
            $error = false;
            $url = strval(Tools::getValue('EVOLUBAT_URL'));
            if (!$url || empty($url) || !Validate::isUrl($url)) {
                $output .= $this->displayError( $this->l('Invalid Url') );
                $error = true;
            } else {
                Configuration::updateValue('EVOLUBAT_URL', $url);
            }
            /* TODO: add some check
             */
            $user = strval(Tools::getValue('EVOLUBAT_USER'));
            Configuration::updateValue('EVOLUBAT_USER', $user);
            $pwd = strval(Tools::getValue('EVOLUBAT_PWD'));
            Configuration::updateValue('EVOLUBAT_PWD', $pwd);
            if (!$error) {
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

/*}}}*/

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


    private static function getCh($path)
    {
        $user = Configuration::getValue('EVOLUBAT_USER');
        $pwd  = Configuration::get('EVOLUBAT_PWD');
        $_ch = curl_init(); // create curl resource

        curl_setopt($_ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($user) || !empty($pwd)) {
            curl_setopt($_ch, CURLOPT_USERPWD, "$user:$pwd");
        }
        curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($_ch, CURLOPT_URL, Configuration::getValue('EVOLUBAT_URL')."/$url");
        return $_ch;
    }
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

    /*
     *
     * @param int     $customer_id;
     * @param int     $product_id;
     *
     *
     * @return int    Product quantity
     *
     */
    public static function getQuantity($customer_id, $product_id)
    {
        $ch = Evolubat::getCh("Stock?piIdU=1&piIdAD=$product_id");
        $data = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200)
            return -1;
        curl_close($ch);
        $output = json_decode($data);
        if (!isset($output['phDSStocks'])
            return -1;
        return $output['phDSStocks'];
    }

    /*
     *
     * @param int     $customer_id;
     * @param int     $product_id;
     *
     *
     * @return float    Product price
     *
     */
    public static function getPrice($customer_id, $product_id)
    {
        $ch = Evolubat::getCh("Prix?piIdU=1&piIdAD=$product_id&piIdCli=$customer_id");
        $data = curl_exec($ch);
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200)
            return -1;
        curl_close($ch);
        $output = json_decode($data);
        if (!isset($output['pdPrix'])
            return -1;
        return $output['pdPrix'];

    }

    /*{{{ Install an Uninstall*/

    /*
     * * add a column to add evolubat id into product
     *
     * *TOOD manage order and devis
     *
     */
    public static function install()
    {
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `id_evolubat` INT NOT NULL');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'customer` ADD `id_evolubat` INT NOT NULL');

        return true;
    }


    /* Delete all tables and inserted columns
     */
    public static function uninstall()
    {
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product DROP `id_evolubat`');
        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'customer DROP `id_evolubat`');
        Configuration::deleteByName('EVOLUBAT_URL');

        return true;
    }

    /*}}}*/

}
?>
