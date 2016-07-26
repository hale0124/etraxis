<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Twig;

use eTraxis\Dictionary\BBCodeMode;
use eTraxis\Service\BBCodeInterface;

class BBCodeExtension extends \Twig_Extension
{
    protected $bbcode;

    /**
     * Dependency Injection constructor.
     *
     * @param   BBCodeInterface $bbcode
     */
    public function __construct(BBCodeInterface $bbcode)
    {
        $this->bbcode = $bbcode;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bbcode_extension';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $options = [
            'pre_escape' => 'html',
            'is_safe'    => ['html'],
        ];

        return [
            new \Twig_SimpleFilter('bbcode', [$this, 'filterBBCode'], $options),
        ];
    }

    /**
     * Parses input for BBCode.
     *
     * @param   string $text
     * @param   string $mode
     *
     * @return  string
     */
    public function filterBBCode(string $text, string $mode = BBCodeMode::ALL)
    {
        return $this->bbcode->bbcode($text, $mode);
    }
}
