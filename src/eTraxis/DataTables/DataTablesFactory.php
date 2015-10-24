<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\DataTables;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * DataTables factory.
 */
class DataTablesFactory implements DataTablesFactoryInterface
{
    protected $container;
    protected $logger;
    protected $validator;

    /** @var array List of registered DataTable services. */
    protected $services = [];

    /**
     * Dependency Injection constructor.
     *
     * @param   ContainerInterface $container
     * @param   LoggerInterface    $logger
     * @param   ValidatorInterface $validator
     */
    public function __construct(
        ContainerInterface $container,
        LoggerInterface    $logger,
        ValidatorInterface $validator)
    {
        $this->container = $container;
        $this->logger    = $logger;
        $this->validator = $validator;
    }

    /**
     * Registers specified DataTable service.
     *
     * @param   string $id     Service ID of the DataTable services.
     * @param   string $entity Entity name which the DataTable service handles.
     */
    public function addService($id, $entity)
    {
        $this->services[$entity] = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $entity)
    {
        $this->logger->debug('Handle DataTable', [$entity]);

        $query = new DataTableQuery();

        $query->draw    = $request->get('draw');
        $query->start   = $request->get('start');
        $query->length  = $request->get('length');
        $query->search  = $request->get('search');
        $query->order   = $request->get('order');
        $query->columns = $request->get('columns');

        $violations = $this->validator->validate($query);

        if (count($violations)) {
            $message = $violations->get(0)->getMessage();
            $this->logger->error($message);
            throw new DataTableException($message);
        }

        if (!array_key_exists($entity, $this->services)) {
            $message = 'Unknown entity to process with DataTable service.';
            $this->logger->error($message);
            throw new DataTableException($message);
        }

        /** @var DataTableInterface $handler */
        $handler = $this->container->get($this->services[$entity]);

        if (!$handler instanceof DataTableInterface) {
            $message = 'DataTable service must implement "DataTableInterface" interface.';
            $this->logger->error($message);
            throw new DataTableException($message);
        }

        $result = null;

        list($msec, $sec) = explode(' ', microtime());
        $timer_started    = (float) $msec + (float) $sec;

        try {
            $result = $handler->handle($query);
        }
        catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new DataTableException($e->getMessage());
        }
        finally {
            list($msec, $sec) = explode(' ', microtime());
            $timer_stopped    = (float) $msec + (float) $sec;

            $this->logger->debug('DataTable processing time', [$timer_stopped - $timer_started]);
        }

        $violations = $this->validator->validate($result);

        if (count($violations)) {
            $message = $violations->get(0)->getMessage();
            $this->logger->error($message);
            throw new DataTableException($message);
        }

        return [
            'draw'            => $query->draw,
            'recordsTotal'    => $result->recordsTotal,
            'recordsFiltered' => $result->recordsFiltered,
            'data'            => $result->data,
        ];
    }
}
