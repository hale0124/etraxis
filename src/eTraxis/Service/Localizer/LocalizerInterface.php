<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\Localizer;

/**
 * Localizer interface.
 */
interface LocalizerInterface
{
    /**
     * Returns specified timestamp with offset in seconds between user's timezone and server's one.
     *
     * @param   int $timestamp Epoch timestamp.
     *
     * @return  int Updated timestamp.
     */
    public function getLocalTimestamp(int $timestamp): int;

    /**
     * Returns specified date formatted for current locale.
     *
     * @param   int $timestamp Epoch timestamp.
     *
     * @return  string Formatted date.
     */
    public function formatDate(int $timestamp): string;

    /**
     * Returns specified time formatted for current locale.
     *
     * @param   int $timestamp Epoch timestamp.
     *
     * @return  string Formatted time.
     */
    public function formatTime(int $timestamp): string;
}
