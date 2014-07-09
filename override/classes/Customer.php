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


class Customer extends CustomerCore
{

    public $id_evolubat;

    public function __construct($id = null)
    {
        Customer::$definition['fields']['id_evolubat'] = array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId');
        parent::__construct($id);
    }
}
