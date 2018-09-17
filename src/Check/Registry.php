<?php

namespace Tvi\MonitorBundle\Check;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tvi\MonitorBundle\Check\Metadata;
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


    /**
     * @param array $tagsMap
     * @param array $checksMap
     */
    public function init(array $tagsMap, array $checksMap)
    {
        //$this->setTagsMap($tagsMap);
        $this->setChecksMap($checksMap);
    }

    /**
     * @param array $tagsMap
     * @param array $checksMap
     */
    protected function setTagsMap(array $tagsMap)
    {
        foreach ($tagsMap as $tag => $tagSetting) {
            $this->addTag(new Tag($tag, $tagSetting['title']));
        }
    }

    /**
     * @param array $checksMap
     */
    protected function setChecksMap($checksMap)
    {

//        v($checksMap);
//        exit;

        foreach ($checksMap as $checkServiceId => $check) {

            //v($check);
            $checkId = $check['id'];

            $checkProxy = new Proxy(function () use ($checkServiceId, $checkId) {
                $this->checks[$checkId] = $this->container->get($checkServiceId);
                return $this->checks[$checkId];
            });

            $this->checks[$checkId] = $checkProxy;

//            foreach ($check['tags'] as $tagName) {
//                $tag = $this->addTag(new Tag($tagName, null));
//                $tag->addCheck($checkId, $this->checks[$checkId]);
//            };
//
//            $group = $this->addGroup(new Group($check['group']));
//            $group->addCheck($checkId, $this->checks[$checkId]);
//            $this->groups[$group->getName()] = $group;

            if(isset($check['items'])) {
                foreach ($check['items'] as $itemCheckServiceId => $itemCheck) {

                    $itemCheckServiceId = sprintf('%s.%s', $checkId, $itemCheckServiceId);

                    $checkProxy = new Proxy(function () use ($checkServiceId, $itemCheckServiceId) {

                        $c = $this->container->get($checkServiceId);
                        $this->checks[$checkServiceId] = $c;

                        foreach ($c->getChecks() as $d=>$cc) {
                            $this->checks[$itemCheckServiceId] = $cc;
                        }

                        return $this->checks[$itemCheckServiceId];
                    });

                    $this->checks[$itemCheckServiceId] = $checkProxy;
                }
            }
        }
    }

    public function test()
    {
        $this['php_version_collection'];
        ///$this['php_version_collection.a'];
        //$this['php_version_collection.b'];
        v($this);
//
//


        //v($this);
        //v($this);
//
//        v($this);
        //v($this);


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

}
