<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Search\Result;

use Gpupo\Search\Search\Core\CollectionAbstract as CoreCollectionAbstract;
use Gpupo\Search\Search\Paginator\PaginatorInterface;

/**
 * Conjunto de Itens de resultado.
 */
abstract class CollectionAbstract  extends CoreCollectionAbstract
{
    /**
     * Lista de documentos encontrados.
     */
    public function getMatches()
    {
        return $this->get('matches');
    }

    /**
     * Quantidade de documentos encontrados para a query, independente da paginação.
     */
    public function getTotal()
    {
        return intval($this->get('total'));
    }

    /**
     * @see getTotal()
     */
    public function getTotalFound()
    {
        return intval($this->get('total_found'));
    }

    /**
     * Tempo necessário para a pesquisa.
     */
    public function getTime()
    {
        return $this->get('time');
    }

    /**
     * Detalhes dos resultados para cada palavra.
     */
    public function getWords()
    {
        return $this->get('words');
    }

    /**
     * Detalhes dos resultados para cada palavra.
     */
    public function getSummary()
    {
        $string = "---\n";
        foreach ($this->getWords() as $k => $v) {
            $string .= '['.$k.']: Docs:'.$v['docs']
                .' | Hits:'.$v['hits']."\n";
        }

        return $string;
    }

    public function getPaginator()
    {
        return $this->get('paginator');
    }

    public function setPaginator(PaginatorInterface $paginator)
    {
        return $this->set('paginator', $paginator);
    }

    public function paginate($limit, $offset = 0)
    {
    }

    /**
     * @return ItemInterface
     */
    public function factoryItem(array $array)
    {
        return new Item($array);
    }

    public function __construct(array $array = null)
    {
        return $this->load($array);
    }

    /**
     * @param array $array Lista de resultados
     */
    public function load(array $array = null)
    {
        if ($array) {
            $list = [];

            if (array_key_exists('matches', $array)) {
                $matches = $array['matches'];
                unset($array['matches']);
            } elseif (array_key_exists('matches', $array[0])) {
                $matches = $array[0]['matches'];
                unset($array[0]['matches']);
                $array = $array[0];
            } else {
                return parent::__construct($array);
            }

            foreach ($matches as $result) {
                $list[] = $this->factoryItem($result);
            }

            $array['itens'] = $list;

            return parent::__construct($array);
        }
    }

    public function __toString()
    {
        $string = '';

        foreach ($this->toArray() as $item) {
            $string .= (string) $item;
        }

        return $string;
    }

    public function toArray()
    {
        return $this->get('itens');
    }

    /**
     * First item found.
     *
     * @return Item
     */
    public function getFirst()
    {
        return current($this->toArray());
    }
}
