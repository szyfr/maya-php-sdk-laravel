<?php

declare(strict_types=1);

namespace Szyfr\Maya\Laravel\Helpers;

use Szyfr\Maya\Data\RedirectUrlData;

class RedirectUrlHelper
{
    /**
     * Create RedirectUrlData from Laravel config
     */
    public static function fromConfig(): RedirectUrlData
    {
        $config = config('maya.redirect_urls', []);

        return new RedirectUrlData(
            success: $config['success'] ?? '',
            failure: $config['failure'] ?? '',
            cancel: $config['cancel'] ?? ''
        );
    }
}
