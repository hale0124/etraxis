<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

use Dictionary\StaticDictionary;

/**
 * BBCode processing mode.
 */
class BBCodeMode extends StaticDictionary
{
    const FALLBACK = self::STRIP;

    const STRIP  = 'strip';     // All tags will be stripped.
    const INLINE = 'inline';    // Only "inline" tags (b, i, u, s, sub, sup, color, url, mail) will be processed, others will be stripped.
    const ALL    = 'all';       // All tags will be processed.

    protected static $dictionary = [
        self::STRIP  => 'bbcode_strip.xsl',
        self::INLINE => 'bbcode_inline.xsl',
        self::ALL    => 'bbcode_all.xsl',
    ];
}
