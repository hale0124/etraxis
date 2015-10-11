<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Static collection of locales.
 */
class Locale extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            'bg'    => 'Bulgarian',
            'cs'    => 'Czech',
            'de'    => 'German',
            'en_AU' => 'English (Australia)',
            'en_CA' => 'English (Canada)',
            'en_NZ' => 'English (New Zealand)',
            'en_GB' => 'English (United Kingdom)',
            'en_US' => 'English (United States)',
            'es'    => 'Spanish',
            'fr'    => 'French',
            'hu'    => 'Hungarian',
            'it'    => 'Italian',
            'ja'    => 'Japanese',
            'lv'    => 'Latvian',
            'nl'    => 'Dutch',
            'pl'    => 'Polish',
            'pt_BR' => 'Portuguese',
            'ro'    => 'Romanian',
            'ru'    => 'Russian',
            'sv'    => 'Swedish',
            'tr'    => 'Turkish',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslatedCollection(TranslatorInterface $translator)
    {
        $collection = static::getCollection();

        array_walk($collection, function (&$value, $key) use ($translator) {
            $value = $translator->trans('lang', [], null, $key);
        });

        return $collection;
    }
}
