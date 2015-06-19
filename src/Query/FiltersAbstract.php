<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Query;

abstract class FiltersAbstract
{
    protected $list = [];

    protected function add(array $array)
    {
        $this->list[$array['key']] = $array;

        return $this;
    }

    protected function append($key, $value)
    {
        $this->list[$key]['values'][] = $value;

        return $this;
    }

    /**
     * Acesso aos valores de um filtro.
     *
     * @param string $key
     *
     * @return array|null
     */
    public function getValues($key)
    {
        if (array_key_exists($key, $this->list)) {
            return $this->list[$key]['values'];
        }
    }

    /**
     * Adiciona um valor a ValuesFilter existente.
     *
     * @param string $key
     * @param type   $value
     *
     * @return bool
     */
    public function appendValueFilter($key, $value)
    {
        if (!empty($value)) {
            if ($this->getValues($key)) {
                return $this->append($key, $value);
            } else {
                return $this->addValuesFilter($key, [$value]);
            }
        } else {
            //do nothing
            return $this;
        }
    }

    /**
     * Filtra por Lista de valores de uma chave.
     *
     * @param string $key    The key to filter on
     * @param array  $values The values to be filtered
     */
    public function addValuesFilter($key, array $values)
    {
        $array = [
            'key'       => $key,
            'values'    => $values,
        ];

        return $this->add($array);
    }

    /**
     * Adiciona um filtro a partir de string no formato 0-10 (inicio - fim).
     *
     * @param string $key
     * @param string $string
     */
    public function addStringRangeFilter($key, $string)
    {
        $array = explode('-', trim($string));

        $min = intval($array[0]);
        if (empty($min)) {
            $min = 1;
        }

        $max = intval($array[1]);
        if (empty($max)) {
            $max = 9999;
        }

        return $this->addRangeFilter($key, $min, $max);
    }

    public function addRangeFilter($key, $min, $max)
    {
        $array = [
            'key'   => $key,
            'min'   => $min,
            'max'   => $max,
        ];

        return $this->add($array);
    }

    public function addGreaterThanFilter($key, $int)
    {
        return $this->addRangeFilter($key, $int, 99999);
    }

    /**
     * Sintaxe de retorno:
     * <code>.
     *
     * array(                                 // Filters only support integer values
     *     array(
     *         'key'    => 'some_search_key', // The key to filter on
     *         'values' => array(30,...),     // The values to be filtered
     *     ),
     *     array(                             // This is a range filter
     *         'key'    => 'some_search_key', // The key to filter on
     *         'min'    => 5,                 // Min and Max value to filter between
     *         'max'    => 105,
     *     ),
     *     ...
     * );
     * </code>
     */
    public function toArray()
    {
        return $this->list;
    }
}
