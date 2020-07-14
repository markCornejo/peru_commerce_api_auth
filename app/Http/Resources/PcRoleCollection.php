<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PcRoleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            "data" => parent::toArray($request),
            "links" => [
                "first_page_url" => $this->url(1),
                "last_page_url" => $this->url($this->lastPage()),
                "prev_page_url" => $this->previousPageUrl(),
                "next_page_url" => $this->nextPageUrl(),
                // ""
            ],
            "meta" => [
                "current_page" => $this->currentPage(),
                "from" => $this->firstItem(),
                "to" => $this->lastItem(),
                "last_page" => $this->lastPage(),
                "per_page" => $this->perPage(),
                "total" => $this->total()
            ],
        ];
    }
}
