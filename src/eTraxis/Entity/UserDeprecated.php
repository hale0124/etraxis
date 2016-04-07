<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User's deprecated features.
 *
 * @deprecated 4.1.0
 * @ORM\Embeddable
 */
class UserDeprecated
{
    /**
     * @ORM\Column(name="text_rows", type="integer")
     */
    private $textRows;

    /**
     * @ORM\Column(name="page_rows", type="integer")
     */
    private $pageRows;

    /**
     * @ORM\Column(name="page_bkms", type="integer")
     */
    private $pageBkms;

    /**
     * @ORM\Column(name="auto_refresh", type="integer")
     */
    private $autoRefresh;

    /**
     * @ORM\Column(name="csv_delim", type="integer")
     */
    private $csvDelim;

    /**
     * @ORM\Column(name="csv_encoding", type="integer")
     */
    private $csvEncoding;

    /**
     * @ORM\Column(name="csv_line_ends", type="integer")
     */
    private $csvLineEnds;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->textRows    = 8;
        $this->pageRows    = 20;
        $this->pageBkms    = 10;
        $this->autoRefresh = 0;
        $this->csvDelim    = 44;
        $this->csvEncoding = 1;
        $this->csvLineEnds = 1;
    }
}
