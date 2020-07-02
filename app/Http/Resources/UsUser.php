<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsUser extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'address' => $this->address,
            'birthdate' => $this->birthdate,
            'genere' => $this->genere,
            'photo' => $this->photo,
            'verify_email' => $this->verify_email,
            'pc_countries_id' => $this->pc_countries_id,
            'token' =>  $this->token
        ];
    }
}
