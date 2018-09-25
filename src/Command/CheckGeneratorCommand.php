<?php

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

class CheckGeneratorCommand extends ContainerAwareCommand
{

    const TPL_DIR = __DIR__.'/../Resources/generator/Check';

    protected function configure()
    {
        $this
            ->setName('tvi:monitor:generator:check')
            ->setDescription('Generates check plugin from tvi monitor template')
            ->addArgument('name', InputArgument::REQUIRED, 'A bundle name, a namespace, or a class name')
            ->addOption('no-backup', null, InputOption::VALUE_NONE, 'Do not backup existing check files.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates check classes
from tvi monitor template:

* Create a check:

By default, the unmodified version of check is backed up and saved
To prevent this task from creating the backup file,
pass the <comment>--no-backup</comment> option:
  
  <info>php %command.full_name% "TviMonitorBundle:Check\Example" --no-backup<</info>

EOT
            );
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        list($bundle, $checkPath) = explode(':', $name);

        /* @var $bundle Bundle */
        $bundle = $this->getApplication()->getKernel()->getBundle($bundle);

        preg_match('#^(.*?)(\w+)$#', $checkPath, $m);
        list($checkNamespace, $checkName) =  [$m[1], $m[2]];

        $checkNamespace = preg_replace('#\\\$#', '', $checkNamespace);
        $bundleNamespace = $bundle->getNamespace();

        $NAMESPACE = sprintf('%s\%s\%s', $bundleNamespace, $checkNamespace, $checkName);
        $NAMESPACE = preg_replace('#\\\$#', '', $NAMESPACE);

        $SERVICE_PREFIX = preg_replace('#\\\#', "_", $bundleNamespace);
        $SERVICE_PREFIX = preg_replace('#bundle$#i', '', $SERVICE_PREFIX);
        $SERVICE_PREFIX = strtolower($SERVICE_PREFIX);

        $CHECK_ALIAS = preg_replace('#([A-Z])#', '_\1', $checkName);
        $CHECK_ALIAS = preg_replace('#^_*#', '', $CHECK_ALIAS);
        $CHECK_ALIAS = strtolower($CHECK_ALIAS);

        //v($NAMESPACE, $SERVICE_PREFIX, $CHECK_ALIAS); exit;

        $checkPath = sprintf('%s%s%s', $bundle->getPath(), DIRECTORY_SEPARATOR, $checkPath);
        $checkPath = str_replace('\\', DIRECTORY_SEPARATOR, $checkPath);

        if(is_dir($checkPath)) {
            $noBackup = !$input->getOption('no-backup');
            if($noBackup && is_dir($checkPath)) {
                $output->writeln(sprintf('<error>check %s exist</error>. Use --no-backup flag to rewrite', $name));
                exit(1);
            }
        } else {
            @mkdir($checkPath, 0775, true) && !is_dir($checkPath);
        }


        $fn = Finder::create();

        $twig = $this->getContainer()->get('twig');
        $twig->setLoader(new \Twig_Loader_Filesystem([__DIR__.'/../Resources/generator/Check/']));

        foreach ($fn->in(self::TPL_DIR)->files() as $f) {
            /* @var SplFileInfo $f */

            $content = $f->getContents();

            $fName = $f->getBasename('.twig');
            $fPath = sprintf('%s%s%s', $checkPath, DIRECTORY_SEPARATOR, $fName);

            $res = $twig->render($f->getRelativePathname(), [
                'NAMESPACE'=>$NAMESPACE,
                'SERVICE_REPFIX'=>$SERVICE_PREFIX,
                'CHECK_ALIAS'=>$CHECK_ALIAS,
            ]);

            file_put_contents($fPath, $res);
        }
    }
}
