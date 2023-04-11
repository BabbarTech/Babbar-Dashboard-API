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
class BacklinkResource extends JsonResource
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
            'id' => $this->resource->id,
            'host_id' => $this->resource->host_id,
            'source_url_id' => $this->resource->source_url_id,
            'source_url' => $this->resource->source_url,
            'target_url_id' => $this->resource->target_url_id,
            'target_url' => $this->resource->target_url,
            'induced_strength' => $this->resource->induced_strength,
            'induced_strength_confidence' => $this->resource->induced_strength_confidence,
            'link_type' => $this->resource->link_type,
            'link_text' => $this->resource->link_text,
            'link_rels' => $this->resource->link_rels,
            'language' => $this->resource->language,
            'ip' => $this->resource->ip,
            'source_date' => $this->resource->source_date,
            'page_value' => $this->resource->page_value,
            'page_trust' => $this->resource->page_trust,
            'semantic_value' => $this->resource->semantic_value,
            'babbar_authority_score' => $this->resource->babbar_authority_score,
            'source_nb_keywords_in_top20' => $this->resource->source_nb_keywords_in_top20,
            'source_nb_backlinks' => $this->resource->source_nb_backlinks ?? null,
        ];
    }
}
