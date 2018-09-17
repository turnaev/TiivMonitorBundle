<?php

namespace Tvi\MonitorBundle\Check\PhpVersion;

use ZendDiagnostics\Check\CheckCollectionInterface;
use Tvi\MonitorBundle\Check\CheckCollectionTrait;
use Tvi\MonitorBundle\Check\CheckTrait;

class CheckCollection implements CheckCollectionInterface
{
    use CheckTrait {
        setAdditionParams as protected traitcalc;
    }
    use CheckCollectionTrait;

    public function init()
    {

        foreach ($this->items as $id => $conf) {

            list($expectedVersion, $operator) = [$conf['expectedVersion'], $conf['operator']];

            $check = new Check($expectedVersion, $operator);
            $check->setLabel(sprintf('PHP version "%s" "%s"', $expectedVersion, $operator));

//            $check->setId(sprintf('%s.%s', $this->id, $id));
//            $check->setGroup($this->group);
            $check->setTags($this->tags);

            $this->checks[$id] = $check;
        }
        unset($this->items);
    }

    public function setAdditionParams(array $data)
    {
        $this->traitcalc($data);
        $this->init();
    }

}
