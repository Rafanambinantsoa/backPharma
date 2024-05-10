<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListReservationRess extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "event_name" => $this->event->titre,
            "firstname" => $this->users->firstname,
            "lastname" => $this->users->lastname,
            "email" => $this->users->email,
        ];
    }
}
