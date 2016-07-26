<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

/**
 * BBCode parser interface.
 */
interface BBCodeInterface
{
    /**
     * Transforms BBCode tags into HTML.
     *
     * @param   string $text   Block of text, which may contain BBCode tags.
     * @param   string $mode   Processing mode (@see eTraxis\Dictionary\BBCodeMode).
     * @param   string $search Current search value.
     *
     * @return  string Resulted block of text with processed BBCode tags.
     */
    public function bbcode(string $text, string $mode, string $search = null): string;
}
