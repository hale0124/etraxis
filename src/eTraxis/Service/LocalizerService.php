<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use eTraxis\Collection\Locale;
use eTraxis\Collection\Timezone;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Localizer interface.
 */
class LocalizerService implements LocalizerInterface
{
    protected $token_storage;
    protected $locale;

    /**
     * Dependency Injection constructor.
     *
     * @param   TokenStorageInterface $token_storage
     * @param   string                $locale
     */
    public function __construct(TokenStorageInterface $token_storage, $locale)
    {
        $this->token_storage = $token_storage;
        $this->locale        = $locale;
    }

    /**
     * Returns current locale.
     *
     * @return  string ISO 639-1 language code with ISO 3166-1 alpha-2 country code (e.g. "pt_BR").
     */
    protected function getLocale()
    {
        $locale = $this->locale;

        if ($token = $this->token_storage->getToken()) {

            /** @var \eTraxis\Entity\User $user */
            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                $locale = $user->getLocale();
            }
        }

        return in_array($locale, Locale::getAllKeys()) ? $locale : 'en_US';
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalTimestamp($timestamp)
    {
        $offset = 0;

        if ($token = $this->token_storage->getToken()) {

            /** @var \eTraxis\Entity\User $user */
            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                $timezone = Timezone::getValue($user->getTimezone());
                $offset   = timezone_offset_get(timezone_open($timezone), date_create()) - intval(date('Z'));
            }
        }

        return $timestamp + $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function formatDate($timestamp)
    {
        $format = [
            'bg'    => 'd.n.Y',
            'cs'    => 'j.n.Y',
            'de'    => 'd.m.Y',
            'en_AU' => 'j/m/Y',
            'en_CA' => 'd/m/Y',
            'en_GB' => 'd/m/Y',
            'en_NZ' => 'j/m/Y',
            'en_US' => 'n/j/Y',
            'es'    => 'd/m/Y',
            'fr'    => 'd/m/Y',
            'hu'    => 'Y.m.d',
            'it'    => 'd/m/Y',
            'ja'    => 'Y/m/d',
            'lv'    => 'Y.m.d.',
            'nl'    => 'j-n-Y',
            'pl'    => 'Y-m-d',
            'pt_BR' => 'j/n/Y',
            'ro'    => 'd.m.Y',
            'ru'    => 'd.m.Y',
            'sv'    => 'Y-m-d',
            'tr'    => 'd.m.Y',
        ];

        return date($format[$this->getLocale()], $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function formatTime($timestamp)
    {
        $format = [
            'bg'    => 'H:i',
            'cs'    => 'G:i',
            'de'    => 'H:i',
            'en_AU' => 'g:i A',
            'en_CA' => 'g:i A',
            'en_GB' => 'H:i',
            'en_NZ' => 'g:i a',
            'en_US' => 'g:i A',
            'es'    => 'G:i',
            'fr'    => 'H:i',
            'hu'    => 'G:i',
            'it'    => 'G.i',
            'ja'    => 'G:i',
            'lv'    => 'G:i',
            'nl'    => 'G:i',
            'pl'    => 'H:i',
            'pt_BR' => 'H:i',
            'ro'    => 'H:i',
            'ru'    => 'G:i',
            'sv'    => 'H:i',
            'tr'    => 'H:i',
        ];

        return date($format[$this->getLocale()], $timestamp);
    }
}
