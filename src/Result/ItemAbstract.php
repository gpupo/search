<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Result;

use Gpupo\Common\Entity\CollectionAbstract;

abstract class ItemAbstract extends CollectionAbstract
{
    /**
     * Acesso aos atributos fornecidos pelo SphinxSearch.
     *
     * @return array
     */
    protected function getAtributos()
    {
        return $this->get('attrs');
    }

    /**
     * Acesso a um atributo específico.
     *
     * @param string $key
     */
    protected function getAtributo($key)
    {
        $attrs = $this->get('attrs');

        if (isset($attrs[$key])) {
            return $attrs[$key];
        }

        return;
    }

    protected function toLineString($chave)
    {
        $method = 'get'.ucfirst($chave);

        $string = ' - '.$chave.':'.$this->$method();
        $string .= "\n";

        return $string;
    }

    /**
     * @param string $key
     */
    public function find($key)
    {
        if ($attr = $this->getAtributo($key)) {
            return $attr;
        }

        return $this->get($key);
    }
}
