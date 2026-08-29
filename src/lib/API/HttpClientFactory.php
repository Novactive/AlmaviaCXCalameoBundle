<?php

declare(strict_types=1);

namespace AlmaviaCX\Calameo\API;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

class HttpClientFactory
{
    protected ConfigResolverInterface $configResolver;
    
    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function create(): HttpClient
    {
        $key = $this->configResolver->getParameter('calameo.api.key', 'almaviacx');
        $secret = $this->configResolver->getParameter('calameo.api.secret', 'almaviacx');
        $config = $this->configResolver->getParameter('calameo.config', 'almaviacx');
        
        return new HttpClient($key, $secret, $config);
    }
}
