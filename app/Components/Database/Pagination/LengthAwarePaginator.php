<?php
/**
 *
 * User: sergei
 * Date: 21.02.19
 * Time: 13:46
 */

namespace App\Components\Database\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as BaseLengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class LengthAwarePaginator extends BaseLengthAwarePaginator
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'items' => $this->items->toArray(),
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'total' => $this->total(),
            'pages' => ceil($this->total()/$this->perPage())
        ];
    }

//    public function load($key)
//    {
//        /**
//         * @var $item Model
//         */
//        foreach($this->items() as $item) {
//            print_r($item->getKey());exit;
//        }
//        print_R($this->items());exit;
//    }
}