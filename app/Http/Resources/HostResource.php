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

use App\Models\Host;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Host $resource */
class HostResource extends JsonResource
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
            'hostname' => $this->resource->hostname,
            'nb_kw_pos_1_10' => $this->resource->nb_kw_pos_1_10,
            'nb_kw_pos_11_20' => $this->resource->nb_kw_pos_11_20,
            'nb_kw_pos_21plus' => $this->resource->nb_kw_pos_21plus,
        ];
    }
}
