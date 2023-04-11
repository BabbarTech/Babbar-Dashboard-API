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

namespace App\Enums;

use App\Repositories\Actions\BabbarFetchHostKeywordsAction;
use App\Repositories\Actions\ExportToCsvAction;
use App\Repositories\Actions\KeywordsGuidesExportToCsvAction;
use App\Repositories\Actions\YtgFetchGuideAction;

enum ActionEnum: string
{
    case BABBAR_FETCH_HOST_KEYWORDS = 'babbar_fetch_host_keywords';
    case YTG_FETCH_GUIDE = 'ytg_fetch_guide';
    case EXPORT_TO_CSV = 'export_to_csv';
    case KEYWORDS_GUIDES_EXPORT_TO_CSV = 'keywords_guides_export_to_csv';

    public function handler(): string
    {
        return match ($this) {
            self::BABBAR_FETCH_HOST_KEYWORDS => BabbarFetchHostKeywordsAction::class,
            self::YTG_FETCH_GUIDE => YtgFetchGuideAction::class,
            self::EXPORT_TO_CSV => ExportToCsvAction::class,
            self::KEYWORDS_GUIDES_EXPORT_TO_CSV => KeywordsGuidesExportToCsvAction::class,
        };
    }
}
