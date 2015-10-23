<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass for Command Bus service.
 */
class CommandBusCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('nih_command_bus')) {
            return;
        }

        $definition = $container->findDefinition('nih_command_bus');

        $services = $container->findTaggedServiceIds('nih_command_handler');

        foreach ($services as $id => $tags) {

            if (isset($tags[0]['command'])) {

                $definition->addMethodCall('addHandler', [
                    $id,
                    $tags[0]['command'],
                ]);
            }
        }
    }
}
