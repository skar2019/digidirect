<?php
namespace Ewave\AISales\Model\Import;

use Ewave\AISales\Helper\Db;

abstract class AbstractValidator implements \Zend_Validate_Interface
{
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var Db
     */
    protected $dbHelper;

    /**
     * BillingAddress constructor.
     *
     * @param Db $dbHelper
     */
    public function __construct(
        Db $dbHelper
    ) {
        $this->dbHelper = $dbHelper;
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
