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

use Gpupo\Petfinder\Search\Core\CollectionAbstract;
use Gpupo\Petfinder\Search\Query\FiltersInterface;
use Gpupo\Petfinder\Search\Paginator\PaginableInterface;
use Gpupo\Petfinder\Search\Paginator\PaginatorInterface;

abstract class QueryAbstract extends CollectionAbstract implements PaginableInterface
{

    protected static $_instance;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $class=get_called_class();
            self::$_instance = new $class();
        }

        return self::$_instance;
    }

    public function set($key, $value)
    {
        parent::set($key, $value);

        return $this;
    }
    
    /**
     *
     * @param \Gpupo\Petfinder\Search\Query\KeywordsInterface $keywords
     */
    public function __construct(KeywordsInterface $keywords = null)
    {
        $data = array (
            'index'                 => null,
            'filters'               => null,
            'keywords'              => array('primary' => $keywords),
            'fieldWeights'          => array(),
            'limit'                 => 20,
            'offset'                => null,
            'countableAttributes'   => array(),
        );

        parent::__construct($data);
    }

    /**
     * Objeto de pesquisa.
     *
     * @param  \Gpupo\Petfinder\Search\Query\KeywordsInterface $keyword
     * @return type
     */
    public function setKeyword(KeywordsInterface $keyword)
    {
        return $this->set('keywords', array('primary' => $keyword));
    }

    public function setKeywords(array $keywordsList)
    {
        foreach ($keywordsList as $keywords) {
            if (!$keywords instanceof KeywordsInterface) {
                throw new \InvalidArgumentException('Cada item de $keywordsList deve implementar KeywordsInterface');
            }
        }

        return $this->set('keywords', $keywordsList);
    }

    /**
     * Acesso a um conjunto de KeywordsInterface
     *      
     * @todo Evoluir o uso da busca facetada para mais de uma Keyword
     */
    public function getKeywords()
    {
        $keywords =  $this->get('keywords');
        
        return $keywords;
    }

    public function getFilters()
    {
        if ($this->get('filters') instanceof FiltersInterface) {
            return $this->get('filters')->toArray();
        }

        return null;
    }
    
    /**
     * SphinxSearch Queries Array
     * 
     * <code>
     * 
     * //Search single query sintaxe:
     * array(
     *     array(
     *         'key'    => 'search_key',     // The key to search on
     *         'values' => array(            // The values to match with
     *             'value_one',
     *             'value_two',
     *         ),
     *     )
     * );
     *
     * //Search Multi query sintaxe:
     * array(
     *     array(
     *         'key'    => 'search_key',     // The key to search on
     *         'values' => array(            // The values to match with
     *             'value_one',
     *             'value_two',
     *         ),
     *     ),
     *     array(
     *         'key'    => 'another_key',    // The key to search on
     *         'values' => array(            // The values to match with
     *             'some_value',
     *         ),
     *         countableAttributes => array(),  // For Faceted search
     *     ),
     *     ...
     * );
     * 
     * </code>
     * 
     */
    public function getQueries()
    {
        $array = array();
        foreach ($this->getKeywords() as $keyword) {
            $value = $keyword->getData();
            if ($this->getCountableAttributes()) {
                $value['countableAttributes'] = $this->getCountableAttributes();
            }
            $array[] = $value;
        }

        return $array;
    }

    /**
     * 
     * Field weights sintaxe:
     * 
     * <code>
     *
     * array(
     *     'field_one' => 5,
     *     'field_two' => 3,
     *     ...
     * );
     * </code>
     *
     * @todo Implementar Query Field weights
     */
    public function getFieldWeights()
    {
    }

    /**
     * Acesso a quantidade de itens por pagina
     * 
     * @return integer
     */
    public function getLimit()
    {
        if ($this->getPaginator()) {
            return $this->getPaginator()->getItemNumberPerPage();
        } else {
            return $this->get('limit');
        }
    }

    /**
     * Offsets the result list by the number of places set by the count; 
     * 
     * This would be used for pagination through results, where if you have 20
     * results per 'page', the second page would begin at offset 20, the third 
     * page at offset 40, etc.
     * 
     * @return integer
     */
    public function getOffSet()
    {
        if ($this->getPaginator()) {
            return $this->getPaginator()->getOffset();
        } else {
            $offset = $this->get('offset');

            if (!$offset) {
                $offset = 0;
            }

            return $offset;
        }
    }

    /**
     * Mapeamento de index SphinxSearch
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getIndex()
    {
        $index = $this->get('index');

        if (empty($index)) {
            throw new \InvalidArgumentException('index nao informado');
        }

        return $index;
    }

    /**
     * Mapeamento de index SphinxSearch
     *
     * @param  string                    $index
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function setIndex($index)
    {
        if (empty($index)) {
            throw new \InvalidArgumentException('index nao pode ser vazio');
        }

        return $this->set('index', $index);
    }

    public function setFilters(FiltersInterface $filters)
    {
        if (!$filters instanceof FiltersInterface) {
            throw new \Exception('Filtro invalido');
        }

        return $this->set('filters', $filters);
    }

    public function setFieldWeights()
    {

    }

    public function setLimit($limit)
    {
        $validator = new \Zend\Validator\Digits();
        if (!$validator->isValid($limit)) {
            throw new \InvalidArgumentException('Limit deve ser numerico');
        }

        return $this->set('limit', $limit);
    }

    public function setOffSet($offset)
    {
        $validator = new \Zend\Validator\Digits();
        if (!$validator->isValid($offset)) {
            throw new \InvalidArgumentException('Offset deve ser numerico');
        }

        return $this->set('offset', $offset);
    }

    /**
     * 
     * @return \Gpupo\Petfinder\Search\Paginator\PaginatorInterface|boolean
     */
    public function getPaginator()
    {
        $paginator = $this->get('paginator');
        
        if ($paginator instanceof PaginatorInterface) {
            return $paginator;
        } else {
            return false;
        }
    }

    public function setPaginator(PaginatorInterface $paginator)
    {
        return $this->set('paginator', $paginator);
    }
    
    public function getCountableAttributes()
    {
        return $this->get('countableAttributes');
    }
    
    /**
     * Adiciona um atributo para contagem de resultados
     * 
     * Usado na busca facetada
     * 
     * @param  string $attribute
     * @return boolean
     */
    public function addCountableAttribute($attribute)
    {       
        if (empty($attribute)) {
            return false;
        }
        
        if (in_array($attribute, $this->getCountableAttributes())) {
            return false;
        }
        $this->addToArrayValue('countableAttributes', $attribute);
        
        return $this;
    }
    
    /**
     * Adiciona muitos atributos para contagem de resultados
     * 
     * @param array $attributes
     */
    public function setCountableAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->addCountableAttribute($attribute);
        }
        
        return $this;
    }
}
