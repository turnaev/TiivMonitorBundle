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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckFinder
{
    protected $searchDirs = [__DIR__.'/**'];

    public function __construct(array $dirs = null)
    {
        if($dirs) {
            $this->searchDirs = array_merge($this->searchDirs, $dirs);
        }

        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->traverser = new NodeTraverser;
    }

    /**
     * @return string[]
     */
    public function find()
    {
        $vis = new class extends NodeVisitorAbstract {

            /**
             * @var string|null
             */
            public $namespace;

            /**
             * @var string|null
             */
            public $class;

            /**
             *
             * @param \PhpParser\Node $node
             *
             * @return void
             */
            public function leaveNode(/*! important full name for HHVM*/ \PhpParser\Node $node) {

                if ($node instanceof Namespace_) {
                    $this->namespace = $node->name . '';
                }

                if ($node instanceof Class_) {
                    $this->class = $node->name . '';
                }
            }

            public function clear()
            {
                $this->namespace = $this->class = null;
            }

            public function getClass()
            {
                if($this->namespace && $this->class) {
                    $res = sprintf('%s\%s', $this->namespace, $this->class);
                    return $res;
                }
            }
        };

        $this->traverser->addVisitor($vis);

        $fs = Finder::create();

        $files = $fs->in($this->searchDirs)->name('*.php')->files();

        $res = [];
        foreach ($files as $f) {
            /* @var \SplFileInfo $f */

            $code = $f->getContents();

            $stmts = $this->parser->parse($code);
            $this->traverser->traverse($stmts);

            $class = $vis->getClass();

            if(is_subclass_of($class, CheckConfigInterface::class)) {
                $res[] = $class;
            }

            $vis->clear();
        }

        return $res;
    }
}
