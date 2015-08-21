<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Command;

use eTraxis\Tests\BaseTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TranslationsCommandTest extends BaseTestCase
{
    public function testCommand()
    {
        $expected = <<<OUT
Updating messages.bg.yml
Updating messages.cs.yml
Updating messages.de.yml
Updating messages.en.yml
Updating messages.es.yml
Updating messages.fr.yml
Updating messages.hu.yml
Updating messages.it.yml
Updating messages.ja.yml
Updating messages.lv.yml
Updating messages.nl.yml
Updating messages.pl.yml
Updating messages.pt_BR.yml
Updating messages.ro.yml
Updating messages.ru.yml
Updating messages.sv.yml
Updating messages.tr.yml

OUT;

        $application = new Application(self::$kernel);
        $application->add(new TranslationsCommand());

        $commandTester = new CommandTester($application->find('etraxis:translations'));
        $commandTester->execute([]);

        $this->assertEquals($expected, $commandTester->getDisplay());
    }
}
