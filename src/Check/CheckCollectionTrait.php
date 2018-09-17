<?php

namespace Tvi\MonitorBundle\Check;

trait CheckCollectionTrait
{
    /**
     * @var array
     */
    protected $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @var array
     */
    protected $checks = [];

    /**
     * Explicitly set label.
     *
     * @var string
     */
    protected $label;

    /**
     * {@inheritdoc}
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }
}
