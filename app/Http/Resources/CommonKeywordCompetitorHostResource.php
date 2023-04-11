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
class CommonKeywordCompetitorHostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $score = ceil($this->resource->score * 100);

        return [
            'id' => $this->resource->host_id,
            'hostname' => $this->resource->hostname,
            'similar_score_percent' => $score,
            'nb_keywords_in_common' => $this->resource->nb_keywords_in_common ?? 0,
            'nb_keywords_in_pos_1_10' => (int) $this->resource->nb_kw_pos_1_10,
            'nb_keywords_in_pos_11_20' => (int) $this->resource->nb_kw_pos_11_20,
            'nb_keywords_in_top20' => (int) $this->resource->nb_kw_top20,
        ];
    }
}
