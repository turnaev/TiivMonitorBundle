<?php

/**
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
class CheckManager implements \ArrayAccess, \Iterator, \Countable
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
     * @param ?string|string[] $tags
     * @param ?string|string[] $alias
     * @param ?string|string[] $groups
     *
     * @return Tag[]
     */
    public function findChecks($alias = null, $groups = null, $tags = null): array
    {
        $alias = (array) (null === $alias ? [] : (\is_string($alias) ? [$alias] : $alias));
        $groups = (array) (null === $groups ? [] : (\is_string($groups) ? [$groups] : $groups));
        $tags = (array) (null === $tags ? [] : (\is_string($tags) ? [$tags] : $tags));

        $check = array_filter($this->toArray(), static function (CheckInterface $c) use ($alias, $groups, $tags) {
            $inAlias = ($alias) ? \in_array($c->getId(), $alias, true) : true;
            $inGroups = ($groups) ? \in_array($c->getGroup(), $groups, true) : true;
            $inTags = ($tags) ? (bool) array_intersect($c->getTags(), $tags) : true;

            return $inAlias && $inGroups && $inTags;
        });

        return $check;
    }

    /**
     * @param ?string|string[] $groups
     *
     * @return Group[]
     */
    public function findGroups($groups = null): array
    {
        if ($groups) {
            $groups = \is_string($groups) ? [$groups] : $groups;

            return array_filter($this->groups, static function ($t) use ($groups) {
                return \in_array($t->getName(), $groups, true);
            });
        }

        return $this->groups;
    }

    /**
     * @param ?string|string[] $tags
     *
     * @return Group[]
     */
    public function findTags($tags = null): array
    {
        if ($tags) {
            $tags = \is_string($tags) ? [$tags] : $tags;

            return array_filter($this->tags, static function ($t) use ($tags) {
                return \in_array($t->getName(), $tags, true);
            });
        }

        return $this->tags;
    }

    public function add(array $checkConfs, array $tagConfs, array $groupConfs)
    {
        $this->addTags($tagConfs);
        $this->addGroups($groupConfs);
        $this->addChecks($checkConfs);
    }

    public function addIfTag(Tag $tag): Tag
    {
        return empty($this->tags[$tag->getName()]) ? $this->tags[$tag->getName()] = $tag : $this->tags[$tag->getName()];
    }

    public function addIfGroup(Group $group): Group
    {
        return empty($this->groups[$group->getName()]) ? $this->groups[$group->getName()] = $group : $this->groups[$group->getName()];
    }

    public function addIfCheck(CheckInterface $check)
    {
        $this->checks[$check->getId()] = $check;

        foreach ($check->getTags() as $tag) {
            $tag = $this->addIfTag(new Tag($tag));
            $tag->addCheck($check->getId(), $this->checks[$check->getId()]);
        }

        $group = $this->addIfGroup(new Group($check->getGroup()));
        $group->addCheck($check->getId(), $this->checks[$check->getId()]);

        $this->groups[$group->getId()] = $group;
    }

    protected function addTags(array $tagConfs)
    {
        foreach ($tagConfs as $id => $setting) {
            $tag = new Tag($id, !empty($setting['name']) ? $setting['name'] : null, !empty($setting['descr']) ? $setting['descr'] : null);
            $this->addIfTag($tag);
        }
    }

    protected function addGroups(array $gropConfs)
    {
        foreach ($gropConfs as $id => $setting) {
            $group = new Group($id, !empty($setting['name']) ? $setting['name'] : null, !empty($setting['descr']) ? $setting['descr'] : null);
            $this->addIfGroup($group);
        }
    }

    /**
     * @param array $checkConfs
     */
    protected function addChecks($checkConfs)
    {
        foreach ($checkConfs as $id => $setting) {
            $serviceId = $setting['serviceId'];
            $checkProxy = new Proxy(function () use ($serviceId, $id) {
                $this->checks[$id] = $this->container->get($serviceId);

                return $this->checks[$id];
            });

            $this->checks[$id] = $checkProxy;

            foreach ($setting['tags'] as $tagId) {
                $tag = $this->addIfTag(new Tag($tagId));
                $tag->addCheck($id, $this->checks[$id]);
            }

            $group = $this->addIfGroup(new Group($setting['group']));
            $group->addCheck($id, $this->checks[$id]);

            $this->groups[$group->getId()] = $group;
        }
    }
}
