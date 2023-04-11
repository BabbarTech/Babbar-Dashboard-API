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
class CommonKeywordCompetitorKeywordResource extends JsonResource
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
            'id' => $this->resource->id,                    // serps.id
            'keyword_id' => $this->resource->keyword_id,                    // keyword_id
            'keywords' => $this->resource->keywords,
            'bks' => $this->resource->bks,
            'current_rank' => $this->resource->current_rank,
            'competitor_rank' => $this->resource->rank,
            'competitor_has_better_rank' => (bool) $this->resource->competitor_has_better_rank,
            'competitor_url' => $this->resource->url,
            'current_url' => $this->resource->current_url,
            //'competitor_host_id' => $this->resource->competitor_host_id,
        ];
    }
}
