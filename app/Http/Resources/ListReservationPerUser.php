<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListReservationPerUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "eventTilte" => $this->event->titre, 
            "eventDate" => $this->event->date, 
            "eventId" => $this->event->id, 
        ];
    }
}
