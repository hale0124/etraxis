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

namespace eTraxis\Service;

/**
 * Localizer interface.
 */
interface LocalizerInterface
{
    /**
     * Returns specified date formatted for current locale.
     *
     * @param   int $timestamp Epoch timestamp.
     *
     * @return  string Formatted date.
     */
    public function formatDate($timestamp);

    /**
     * Returns specified time formatted for current locale.
     *
     * @param   int $timestamp Epoch timestamp.
     *
     * @return  string Formatted time.
     */
    public function formatTime($timestamp);
}
