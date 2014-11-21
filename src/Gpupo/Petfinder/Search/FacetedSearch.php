<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search;

use Gpupo\Petfinder\Search\Query\QueryInterface;
use Gpupo\Petfinder\Search\Result\CountableCollection;

class FacetedSearch  extends Search implements SearchInterface
{
    public function findByQuery(QueryInterface $query)
    {
        $collection = parent::findByQuery($query);

        foreach ($query->getCountableAttributes() as $attributeName) {
            $results = $collection->next();
            $collection->attributes['countable'][$attributeName] = new CountableCollection($results, $attributeName);
        }
   
        return $collection;
    }
    
}
