<?php

namespace MonitorBundle\Check;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MonitorBundle\Check\Metadata;
use ZendDiagnostics\Check\CheckCollectionInterface;
use ZendDiagnostics\Check\CheckInterface;

class Registry implements \ArrayAccess, \Iterator, \Countable
{
    use ContainerAwareTrait;
    use ArraybleProxyTrait;

    /**
     * @var Group[]
     */
    protected $groups = [];

    /**
     * @var Tag[]
     */
    protected $tags = [];

    public function test()
    {
        //        $r = $this->getTags('tag');
        //        v($r);
        ////        v(current($this->checks));
        ////        (next($this->checks));
        ////        v(current($this->checks));
        //        foreach ($this as $k => $s) {
        //            v($k, $s->check());
        //        }
        //
        //        v($r);
        ////
        ////        foreach ($this as $k => $s) {
        ////            v($k, $s);
        ////        }
        //        //v($this['monitor.check.php_version']);
        ////        v($this['monitor.check.php_extension']);
        ////        $this->checks['monitor.check.php_version'] = $this->checks['monitor.check.php_version']->getInstance();
        ////        $this->checks['monitor.check.php_extension'] = $this->checks['monitor.check.php_extension']->getInstance();
        //        $tag = $this->tags['tag'];
        ////        $tag = $this->groups['tag'];
        ////
        ////        v($tag->getLabel());
        ////        foreach ($tag as $k => $s) {
        ////            //v($k, $s);
        ////        }
        ////
        ////        //$ch['monitor.check.php_extension'] = $ch['monitor.check.php_extension']->getInstance();
        ////        //$this->checks['monitor.check.php_version']+=11;
        ////        v($this->toArray());
        ////        v($this);
    }

    /**
     * @param array $tagsMap
     * @param array $checksMap
     */
    public function setMap(array $tagsMap, array $checksMap)
    {
        foreach ($tagsMap as $tag => $tagSetting) {
            $this->addTag(new Tag($tag, $tagSetting['title']));
        }

        $this->setChecksMap($checksMap);
    }

    /**
     * @param Group $group
     *
     * @return Group
     */
    public function addGroup(Group $group): Group
    {
        return empty($this->group[$group->getName()]) ? $this->group[$group->getName()] = $group : $this->group[$group->getName()];
    }

    /**
     * @param Tag $tag
     *
     * @return Tag
     */
    public function addTag(Tag $tag): Tag
    {
        return empty($this->tags[$tag->getName()]) ? $this->tags[$tag->getName()] = $tag : $this->tags[$tag->getName()];
    }

    /**
     * @param null|string|string[] $groups
     *
     * @return Group[]
     */
    public function getGroups($groups = null): array
    {
        if ($groups) {

            $groups = is_string($groups) ? [$groups] : $groups;

            return array_filter($this->groups, function ($t) use ($groups) {
                return in_array($t->getName(), $groups);
            });
        }

        return $this->groups;
    }

    /**
     * @param null|string|string[] $tags
     *
     * @return Tag[]
     */
    public function getTags($tags = null): array
    {
        if ($tags) {

            $tags = is_string($tags) ? [$tags] : $tags;

            return array_filter($this->tags, function ($t) use ($tags) {
                return in_array($t->getName(), $tags);
            });
        }

        return $this->tags;
    }

    /**
     * @param array $checksMap
     */
    protected function setChecksMap($checksMap)
    {
        foreach ($checksMap as $checkName => $check) {

            $checkProxy = new Proxy(function () use ($checkName) {
                return $this->container->get($checkName);
            });

            $this->checks[$checkName] = $checkProxy;

            foreach ($check['tags'] as $tagName) {
                $tag = $this->addTag(new Tag($tagName, null));
                $tag->addCheck($checkName, $this->checks[$checkName]);
            };

            $group = $this->addGroup(new Group($check['group']));
            $group->addCheck($checkName, $this->checks[$checkName]);
            $this->groups[$group->getName()] = $group;
        }
    }
}
