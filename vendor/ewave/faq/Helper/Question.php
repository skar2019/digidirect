<?php
namespace Ewave\Faq\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Question extends AbstractHelper
{
    const POST_VALUE_KEY = 'faq_question';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var null|[]
     */
    protected $postData;

    /**
     * Question constructor.
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(Context $context, DataPersistorInterface $dataPersistor)
    {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getPostValue($key): string
    {
        if (null === $this->postData) {
            $this->postData = (array)$this->dataPersistor->get(self::POST_VALUE_KEY);
            $this->dataPersistor->clear(self::POST_VALUE_KEY);
        }

        if (isset($this->postData[$key])) {
            return (string)$this->postData[$key];
        }

        return '';
    }
}
