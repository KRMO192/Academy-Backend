<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
        $start_at = $request->start_at;
        $start_at = Carbon::parse($start_at)->format('d/m/y');
        $end_at = $request->end_at;
        $end_at = Carbon::parse($end_at)->format('d/m/y');
        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'description'=>$this->description,
            'course_image'=>$this->course_image,
            'instructor_id'=>$this->instructor_id,
            'status'=>$this->status,
            'start_at'=>$this->start_at,
            'end_at'=>$this->end_at,
            'created_at'=>$this->created_at->format('d/m/y'),
            'updated_at'=>$this->updated_at->format('d/m/y')
        ];

    }
}
