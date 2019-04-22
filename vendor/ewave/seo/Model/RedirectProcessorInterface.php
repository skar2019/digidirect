<?php

namespace Ewave\SEO\Model;

use Magento\Framework\App\RequestInterface;

/**
 * Interface RedirectProcessorInterface
 *
 * @package Ewave\SEO\Model
 */
interface RedirectProcessorInterface
{
    /**
     * @param RequestInterface $request
     * @param string           $url
     * @param int              $code
     *
     * @return mixed
     */
    public function processRedirect(RequestInterface $request, string $url, int $code);
}
