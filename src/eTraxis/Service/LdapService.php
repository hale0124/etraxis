<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2005-2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use Psr\Log\LoggerInterface;

/**
 * Interaction with LDAP servers.
 */
class LdapService implements LdapInterface
{
    protected $logger;

    /** @var bool|resource */
    protected $link = false;

    /**
     * Connects to specified LDAP server.
     *
     * @param   LoggerInterface $logger   Debug logger.
     * @param   string          $host     LDAP server hostname.
     * @param   int             $port     LDAP server port (389 by default).
     * @param   string          $user     Username to bind to LDAP server to make queries there.
     * @param   string          $password Password to bind to LDAP server to make queries there.
     * @param   bool            $tls      Whether to use TLS.
     *
     * @throws  LdapException
     */
    public function __construct(LoggerInterface $logger, $host, $port = null, $user = null, $password = null, $tls = false)
    {
        $this->logger = $logger;

        if (!extension_loaded('ldap')) {
            $this->logger->error('LDAP extension is not loaded.');
            throw new LdapException('LDAP extension is not loaded.');
        }

        if ($host === null) {
            $this->logger->info('LDAP connection is not configured.');

            return;
        }

        try {

            $this->link = ldap_connect($host, $port ?: 389);

            if ($this->link === false) {
                $this->logger->error('LDAP server cannot be connected.');
                throw new LdapException('LDAP server cannot be connected.');
            }

            if (!ldap_set_option($this->link, LDAP_OPT_PROTOCOL_VERSION, 3)) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(LDAP_OPT_PROTOCOL_VERSION) Error {$errno}", [ldap_err2str($errno)]);
                throw new LdapException(ldap_err2str($errno), $errno);
            }

            if (!ldap_set_option($this->link, LDAP_OPT_REFERRALS, 0)) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(LDAP_OPT_REFERRALS) Error {$errno}", [ldap_err2str($errno)]);
                throw new LdapException(ldap_err2str($errno), $errno);
            }

            if ($tls && !ldap_start_tls($this->link)) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(ldap_start_tls) Error {$errno}", [ldap_err2str($errno)]);
                throw new LdapException(ldap_err2str($errno), $errno);
            }

            if (!ldap_bind($this->link, $user, $password)) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(ldap_bind) Error {$errno}", [ldap_err2str($errno)]);
                throw new LdapException(ldap_err2str($errno), $errno);
            }
        }
        catch (\Exception $e) {
            throw new LdapException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Disconnects from LDAP server.
     */
    public function __destruct()
    {
        if ($this->link !== false) {
            ldap_close($this->link);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($basedn, $username, $attributes = [])
    {
        if ($this->link === false) {
            $this->logger->info('LDAP connection is not configured.');

            return false;
        }

        try {

            $result = ldap_search($this->link, $basedn, "(uid={$username})", $attributes);

            if (!$result) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(ldap_search) Error {$errno}", [ldap_err2str($errno)]);

                return false;
            }

            $entries = ldap_get_entries($this->link, $result);

            if ($entries === false) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(ldap_get_entries) Error {$errno}", [ldap_err2str($errno)]);

                return false;
            }

            if (count($entries) == 0 || $entries['count'] == 0) {
                $this->logger->error('No entries are found.');

                return false;
            }

            $entry = [];

            foreach ($attributes as $attribute) {

                if (empty($entries[0][$attribute][0])) {
                    $this->logger->error('Attribute is empty.', [$attribute]);

                    return false;
                }
                else {
                    $entry[$attribute] = $entries[0][$attribute][0];
                }
            }

            if (count($entry) != count($attributes)) {
                return false;
            }

            return $entry;
        }
        catch (\Exception $e) {

            $this->logger->error('(find) Exception', [$e->getCode(), $e->getMessage()]);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($basedn, $username, $password)
    {
        if ($this->link === false) {
            $this->logger->info('LDAP connection is not configured.');

            return false;
        }

        try {

            if (!ldap_bind($this->link, "UID={$username},{$basedn}", $password)) {
                $errno = ldap_errno($this->link);
                $this->logger->error("(authenticate) Error {$errno}", [ldap_err2str($errno)]);

                return false;
            }
        }
        catch (\Exception $e) {

            $this->logger->error('(authenticate) Exception', [$e->getCode(), $e->getMessage()]);

            return false;
        }

        return true;
    }
}
