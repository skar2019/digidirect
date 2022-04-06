<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Temp;

class TempEntity
{
    const MAIN_TABLE = 'amasty_bannerslider_banner_%s_temp';

    const VIEW_TYPE = 'view';
    const CLICK_TYPE = 'click';

    const ID = 'id';
    const BANNER_ID = 'banner_id';

    const AGGREGATE_COUNTER = 'counter';
    const AGGREGATE_VERSION = 'version_id';

    public function getTableName(string $type): string
    {
        return sprintf(self::MAIN_TABLE, $type);
    }
}
