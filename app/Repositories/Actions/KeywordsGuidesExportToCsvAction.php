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

namespace App\Repositories\Actions;

use App\Models\Project;
use App\Repositories\GuideRepository;

class KeywordsGuidesExportToCsvAction extends ExportToCsvAction
{
    protected GuideRepository $repository;

    protected function exportLine(array $transformedData): void
    {
        $this->getRepository()
            ->getGuideStatsQuery($transformedData['yourtextguru_guide_id'])
            ->orderBy('rank')
            ->get()
            ->map(function ($guideDetail) use ($transformedData) {
                $line = array_merge($transformedData, (array) $guideDetail);

                // Push columns header
                if ($this->delta === 0) {
                    $columns = array_keys($line);
                    fputcsv($this->getFile(), $columns, $this->csvSeparator);
                }

                fputcsv($this->getFile(), $line, $this->csvSeparator);

                $this->delta++;
            });
    }

    protected function getRepository(): GuideRepository
    {
        if (! isset($this->repository)) {
            $this->repository = resolve(GuideRepository::class);
        }

        return $this->repository;
    }

    protected function getFilename(): string
    {
        /** @var Project $project */
        $project = request()->route('project');
        $date = date('Y-m-d');

        return implode('__', [
                $project->hostname,
                'keywords-guides-with-details',
                $date
            ]) . '.csv';
    }
}
