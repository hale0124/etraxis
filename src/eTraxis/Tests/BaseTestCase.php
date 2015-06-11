<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace eTraxis\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Basic test case with access to kernel and database transactions.
 */
class BaseTestCase extends KernelTestCase
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router */
    protected $router;

    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface */
    protected $session;

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface */
    protected $validator;

    /** @var \Symfony\Component\Translation\TranslatorInterface */
    protected $translator;

    /** @var \Doctrine\Common\Persistence\ManagerRegistry */
    protected $doctrine;

    /**
     * Begins new transaction.
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->logger     = static::$kernel->getContainer()->get('logger');
        $this->router     = static::$kernel->getContainer()->get('router');
        $this->session    = static::$kernel->getContainer()->get('session');
        $this->validator  = static::$kernel->getContainer()->get('validator');
        $this->translator = static::$kernel->getContainer()->get('translator');
        $this->doctrine   = static::$kernel->getContainer()->get('doctrine');

        /** @var \Doctrine\ORM\EntityManager $manager */
        $manager = $this->doctrine->getManager();
        $manager->beginTransaction();
    }

    /**
     * Rolls back current transaction.
     */
    protected function tearDown()
    {
        /** @var \Doctrine\ORM\EntityManager $manager */
        $manager = $this->doctrine->getManager();
        $manager->rollback();

        parent::tearDown();
    }
}
