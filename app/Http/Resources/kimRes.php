<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class kimRes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "firstname" => $this->usera->firstname,
            "lastname" =>  $this->usera->lastname,
            "phone" => $this->usera->phone,
            "email" => $this->usera->email,
            //les data de la pagination
            // "current_page" => $this->currentPage(),
            // "per_page" => $this->perPage(),
            // "total" => $this->total(),
            // "last_page" => $this->lastPage(),
            // "next_page_url" => $this->nextPageUrl(),
            // "prev_page_url" => $this->previousPageUrl(),
            // "from" => $this->firstItem(),
            // "to" => $this->lastItem(),
            // "path" => $this->path(),

        ];
    }
}
