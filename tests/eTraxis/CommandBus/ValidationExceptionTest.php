<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus;

class ValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMessages()
    {
        $errors = [
            'error1' => 'Message #1',
            'error2' => 'Message #2',
            'error3' => 'Message #3',
        ];

        $exception = new ValidationException($errors);

        $this->assertTrue(is_array($exception->getMessages()));
        $this->assertEquals($errors, $exception->getMessages());
    }
}
