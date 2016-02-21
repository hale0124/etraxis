<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to dump all existing routes as JavaScript object.
 */
class RoutesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('etraxis:routes')
            ->setDescription('Dumps all routes as JavaScript object')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Component\Routing\RouterInterface $router */
        $router = $this->getContainer()->get('router');

        /** @var \Symfony\Component\Routing\Route[] $routes */
        $routes = $router->getRouteCollection()->all();

        $output->writeln('var eTraxis = window.eTraxis || {};');
        $output->writeln('');
        $output->writeln('eTraxis.routes = {');

        foreach ($routes as $key => $route) {
            if ($key[0] != '_') {
                $output->writeln(sprintf('    %s: \'%s\',', $key, $route->getPath()));
            }
        }

        $output->writeln('};');
    }
}
