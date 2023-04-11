<?php
/*
 * Babbar Dashboard API
 *
 * Licensed under the MIT license. See LICENSE file in the project root for details.
 *
 * @copyright Copyright (c) 2023 Babbar
 * @license   https://opensource.org/license/mit/ MIT License
 *
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @property \stdClass $resource */
class KeywordGuideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'guide_id' => $this->resource->id,
            'keywords' => $this->resource->keywords,
            'yourtextguru_guide_id' => $this->resource->yourtextguru_guide_id,
            'status' => $this->resource->status,
            'bks' => $this->resource->bks,
        ];
    }
}
