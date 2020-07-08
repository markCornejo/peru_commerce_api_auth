<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ManagerMaster extends JsonResource
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
        $array = [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "pc_users_roles" => []
        ];

        foreach($this->pc_users_roles as $key => $value) {
            $users_roles = [
                "id" => $value->id,
                "name" => $value->name,
                "description" => $value->description,
            ];

            array_push($array["pc_users_roles"], $users_roles);
        }

        return $array;
    }
}
