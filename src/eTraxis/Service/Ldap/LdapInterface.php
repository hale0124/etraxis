<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2005-2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\Ldap;

/**
 * Interaction with LDAP servers.
 */
interface LdapInterface
{
    /**
     * Searches for specified username on LDAP server.
     *
     * @param   string   $basedn     Base DN to search in.
     * @param   string   $username   Login of user to be found.
     * @param   string[] $attributes List of LDAP attributes to return.
     *
     * @return  array|false If user is found then the requested attributes are returned, otherwise FALSE.
     */
    public function find($basedn, $username, array $attributes = []);

    /**
     * Authenticates specified credentials against LDAP server.
     *
     * @param   string $basedn   Base DN to use in binding.
     * @param   string $username Login.
     * @param   string $password Password.
     *
     * @return  bool Whether authenticated successfully.
     */
    public function authenticate($basedn, $username, $password);
}
