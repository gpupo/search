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

class Keywords extends KeywordsAbstract implements KeywordsInterface
{
    public function toLog()
    {
        return array(
            'data' => $this->toArray(),
        );
    }

}
