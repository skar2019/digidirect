<?php
namespace Ewave\ExtendedShippingRates\Model;

use Ewave\ExtendedShippingRates\Api\RateRepositoryInterface;
use Ewave\ExtendedShippingRates\Model\ResourceModel\Rate as RateResource;
use Ewave\ExtendedShippingRates\Model\Carrier\Method\RateFactory;

class RateRepository extends AbstractRepository implements RateRepositoryInterface
{
    /**
     * RateRepository constructor.
     * @param RateResource $resource
     * @param RateFactory $factory
     */
    public function __construct(
        RateResource $resource,
        RateFactory $factory
    ) {
        parent::__construct($resource, $factory);
    }
}
