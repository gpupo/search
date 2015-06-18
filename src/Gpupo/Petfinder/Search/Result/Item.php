<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Result;

class Item extends ItemAbstract implements ItemInterface
{
    public function getId()
    {
        return $this->get('id');
    }

    public function __toString()
    {
        $string = json_encode($this->toArray());

        return $string;
    }
}
