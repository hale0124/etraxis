<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security\Authenticator;

use eTraxis\Service\Ldap\LdapInterface;

class LdapServiceStub implements LdapInterface
{
    public function find($basedn, $username, array $attributes = [])
    {
        if ($username !== 'einstein') {
            return false;
        }

        return [
            'cn'   => 'Albert Einstein',
            'mail' => 'einstein@ldap.forumsys.com',
        ];
    }

    public function authenticate($basedn, $username, $password)
    {
        return $username === 'einstein' && $password === 'password';
    }
}
