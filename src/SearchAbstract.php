<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search;

use Gpupo\Search\Result\CountableCollection;

/**
 * Comunicacao com o SphinxClient.
 */
abstract class SearchAbstract
{
    /**
     * Facade para Single query, obtendo apenas os resultados (matches).
     *
     * @param string $index
     */
    public function search($index, array $filters = null,
        array $queries = null, array $fieldWeights = null,
        $limit = 20, $offset = 0
    ) {
        $result = $this->query($index, $filters, $queries,
            $fieldWeights, $limit, $offset);

        if (!isset($result['matches'])) {
            return [];
        }

        return $result['matches'];
    }

    /**
     * Executa Queries.
     *
     * @see \Gpupo\Search\Query\FiltersAbstract::toArray()          Filter Array Sintaxe
     * @see \Gpupo\Search\Query\QueryAbstract::getQueries()         Query Array Sintaxe
     * @see \Gpupo\Search\Query\QueryAbstract::getFieldWeights()    Query Array Sintaxe
     *
     * @param array $filter       Search filter
     * @param array $queries      Search query
     * @param array $fieldWeights Field weights array
     * @param int   $limit
     * @param int   $offset
     *
     * @return array Results
     */
    public function query($index, array $filters = null,
        array $queries = null, array $fieldWeights = null,
        $limit = 20, $offset = 0
    ) {
        $sphinxClient = $this->getSphinxClient();
        $sphinxClient->SetLimits($offset, $limit);
        if (null !== $filters) {
            foreach ($filters as $filter) {
                if (!isset($filter['key'])) {
                    // Filtro existe mas sem key
                }
                if (
                    array_key_exists('min', $filter) &&
                    array_key_exists('max', $filter)
                ) {
                    $sphinxClient->SetFilterRange(
                                  $filter['key'],
                        (integer) $filter['min'],
                        (integer) $filter['max']
                    );
                } else {
                    if (!isset($filter['values']) || !is_array($filter['values'])) {
                        //Filtro existe mas sem valor;
                    }
                    $sphinxClient->SetFilter(
                        $filter['key'],
                        $filter['values']
                    );
                }
            }
        }
        if (null !== $queries) {
            foreach ($queries as $key => $queryInfo) {
                $query = $this->implodeQueryValues($queryInfo);

                if (array_key_exists('countableAttributes', $queryInfo)) {
                    $array = $queryInfo['countableAttributes'];
                    if (!is_array($array)) {
                        $array = [$array];
                    }

                    $sphinxClient->addFacetedQuery($query, $index, $array);
                } else {
                    $sphinxClient->AddQuery($query, $index);
                }
            }
        }

        if (null !== $fieldWeights) {
            $sphinxClient->SetFieldWeights($fieldWeights);
        }

        $result = $this->getResult($sphinxClient);

        return $result;
    }

    /**
     * RunQueries() + validate.
     *
     * - Single Query: Resultados da Query
     *
     * - Multi Query:  Array de Resultados das Querys
     *
     * Formato de cada Resultado:
     *
     * <code>
     * //Results
     * array(
     *     array(
     *         'id'     => 12345,
     *         'weight' => 30,
     *         'attrs'  => array(...)
     *     ),
     *     array(
     *         'id'     => 23456,
     *         'weight' => 20,
     *         'attrs'  => array(...)
     *     ),
     *     ...
     * );
     * </code>
     *
     * @param \SphinxClient $sphinxClient
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function getResult(\SphinxClient $sphinxClient)
    {
        $result = $sphinxClient->RunQueries();

        if (false === $result) {
            throw new \Exception(
                $sphinxClient->getLastError()
            );
        }
        if ($sphinxClient->GetLastWarning()) {
            throw new \Exception(
                $sphinxClient->GetLastWarning()
            );
        }

        if (false === $result) {
            throw new \Exception(
                $sphinxClient->getLastError()
            );
        }
        if ($sphinxClient->GetLastWarning()) {
            throw new \Exception(
                $sphinxClient->GetLastWarning()
            );
        }

        //Suporte ao formato inicial de unica query
        if (count($result) === 1) {
            return current($result);
        }

        return $result;
    }

    /**
     * Transforma uma query array em uma string usada na
     * query do Client Sphinx Search.
     *
     * @param array $queryInfo
     *
     * @return string
     */
    protected function implodeQueryValues(array $queryInfo)
    {
        $query = "@{$queryInfo['key']} "
            .(
                '*'.implode('* *', $queryInfo['values'])
                .'*'
            ).PHP_EOL;

        return $query;
    }

    /**
     * Facade para query, obtendo resultados em objeto.
     */
    public function getCollection($index, array $filters = null,
        array $queries = null, array $fieldWeights = null,
        $limit = 20, $offset = 0, $countableAttributes = null
    ) {
        $result = $this->query($index, $filters, $queries,
            $fieldWeights, $limit, $offset);

        if (is_array($result)) {
            $i = 0;

            if ($countableAttributes) {
                foreach ($countableAttributes as $attributeName) {
                    $i++;
                    $result[0]['attributes']['countable'][$attributeName] = new CountableCollection($result[$i], $attributeName);
                }
            }

            for ($l = 1; $l <= $i; $l++) {
                unset($result[$l]);
            }

            $collection = $this->factoryCollection($result);

            return $collection;
        }
    }
}
