<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Core;

use Doctrine\Common\Collections\ArrayCollection;

abstract class CollectionAbstract extends ArrayCollection
{
    protected static $_instance;
    
    /**
     * Permite acesso a instancia dinamica
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $class=get_called_class();
            self::$_instance = new $class();
        }

        return self::$_instance;
    }

    /**
     * Adiciona um elemento no final de um valor array existente
     *
     * @param  string          $key
     * @param  mixed           $value
     * @throws \LogicException
     */
    public function addToArrayValue($key, $value)
    {
        $currentValue = $this->get($key);

        if (is_array($currentValue)) {
            $currentValue[] = $value;
            $this->set($key, $currentValue);
        } else {
            throw new \LogicException("Elemento $key deve ser um array");
        }
    }
}