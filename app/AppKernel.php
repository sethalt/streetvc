<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Finite\Bundle\FiniteBundle\FiniteFiniteBundle;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
//            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Sd\BaseBundle\SdBaseBundle(),
            new Sd\UserBundle\SdUserBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new StreetVC\LenderBundle\StreetVCLenderBundle(),
            new StreetVC\BorrowerBundle\StreetVCBorrowerBundle(),
            new StreetVC\UserBundle\StreetVCUserBundle(),
            new FOS\CommentBundle\FOSCommentBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Application\FOS\CommentBundle\ApplicationFOSCommentBundle(),
            /* media */
//            new Sonata\CoreBundle\SonataCoreBundle(),
//            new Sonata\MediaBundle\SonataMediaBundle(),
            /* sonata */
//            new Sonata\BlockBundle\SonataBlockBundle(),
//            new Sonata\jQueryBundle\SonatajQueryBundle(),
//            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
//            new Sonata\DoctrineMongoDBAdminBundle\SonataDoctrineMongoDBAdminBundle(),
//            new Sonata\AdminBundle\SonataAdminBundle(),
            new StreetVC\LoanBundle\StreetVCLoanBundle(),
            new StreetVC\ActivityBundle\StreetVCActivityBundle(),
            new Spy\TimelineBundle\SpyTimelineBundle(),
            new StreetVC\TransactionBundle\StreetVCTransactionBundle(),
            new StreetVC\BancboxInvest\BancboxInvestBundle\StreetVCBancboxInvestBundle(),
            new FiniteFiniteBundle(),
            new StreetVC\BaseBundle\StreetVCBaseBundle(),
            new StreetVC\LoanEvaluation\Bundle\StreetVCLoanEvaluationBundle(),
            new StreetVC\TranslationBundle\StreetVCTranslationBundle(),
            new StreetVC\SiteBundle\StreetVCSiteBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new ServerGrove\Bundle\TranslationEditorBundle\ServerGroveTranslationEditorBundle();
        }

        return $bundles;
    }

    public function getCacheDir()
    {
        if (in_array($this->environment, array('dev', 'test'))) {
            return '/dev/shm/streetvc/cache/' .  $this->environment;
        }

        return parent::getCacheDir();
    }

    public function getLogDir()
    {
        if (in_array($this->environment, array('dev', 'test'))) {
            return '/dev/shm/streetvc/logs';
        }

        return parent::getLogDir();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
