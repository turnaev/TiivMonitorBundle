<?php

/*
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Manager implements \ArrayAccess, \Iterator, \Countable
{
    use ContainerAwareTrait;
    use CheckArraybleTrait;

    /**
     * @var Group[]
     */
    protected $groups = [];

    /**
     * @var Group[]
     */
    protected $tags = [];

    /**
     * @param array $tagsMap
     * @param array $checkServiceMap
     */
    public function init(array $tagsMap, array $checkServiceMap)
    {
        $this->setTagsMap($tagsMap);
        $this->setCheckServiceMap($checkServiceMap);
    }

    /**
     * @param Group $group
     *
     * @return Group
     */
    public function addGroup(Group $group): Group
    {
        return empty($this->groups[$group->getName()]) ? $this->groups[$group->getName()] = $group : $this->groups[$group->getName()];
    }

    /**
     * @param null|string|string[] $groups
     *
     * @return Group[]
     */
    public function getGroups($groups = null): array
    {
        if ($groups) {
            $groups = \is_string($groups) ? [$groups] : $groups;

            return array_filter($this->groups, function ($t) use ($groups) {
                return \in_array($t->getName(), $groups);
            });
        }

        return $this->groups;
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
     * @param null|string|string[] $tags
     *
     * @return Tag[]
     */
    public function getTags($tags = null): array
    {
        if ($tags) {
            $tags = \is_string($tags) ? [$tags] : $tags;

            return array_filter($this->tags, function ($t) use ($tags) {
                return \in_array($t->getName(), $tags);
            });
        }

        return $this->tags;
    }

    /**
     * @param array $tagsMap
     * @param array $checksMap
     */
    protected function setTagsMap(array $tagsMap)
    {
        foreach ($tagsMap as $tag => $tagSetting) {
            $this->addTag(new Tag($tag));
        }
    }

    /**
     * @param array $checkServiceMap
     */
    protected function setCheckServiceMap($checkServiceMap)
    {
        foreach ($checkServiceMap as $checkId => $check) {
            $checkServiceId = $check['serviceId'];
            $checkProxy = new Proxy(function () use ($checkServiceId, $checkId) {
                $this->checks[$checkId] = $this->container->get($checkServiceId);

                return $this->checks[$checkId];
            });

            $this->checks[$checkId] = $checkProxy;

            foreach ($check['tags'] as $tagName) {
                $tag = $this->addTag(new Tag($tagName));
                $tag->addCheck($checkId, $this->checks[$checkId]);
            }

            $group = $this->addGroup(new Group($check['group']));
            $group->addCheck($checkId, $this->checks[$checkId]);
            $this->groups[$group->getName()] = $group;
        }

        foreach ($this->tags as $id => $tag) {
            if (!\count($tag)) {
                unset($this->tags[$id]);
            }
        }
    }
}
