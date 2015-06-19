<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Core;

use Doctrine\Common\Collections\ArrayCollection;

abstract class CollectionAbstract extends ArrayCollection
{
    protected static $_instance;

    /**
     * Permite acesso a instancia dinamica.
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $class = get_called_class();
            self::$_instance = new $class();
        }

        return self::$_instance;
    }

    /**
     * Adiciona um elemento no final de um valor array existente.
     *
     * @param string $key
     * @param mixed  $value
     *
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

    /**
     * Magic method that implements.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $command = substr($method, 0, 3);
        $field = $this->__calculateFieldName($method);

        if ($command === 'set') {
            $this->set($field, $args);
        } elseif ($command === 'get') {
            return $this->get($field);
        } elseif ($command === 'add') {
            $this->add($field, $args);
        } else {
            throw new \BadMethodCallException('There is no method '.$method);
        }
    }

    /**
     * Encontra o nome de uma coluna snake_case para um getter.
     *
     * @param string $method
     *
     * @return string
     */
    protected function __calculateFieldName($method)
    {
        $string = substr($method, 3);
        $from_camel_case = function ($str) {
            $str[0] = strtolower($str[0]);
            $func = create_function('$c', 'return "_" . strtolower($c[1]);');

            return preg_replace_callback('/([A-Z])/', $func, $str);
        };

        return $from_camel_case($string);
    }
}
