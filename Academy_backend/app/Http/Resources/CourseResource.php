<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'title'=>$this->title,
            'description'=>$this->description,
            'course_image'=>$this->course_image,
            'instructor_id'=>$this->instructor_id,
            'status'=>$this->status,
            'start_at'=>$this->start_at->format('d/m/y'),
            'end_at'=>$this->end_at->format('d/m/y'),
            'created_at'=>$this->created_at->format('d/m/y'),
            'updated_at'=>$this->updated_at->format('d/m/y')
        ];

    }
}
