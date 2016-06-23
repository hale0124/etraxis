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

use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;

class QueryStub implements QueryInterface
{
    private $dn;

    public function __construct($dn)
    {
        $this->dn = $dn;
    }

    public function execute()
    {
        if ($this->dn === '(uid=einstein)') {

            $entry = new Entry('einstein', [
                'cn'   => ['Albert Einstein'],
                'mail' => ['einstein@ldap.forumsys.com'],
            ]);

            return [$entry];
        }

        if ($this->dn === '(uid=artem)') {

            $entry = new Entry('artem', [
                'cn' => ['Artem Rodygin'],
            ]);

            return [$entry];
        }

        return [];
    }
}
