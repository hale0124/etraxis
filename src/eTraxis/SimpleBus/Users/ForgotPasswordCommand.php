<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use SimpleBus\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Marks password of specified eTraxis account as forgotten.
 *
 * @property    string $username Username of the account.
 * @property    string $ip       IP address of the request.
 */
class ForgotPasswordCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
     * @Assert\Regex(pattern="/^[a-z0-9_\.\-]+$/i", message="user.invalid.username");
     */
    public $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Ip()
     */
    public $ip;
}
