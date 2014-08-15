<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Query;

abstract class KeywordsAbstract
{
    protected $data = array(
        'key'    => '*',
        'values' => array(),
        'strict' => false,
    );

    public function addKeyword($string)
    {
        if (strlen($string) > 2) {
            $this->data['values'][] = $string;
        }

        return $this;
    }

    public function setKey($key)
    {
        $this->data['key'] = $key;
    }

    public function setStrict($bool)
    {
        $this->data['strict'] = $bool;
    }

    public function setData($key, array $values, $strict = false)
    {
        $array = array(
            'key'       => $key,
            'values'    => $values,
            'strict'    => $strict,
        );

        return $this->data = $array;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getValues()
    {
        return $this->data['values'];
    }

    public function getKey()
    {
        return $this->data['key'];
    }

    /**
     * Recebe a string pesquisada
     *
     * @param string $string
     */
    public function readString($string)
    {
        $string = str_replace(
            array("'",'"', 'buscar', '�'),
            array(' ', ' ', ' ', ' '),
            strtolower(trim($string))
        );

        if (empty($string)) {
            throw new \InvalidArgumentException('Palavra chave nao pode ser vazia');
        }

        if (strlen(preg_replace("/[^A-Za-z0-9?!]/",'',$string)) < 1) {
            throw new \InvalidArgumentException('Palavra chave deve ter mais que 1 caracteres');
        }

        $array = explode(' ', $string);

        foreach ($array as $keyword) {
            $this->addKeyword($keyword);
        }

        return $this;
    }
}
