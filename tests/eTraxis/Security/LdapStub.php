<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapInterface;

class LdapStub implements LdapInterface
{
    public function bind($dn = null, $password = null)
    {
        if ($dn === 'cn=admin,dc=example,dc=com' && $password === 'secret') {
            return;
        }

        if ($dn === 'uid=einstein,dc=example,dc=com' && $password === 'password') {
            return;
        }

        throw new ConnectionException();
    }

    public function query($dn, $query, array $options = [])
    {
        return new QueryStub($query);
    }

    public function getEntryManager()
    {
        return null;
    }

    public function escape($subject, $ignore = '', $flags = 0)
    {
        return $subject;
    }
}
