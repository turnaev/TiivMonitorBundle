<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckGeneratorCommand extends ContainerAwareCommand
{
    const TPL_DIR = __DIR__.'/../Resources/generator/Check';

    protected function configure()
    {
        $this
            ->setName('tvi:monitor:generator:check')
            ->setDescription('Generates check plugin from tvi monitor template')
            ->addArgument('name', InputArgument::REQUIRED, 'Check name')
            ->addOption('group', 'g',InputOption::VALUE_OPTIONAL, 'Check group')
            ->addOption('integration-test-src', null,InputOption::VALUE_OPTIONAL, 'Path to integration tests src', null)
            ->addOption('no-backup', 'b', InputOption::VALUE_NONE, 'Do not backup existing check files.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates check classes
from tvi monitor template:

* Create a check:

By default, the unmodified version of check is backed up and saved
To prevent this task from creating the backup file,
pass the <comment>--no-backup</comment> option:
  
  <info>php %command.full_name% "TviMonitorBundle:Check\Example" [--group=...] [--no-backup]</info>
  <info>php %command.full_name% ":Check\Example"</info>
  <info>php %command.full_name% "Check\Example"</info>

EOT
            );
        ;
    }

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var SplFileInfo[]
     */
    private $tpls;

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->twig = $this->getContainer()->get('twig');
        $this->twig->setLoader(new \Twig_Loader_Filesystem([__DIR__.'/../Resources/generator/Check/']));

        $fn = Finder::create();
        $tpls = $fn->in(self::TPL_DIR)->files()->getIterator();
        $this->tpls = iterator_to_array($tpls);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $r = explode(':', $name);
        @list($bundle, $checkPath) = (count($r) == 1) ? [null, current($r)] : $r;

        /* @var $bundle Bundle */
        if(!$bundle) {
            $defaultBundle = 'TviMonitorBundle';
            $bundle = $this->getApplication()->getKernel()->getBundle('TviMonitorBundle');
            $output->writeln(sprintf('<info>Use default bundle <comment>%s</comment></info>', $bundle->getNamespace()));
        } else {
            try {
                $bundle = $this->getApplication()->getKernel()->getBundle($bundle);
            } catch (\InvalidArgumentException $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
        }

        preg_match('#^(.*?)(\w+)$#', $checkPath, $m);
        list($checkNamespace, $checkName) =  [$m[1], $m[2]];

        $checkNamespace = preg_replace('#\\\$#', '', $checkNamespace);
        $bundleNamespace = $bundle->getNamespace();

        $NAMESPACE = sprintf('%s\%s\%s', $bundleNamespace, $checkNamespace, $checkName);
        $NAMESPACE = preg_replace('#\\\$#', '', $NAMESPACE);

        $SERVICE_PREFIX = preg_replace('#\\\#', "_", $bundleNamespace);
        $SERVICE_PREFIX = preg_replace('#bundle$#i', '', $SERVICE_PREFIX);
        $SERVICE_PREFIX = strtolower($SERVICE_PREFIX);

        $CHECK_NAME = $checkName;

        $CHECK_ALIAS = preg_replace('#([A-Z])#', '_\1', $checkName);
        $CHECK_ALIAS = preg_replace('#^_*#', '', $CHECK_ALIAS);
        $CHECK_ALIAS = strtolower($CHECK_ALIAS);

        $group = $input->getOption('group');
        $CHECK_GROUP = $group ? $group: $CHECK_ALIAS;

        //v($NAMESPACE, $SERVICE_PREFIX, $CHECK_ALIAS, $checkName); exit;

        $checkPath = sprintf('%s%s%s', $bundle->getPath(), DIRECTORY_SEPARATOR, $checkPath);
        $checkPath = str_replace('\\', DIRECTORY_SEPARATOR, $checkPath);

        $noBackup = !$input->getOption('no-backup');

        if(is_dir($checkPath)) {

            if($noBackup && is_dir($checkPath)) {
                $output->writeln(sprintf('<info><error>Check %s exist</error>. Use --no-backup flag to rewrite</info>', $NAMESPACE));
                exit(1);
            } else {
                $output->writeln(sprintf('<info>Check <comment>%s</comment> exist rewrite them</info>', $NAMESPACE));
            }
        } else {
            @mkdir($checkPath, 0775, true) && !is_dir($checkPath);
        }




        foreach ($this->tpls as $f) {

            if(in_array($f->getBasename(), ['config.example.yml.twig', 'README.mdpp.twig'])) {
                continue;
            }

            /* @var SplFileInfo $f */
            $fName = $f->getBasename('.twig');

            if($fName == 'Test.php.integration') {

                $testPath = $input->getOption('integration-test-src');
                if(!$testPath) {
                    continue;
                } else {
                    $fName = $f->getBasename('.integration.twig');

                    $testPath = preg_replace('#///*$#', '', $testPath);

                    $fPath = sprintf('%s%s%s%s%s%s%s%s%s',
                        $bundle->getPath(),
                       DIRECTORY_SEPARATOR,
                        $testPath,
                        DIRECTORY_SEPARATOR,
                        'Test',
                        DIRECTORY_SEPARATOR,
                        $checkNamespace,
                        DIRECTORY_SEPARATOR,
                        $checkName
                        );

                    if(is_dir($fPath)) {
                        if($noBackup && is_dir($fPath)) {
                            $output->writeln(sprintf('<info>Tests for <error>check %s exist</error>. Use --no-backup flag to rewrite</info>', $NAMESPACE));
                            continue;
                        } else {
                            $output->writeln(sprintf('<info>Tests for check <comment>%s</comment> exist rewrite them.</info>', $NAMESPACE));
                        }
                    } else {
                        @mkdir($fPath, 0775, true) && !is_dir($fPath);
                    }
                }
                $TEST_NAMESPACE = sprintf('%s\%s\%s\%s', $bundleNamespace,'Test', $checkNamespace, $checkName);

                $tplData = [
                    'NAMESPACE'      => $TEST_NAMESPACE,
                    'SERVICE_REPFIX' => $SERVICE_PREFIX,
                    'CHECK_NAME'    =>  $CHECK_NAME,
                    'CHECK_ALIAS'    => $CHECK_ALIAS,
                    'CHECK_GROUP'    => $CHECK_GROUP,
                ];
                $res = $this->twig->render($f->getRelativePathname(), $tplData);

                $path = sprintf('%s%s%s', $fPath, DIRECTORY_SEPARATOR, $fName);
                file_put_contents($path, $res);
                //'config.example.yml.twig', 'README.mdpp.twig'
                $this->createConf($fPath, $tplData, 'config.example.yml.twig', 'config.yml');
                $this->createConf($fPath, $tplData, 'README.mdpp.twig', 'README.mdpp');

            } else {
                $path = sprintf('%s%s%s', $checkPath, DIRECTORY_SEPARATOR, $fName);

                $tplData = [
                    'NAMESPACE'      => $NAMESPACE,
                    'SERVICE_REPFIX' => $SERVICE_PREFIX,
                    'CHECK_NAME'    =>  $CHECK_NAME,
                    'CHECK_ALIAS'    => $CHECK_ALIAS,
                    'CHECK_GROUP'    => $CHECK_GROUP,
                ];
                $res = $this->twig->render($f->getRelativePathname(), $tplData);

                file_put_contents($path, $res);


                $this->createConf($checkPath, $tplData, 'config.example.yml.twig', 'config.example.yml');
                $this->createConf($checkPath, $tplData, 'README.mdpp.twig', 'README.mdpp');
            }
        }
    }

    private function createConf($basePath, $tplData)
    {
        v($basePath, $tplData);
//        $r = array_filter($tpls, function (SplFileInfo $f) {
//            return $f->getBasename() == 'config.example.yml.twig';
//        });
//
//        $f = current($r);
//        $res = $this->twig->render($f->getRelativePathname(), $tplData);
//
//        $fName = $f->getBasename('.example.yml.twig');
//        $path = sprintf('%s%s%s%s', $basePath, DIRECTORY_SEPARATOR, $fName, '.yml');
//        file_put_contents($path, $res);
    }
}
