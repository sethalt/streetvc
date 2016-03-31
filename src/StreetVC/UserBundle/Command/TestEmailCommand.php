<?php 
namespace StreetVC\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestEmailCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('streetvc:test-email')
            ->setDescription('Testing email functionality')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
          $container = $this->getApplication()->getKernel()->getContainer();
            $message = \Swift_Message::newInstance()
            ->setFrom('seth.alt@streetvc.com')
            ->setSubject('testing email')
            ->setTo('seth@sweepingdesign.com') 
            ->setBody('TEST WORKED!', 'text/html');
            $container->get('mailer')->send($message);
    }
}