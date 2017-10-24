<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Sphinx;

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

include dirname(__FILE__).'/sphinxapi.php';

/**
 * Acesso a API PHP Oficial do Sphinx.
 *
 * @see https://code.google.com/p/sphinxsearch/
 */
class SphinxClient extends \SphinxClient
{
    /**
     * {@inheritdoc }.
     *
     * SPH_MATCH_ALL, matches all query words (<b>default mode</b>);
     * SPH_MATCH_ANY, matches any of the query words;
     * SPH_MATCH_PHRASE, matches query as a phrase, requiring perfect match;
     * SPH_MATCH_BOOLEAN, matches query as a boolean expression;
     * SPH_MATCH_EXTENDED, matches query as an expression in Sphinx internal query language;
     * SPH_MATCH_FULLSCAN, matches query, forcibly using the "full scan" mode as below.
     *
     * @see http://sphinxsearch.com/docs/current.html#matching-modes Matching modes
     */
    public function SetMatchMode($mode)
    {
        return parent::SetMatchMode($mode);
    }

    /**
     * Uso amigavel da definicao de Match Mode.
     *
     * @param string $modeName all|any|phrase|boolean
     */
    public function setMatchModeByModeName($modeName)
    {
        $modes = [
            'all'       => 0,
            'any'       => 1,
            'phrase'    => 2,
            'boolean'   => 3,
            'extended'  => 4,
            'fullscan'  => 5,
        ];

        if (array_key_exists($modeName, $modes)) {
            $mode = $modes[$modeName];
            $this->SetMatchMode($mode);
        } else {
            throw new \LogicException('Wrong Mode');
        }
    }

    /**
     * Define a ordem de resultados.
     *
     * @param string $string
     */
    public function setSortExtended($string)
    {
        $this->SetSortMode(SPH_SORT_EXTENDED, $string);
    }

    /**
     * Agrupa resultados por atributo.
     *
     * @param string $attr
     */
    public function setGroupByAttr($attr)
    {
        $this->SetGroupBy($attr, SPH_GROUPBY_ATTR);
    }

    /**
     * Adiciona multiplas queries a partir de uma query
     * matriz e chaves usadas na busca facetada.
     *
     * Multi-queries are just a mechanism that lets you send several search
     * queries to searchd in one batch. That, in turn, lets searchd internally
     * optimize common parts between the queries. And that’s where the savings
     * come from.
     *
     * @see http://sphinxsearch.com/blog/2010/04/05/facets-multi-queries-and-searching-3x-faster/
     *
     * @param string $query
     * @param string $index
     * @param array  $keys
     */
    public function addFacetedQuery($query, $index, array $keys)
    {
        $this->AddQuery($query, $index);

        //Clear Offset
        $currentOffset = $this->_offset;
        $mode = $this->_sort;
        $sortby = $this->_sortby;
        $limit = $this->_limit;

        $this->_offset = 0;
        $this->_sort = 0;
        $this->_sortby = '';
        $this->SetLimits(0, 999);

        foreach ($keys as $key) {
            $this->setGroupByAttr($key);
            $this->AddQuery($query, $index);
        }

        //Reset
        $this->_offset = $currentOffset;
        $this->_sort = $mode;
        $this->_sortby = $sortby;
        $this->SetLimits($currentOffset, $limit);
    }
}
