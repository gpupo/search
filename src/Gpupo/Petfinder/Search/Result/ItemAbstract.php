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
    protected function getAtributo($chave)
    {
        $atributos = $this->getAtributos();

        if (array_key_exists($chave, $atributos)) {
            return trim($atributos[$chave]);
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
}
