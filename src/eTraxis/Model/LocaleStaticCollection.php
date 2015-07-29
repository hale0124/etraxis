<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Model;

/**
 * Static collection of locales.
 */
class LocaleStaticCollection extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            'bg'    => 'Български',
            'cs'    => 'Čeština',
            'de'    => 'Deutsch',
            'en'    => 'English',
            'es'    => 'Español',
            'fr'    => 'Français',
            'hu'    => 'Magyar',
            'it'    => 'Italiano',
            'ja'    => '日本語',
            'lv'    => 'Latviešu',
            'nl'    => 'Nederlands',
            'pl'    => 'Polski',
            'pt_BR' => 'Português',
            'ro'    => 'Română',
            'ru'    => 'Русский',
            'sv'    => 'Svenska',
            'tr'    => 'Türkçe',
        ];
    }
}
