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

/**
 * {@inheritDoc}. Collection de Contagens na busca Facetada
 */
class CountableCollection extends CollectionAbstract
{
    /**
     * Nome do atributo que recebeu a contagem.
     *
     * @type string
     */
    protected $attributeName;

    public function setAttributeName($name)
    {
        $this->attributeName = $name;
    }
    public function __construct(array $array, $attributeName)
    {
        $this->setAttributeName($attributeName);

        return parent::__construct($array);
    }
    /**
     * Nome do atributo que recebeu a contagem.
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    public function toArray()
    {
        $array = [];

        foreach (parent::toArray() as $item) {
            $name = $item->find($this->getAttributeName());

            if (!empty($name)) {
                $array[] = [
                    'field' => $this->getAttributeName(),
                    'name'  => $name,
                    'value' => $item->find('@count'),
                ];
            }
        }

        return $array;
    }
}
