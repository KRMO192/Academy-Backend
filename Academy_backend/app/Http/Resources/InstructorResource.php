<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'brith_date'=>$this->brith_date,
            'instructor_image'=>$this->instructor_image,
            'cv'=>$this->cv,
            'address'=>$this->address,
            'bio'=>$this->bio,
            'phone'=>$this->phone,
            'created_at'=>$this->created_at->format('d/m/y'),
            'updated_at'=>$this->updated_at->format('d/m/y')
        ];
    }
}
