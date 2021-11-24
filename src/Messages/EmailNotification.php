<?php


namespace App\Messages;


/**
 * Class EmailNotification
 * @package App\Messages
 */
class EmailNotification
{
    /**
     * @var array
     */
    private $options;

    /**
     * EmailNotification constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}