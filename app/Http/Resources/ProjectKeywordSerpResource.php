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

use stdClass;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property stdClass $resource */
class ProjectKeywordSerpResource extends JsonResource
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
            'keyword_id' => $this->resource->keyword_id,
            'keywords' => $this->resource->keywords,
            'bks' => $this->resource->bks,
            'rank' => $this->resource->rank,
            'url' => $this->resource->url,
        ];
    }
}
