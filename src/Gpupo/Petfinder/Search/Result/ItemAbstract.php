<?php

namespace Gpupo\Petfinder\Search\Result;

abstract class ItemAbstract extends \Gpupo\Petfinder\Search\Core\CollectionAbstract
{
    /**
     * Acesso aos atributos fornecidos pelo SphinxSearch
     *
     * @return array
     */
    protected function getAtributos()
    {
       return $this->get('attrs');
    }

    /**
     * Acesso a um atributo específico
     *
     * @param string $chave
     */
    protected function getAtributo($key)
    {
        $attrs = $this->get('attrs');

        if (isset($attrs[$key])) {
            return $attrs[$key];
        }

        return null;
    }

    protected function toLineString($chave)
    {
        $method = 'get' . ucfirst($chave);

        $string = " - " . $chave . ":" . $this->$method();
        $string .= "\n";

        return $string;
    }

    /**
     * Magic method that implements
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == "get") {
            return  $this->get($this->__calculateFieldName($method));
        }

        return $this->find($method);
    }

    public function find($key)
    {
        if ($attr = $this->getAtributo($key)) {
            return $attr;
        }

        return $this->get($key);
    }
}
