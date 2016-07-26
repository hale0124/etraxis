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

use eTraxis\Dictionary\BBCodeMode;

class BBCodeServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var BBCodeInterface */
    protected $bbcode;

    protected function setUp()
    {
        parent::setUp();

        $this->bbcode = new BBCode\BBCodeService();
    }

    public function testTagB()
    {
        $text = 'This is [b]bold[/b] text.';

        self::assertEquals('This is bold text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <b>bold</b> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <b>bold</b> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagI()
    {
        $text = 'This is [i]italic[/i] text.';

        self::assertEquals('This is italic text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <i>italic</i> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <i>italic</i> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagU()
    {
        $text = 'This is [u]underline[/u] text.';

        self::assertEquals('This is underline text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <u>underline</u> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <u>underline</u> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagS()
    {
        $text = 'This is [s]strike out[/s] text.';

        self::assertEquals('This is strike out text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <s>strike out</s> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <s>strike out</s> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagSub()
    {
        $text = 'This is [sub]subscript[/sub] text.';

        self::assertEquals('This is subscript text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <sub>subscript</sub> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <sub>subscript</sub> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagSup()
    {
        $text = 'This is [sup]superscript[/sup] text.';

        self::assertEquals('This is superscript text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <sup>superscript</sup> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <sup>superscript</sup> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagColor()
    {
        $text = 'This is [color=red]red color[/color] text.';

        self::assertEquals('This is red color text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <span style="color:red">red color</span> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <span style="color:red">red color</span> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagSize()
    {
        $text = 'This is [size=10pt]size[/size] text.';

        self::assertEquals('This is size text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is size text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is size text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagFont()
    {
        $text = 'This is [font=Verdana]font[/font] text.';

        self::assertEquals('This is font text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is font text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is font text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagAlign()
    {
        $text = 'This is [align=left]align[/align] text.';

        self::assertEquals('This is align text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is align text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is align text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagH1()
    {
        $text = 'This is [h1]header level 1[/h1] text.';

        self::assertEquals('This is header level 1 text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is header level 1 text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <h1>header level 1</h1> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagH2()
    {
        $text = 'This is [h2]header level 2[/h2] text.';

        self::assertEquals('This is header level 2 text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is header level 2 text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <h2>header level 2</h2> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagH3()
    {
        $text = 'This is [h3]header level 3[/h3] text.';

        self::assertEquals('This is header level 3 text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is header level 3 text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <h3>header level 3</h3> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagH4()
    {
        $text = 'This is [h4]header level 4[/h4] text.';

        self::assertEquals('This is header level 4 text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is header level 4 text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <h4>header level 4</h4> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagH5()
    {
        $text = 'This is [h5]header level 5[/h5] text.';

        self::assertEquals('This is header level 5 text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is header level 5 text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <h5>header level 5</h5> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagH6()
    {
        $text = 'This is [h6]header level 6[/h6] text.';

        self::assertEquals('This is header level 6 text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is header level 6 text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <h6>header level 6</h6> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagList()
    {
        $text =
            "Week days:\n" .
            "[list]\n" .
            "[li]Monday[/li]\n" .
            "[li]Tuesday[/li]\n" .
            "[li]Wednesday[/li]\n" .
            "[li]Thursday[/li]\n" .
            "[li]Friday[/li]\n" .
            "[li]Saturday[/li]\n" .
            "[li]Sunday[/li]\n" .
            "[/list]\n";

        $stripped =
            "Week days:\n" .
            "Monday\n" .
            "Tuesday\n" .
            "Wednesday\n" .
            "Thursday\n" .
            "Friday\n" .
            "Saturday\n" .
            "Sunday\n";

        $processed =
            "Week days:\n" .
            "<ol><li>Monday</li>\n" .
            "<li>Tuesday</li>\n" .
            "<li>Wednesday</li>\n" .
            "<li>Thursday</li>\n" .
            "<li>Friday</li>\n" .
            "<li>Saturday</li>\n" .
            "<li>Sunday</li></ol>\n";

        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals($processed, $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagUlist()
    {
        $text =
            "Seasons:\n" .
            "[ulist]\n" .
            "[li]Winter[/li]\n" .
            "[li]Spring[/li]\n" .
            "[li]Summer[/li]\n" .
            "[li]Autumn[/li]\n" .
            "[/ulist]\n";

        $stripped =
            "Seasons:\n" .
            "Winter\n" .
            "Spring\n" .
            "Summer\n" .
            "Autumn\n";

        $processed =
            "Seasons:\n" .
            "<ul><li>Winter</li>\n" .
            "<li>Spring</li>\n" .
            "<li>Summer</li>\n" .
            "<li>Autumn</li></ul>\n";

        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals($processed, $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagUrlSimple()
    {
        $text = 'This is [url]http://www.example.com[/url] link.';

        self::assertEquals('This is http://www.example.com link.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <a href="http://www.example.com">http://www.example.com</a> link.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <a href="http://www.example.com">http://www.example.com</a> link.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagUrlExtended()
    {
        $text = 'This is [url=http://www.example.com]example[/url] link.';

        self::assertEquals('This is example link.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <a href="http://www.example.com">example</a> link.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <a href="http://www.example.com">example</a> link.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagMailSimple()
    {
        $text = 'This is [mail]artem@example.com[/mail] email.';

        self::assertEquals('This is artem@example.com email.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <a href="mailto:artem@example.com">artem@example.com</a> email.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <a href="mailto:artem@example.com">artem@example.com</a> email.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagMailExtended()
    {
        $text = 'This is [mail=artem@example.com]Artem\'s[/mail] email.';

        self::assertEquals('This is Artem\'s email.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <a href="mailto:artem@example.com">Artem\'s</a> email.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <a href="mailto:artem@example.com">Artem\'s</a> email.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagQuote()
    {
        $text =
            "The standard Lorem Ipsum passage:\n" .
            "[quote]\n" .
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n" .
            "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n" .
            "[/quote]\n" .
            "used since the 1500s\n";

        $stripped =
            "The standard Lorem Ipsum passage:\n" .
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n" .
            "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n" .
            "used since the 1500s\n";

        $processed =
            "The standard Lorem Ipsum passage:\n" .
            "<blockquote class=\"bbcode\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n" .
            "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</blockquote>\n" .
            "used since the 1500s\n";

        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals($processed, $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testTagCode()
    {
        $text =
            "The standard Lorem Ipsum passage:\n" .
            "[code]\n" .
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n" .
            "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n" .
            "[/code]\n" .
            "used since the 1500s\n";

        $stripped =
            "The standard Lorem Ipsum passage:\n" .
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n" .
            "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n" .
            "used since the 1500s\n";

        $processed =
            "The standard Lorem Ipsum passage:\n" .
            "<pre class=\"bbcode\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n" .
            "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</pre>\n" .
            "used since the 1500s\n";

        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals($processed, $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testNestedTags()
    {
        $text = 'This is [b]bold [i]italic[/i][/b] text.';

        self::assertEquals('This is bold italic text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <b>bold <i>italic</i></b> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <b>bold <i>italic</i></b> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testMissedClosingTag()
    {
        $text = 'This is [b]bold [i]italic[/i] text.';

        self::assertEquals('This is bold italic text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <b>bold <i>italic</i> text.</b>', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <b>bold <i>italic</i> text.</b>', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testClosingTagsWrongOrder()
    {
        $text = 'This is [b]bold [i]italic[/b][/i] text.';

        self::assertEquals('This is bold italic text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals('This is <b>bold <i>italic</i></b> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals('This is <b>bold <i>italic</i></b> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testCodeWithNestedTags()
    {
        $text =
            "Example:\n" .
            "[code]\n" .
            "This is [i]italic[/i] text.\n" .
            "[/code]\n";

        $stripped =
            "Example:\n" .
            "This is italic text.\n";

        $processed =
            "Example:\n" .
            "<pre class=\"bbcode\">This is italic text.</pre>\n";

        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::STRIP));
        self::assertEquals($stripped, $this->bbcode->bbcode($text, BBCodeMode::INLINE));
        self::assertEquals($processed, $this->bbcode->bbcode($text, BBCodeMode::ALL));
    }

    public function testSearch()
    {
        $text = 'This is [b]bold[/b] text.';

        self::assertEquals('This is bold text.', $this->bbcode->bbcode($text, BBCodeMode::STRIP, 'Old'));
        self::assertEquals('This is <b>b<span class="search">old</span></b> text.', $this->bbcode->bbcode($text, BBCodeMode::INLINE, 'Old'));
        self::assertEquals('This is <b>b<span class="search">old</span></b> text.', $this->bbcode->bbcode($text, BBCodeMode::ALL, 'Old'));
    }
}
