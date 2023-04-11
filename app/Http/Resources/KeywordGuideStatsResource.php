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
class KeywordGuideStatsResource extends JsonResource
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
            'url' => $this->resource->url,
            'rank' => $this->resource->rank,
            'page_trust' => $this->resource->page_trust,
            'page_value' => $this->resource->page_value,
            'semantic_value' => $this->resource->semantic_value,
            'babbar_authority_score' => $this->resource->babbar_authority_score,
            'yourtextguru_rank' => $this->resource->yourtextguru_rank,
            'soseo_y' => $this->resource->soseo_y,
            'dseo_y' => $this->resource->dseo_y,
            'soseo_t' => $this->resource->soseo_t,
            'dseo_t' => $this->resource->dseo_t,
        ];
    }
}
