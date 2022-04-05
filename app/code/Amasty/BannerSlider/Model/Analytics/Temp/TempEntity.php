<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Temp;

class TempEntity
{
    public const MAIN_TABLE = 'amasty_bannerslider_banner_%s_temp';

    public const VIEW_TYPE = 'view';
    public const CLICK_TYPE = 'click';

    public const ID = 'id';
    public const BANNER_ID = 'banner_id';

    public const AGGREGATE_COUNTER = 'counter';
    public const AGGREGATE_VERSION = 'version_id';

    public function getTableName(string $type): string
    {
        return sprintf(self::MAIN_TABLE, $type);
    }
}
