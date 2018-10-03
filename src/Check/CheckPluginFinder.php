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

use Symfony\Component\Finder\Finder;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckPluginFinder
{
    protected $searchDirs = [__DIR__.'/**', __DIR__.'/**/**'];

    public function __construct(array $dirs = null)
    {
        if ($dirs) {
            $this->searchDirs = array_merge($this->searchDirs, $dirs);
        }
    }

    /**
     * @return string[]
     */
    public function find()
    {
        $fs = Finder::create();
        $files = $fs->in($this->searchDirs)->name('Plugin.php')->files();

        $res = [];
        foreach ($files as $f) {
            /* @var \SplFileInfo $f */

            $code = $f->getContents();
            $class = $this->getConfigClass($code);
            if (is_subclass_of($class, CheckPluginInterface::class)) {
                $res[] = $class;
            }
        }

        return array_unique($res);
    }

    private function getConfigClass($contents)
    {
        $namespace = [];
        $class = [];

        $tokens = token_get_all($contents);

        do {
            $token = current($tokens);

            if (isset($token[0]) && T_NAMESPACE === $token[0]) {
                next($tokens);
                do {
                    $token = current($tokens);
                    if (';' === $token) {
                        break 1;
                    }
                    $namespace[] = $token[1];
                } while (next($tokens));

                $namespace = trim(implode('', $namespace));
            }

            if (isset($token[0]) && T_CLASS === $token[0]) {
                next($tokens);
                do {
                    $token = current($tokens);

                    if (T_STRING === $token[0]) {
                        $class[] = $token[1];
                        break 1;
                    }
                } while (next($tokens));

                $class = trim(implode('', $class));

                break;
            }
        } while (next($tokens));

        return (string) $namespace.'\\'.(string) $class;
    }
}
