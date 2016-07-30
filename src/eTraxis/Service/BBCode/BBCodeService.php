<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2007-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\BBCode;

use eTraxis\Dictionary\BBCodeMode;
use eTraxis\Service\BBCodeInterface;

/**
 * BBCode parser service.
 */
class BBCodeService implements BBCodeInterface
{
    /** @var array BBCode tags (opening => closing). */
    protected static $bbcode_tags = [
        '!(\[b\])!isu'            => '!(\[/b\])!isu',
        '!(\[i\])!isu'            => '!(\[/i\])!isu',
        '!(\[u\])!isu'            => '!(\[/u\])!isu',
        '!(\[s\])!isu'            => '!(\[/s\])!isu',
        '!(\[sub\])!isu'          => '!(\[/sub\])!isu',
        '!(\[sup\])!isu'          => '!(\[/sup\])!isu',
        '!(\[color\=(.*?)\])!isu' => '!(\[/color\])!isu',
        '!(\[size\=(.*?)\])!isu'  => '!(\[/size\])!isu',
        '!(\[font\=(.*?)\])!isu'  => '!(\[/font\])!isu',
        '!(\[align\=(.*?)\])!isu' => '!(\[/align\])!isu',
        '!(\[h1\])!isu'           => '!(\[/h1\])!isu',
        '!(\[h2\])!isu'           => '!(\[/h2\])!isu',
        '!(\[h3\])!isu'           => '!(\[/h3\])!isu',
        '!(\[h4\])!isu'           => '!(\[/h4\])!isu',
        '!(\[h5\])!isu'           => '!(\[/h5\])!isu',
        '!(\[h6\])!isu'           => '!(\[/h6\])!isu',
        '!(\[list\])!isu'         => '!(\[/list\])!isu',
        '!(\[ulist\])!isu'        => '!(\[/ulist\])!isu',
        '!(\[li\])!isu'           => '!(\[/li\])!isu',
        '!(\[url\])!isu'          => '!(\[/url\])!isu',
        '!(\[url\=(.*?)\])!isu'   => '!(\[/url\])!isu',
        '!(\[mail\])!isu'         => '!(\[/mail\])!isu',
        '!(\[mail\=(.*?)\])!isu'  => '!(\[/mail\])!isu',
        '!(\[quote\])!isu'        => '!(\[/quote\])!isu',
        '!(\[code\])!isu'         => '!(\[/code\])!isu',
        '!(\[search\])!isu'       => '!(\[/search\])!isu',
    ];

    /** @var array BBCode tags to XML mapping. */
    protected static $bbcode2xml = [
        '!(\[b\]\n?)!isu'            => '<bbcode_b>',
        '!(\n?\[/b\])!isu'           => '</bbcode_b>',
        '!(\[i\]\n?)!isu'            => '<bbcode_i>',
        '!(\n?\[/i\])!isu'           => '</bbcode_i>',
        '!(\[u\]\n?)!isu'            => '<bbcode_u>',
        '!(\n?\[/u\])!isu'           => '</bbcode_u>',
        '!(\[s\]\n?)!isu'            => '<bbcode_s>',
        '!(\n?\[/s\])!isu'           => '</bbcode_s>',
        '!(\[sub\]\n?)!isu'          => '<bbcode_sub>',
        '!(\n?\[/sub\])!isu'         => '</bbcode_sub>',
        '!(\[sup\]\n?)!isu'          => '<bbcode_sup>',
        '!(\n?\[/sup\])!isu'         => '</bbcode_sup>',
        '!(\[color\=(.*?)\]\n?)!isu' => '<bbcode_color value="$2">',
        '!(\n?\[/color\])!isu'       => '</bbcode_color>',
        '!(\[size\=(.*?)\]\n?)!isu'  => '<bbcode_size value="$2">',
        '!(\n?\[/size\])!isu'        => '</bbcode_size>',
        '!(\[font\=(.*?)\]\n?)!isu'  => '<bbcode_font value="$2">',
        '!(\n?\[/font\])!isu'        => '</bbcode_font>',
        '!(\[align\=(.*?)\]\n?)!isu' => '<bbcode_align value="$2">',
        '!(\n?\[/align\])!isu'       => '</bbcode_align>',
        '!(\[h1\]\n?)!isu'           => '<bbcode_h1>',
        '!(\n?\[/h1\])!isu'          => '</bbcode_h1>',
        '!(\[h2\]\n?)!isu'           => '<bbcode_h2>',
        '!(\n?\[/h2\])!isu'          => '</bbcode_h2>',
        '!(\[h3\]\n?)!isu'           => '<bbcode_h3>',
        '!(\n?\[/h3\])!isu'          => '</bbcode_h3>',
        '!(\[h4\]\n?)!isu'           => '<bbcode_h4>',
        '!(\n?\[/h4\])!isu'          => '</bbcode_h4>',
        '!(\[h5\]\n?)!isu'           => '<bbcode_h5>',
        '!(\n?\[/h5\])!isu'          => '</bbcode_h5>',
        '!(\[h6\]\n?)!isu'           => '<bbcode_h6>',
        '!(\n?\[/h6\])!isu'          => '</bbcode_h6>',
        '!(\[list\]\n?)!isu'         => '<bbcode_list>',
        '!(\n?\[/list\])!isu'        => '</bbcode_list>',
        '!(\[ulist\]\n?)!isu'        => '<bbcode_ulist>',
        '!(\n?\[/ulist\])!isu'       => '</bbcode_ulist>',
        '!(\[li\]\n?)!isu'           => '<bbcode_li>',
        '!(\n?\[/li\])!isu'          => '</bbcode_li>',
        '!(\[url\]\n?)!isu'          => '<bbcode_url>',
        '!(\[url\=(.*?)\]\n?)!isu'   => '<bbcode_url value="$2">',
        '!(\n?\[/url\])!isu'         => '</bbcode_url>',
        '!(\[mail\]\n?)!isu'         => '<bbcode_mail>',
        '!(\[mail\=(.*?)\]\n?)!isu'  => '<bbcode_mail value="$2">',
        '!(\n?\[/mail\])!isu'        => '</bbcode_mail>',
        '!(\[quote\]\n?)!isu'        => '<bbcode_quote>',
        '!(\n?\[/quote\])!isu'       => '</bbcode_quote>',
        '!(\[code\]\n?)!isu'         => '<bbcode_code>',
        '!(\n?\[/code\])!isu'        => '</bbcode_code>',
        '!(\[search\]\n?)!isu'       => '<bbcode_search>',
        '!(\n?\[/search\])!isu'      => '</bbcode_search>',
    ];

    /**
     * {@inheritdoc}
     */
    public function bbcode(string $text, string $mode, string $search = null): string
    {
        // If search mode is on, strip the PCRE delimiter (we use '!' here) and special PCRE characters.
        if ($search !== null) {
            $search = preg_quote($search, '!');
        }

        $opening_tags = array_keys(self::$bbcode_tags);
        $closing_tags = array_values(self::$bbcode_tags);

        // Put zero byte before and after each BBCode tag, as a tags delimiter.
        $text = preg_replace($opening_tags, "\0\$1\0", $text);
        $text = preg_replace($closing_tags, "\0\$1\0", $text);

        // Split BBCode text into array via zero byte border, so each tag is a separated array item
        // as well as each text between or inside tags.
        $text = explode("\0", $text);

        // Stack for found opening BBCode tags.
        $stack = new \SplStack();

        // Evaluate each piece of BBCode text.
        foreach ($text as $i => &$str) {
            // Flag to determine whether the piece is a BBCode tag.
            $is_tag = false;

            // Check whether the piece is an opening BBCode tag.
            // If so, push it to the stack.
            foreach ($opening_tags as $j => $tag) {
                if ($is_tag = preg_match($tag, $str)) {
                    $stack->push($closing_tags[$j]);
                    break;
                }
            }

            // If still is not a tag, then it's definitely not an *opening* BBCode tag.
            if (!$is_tag) {
                // Check whether the piece is a closing BBCode tag.
                foreach ($closing_tags as $j => $tag) {

                    if ($is_tag = preg_match($tag, $str)) {
                        $is_closed = false;

                        // Close all previous tags, remained unclosed.
                        while (!$stack->isEmpty() && !$is_closed) {
                            $k = $stack->pop();

                            if ($k === $tag) {
                                $is_closed = true;
                            }
                            else {
                                // Add missing closing tag.
                                $close_tag = preg_replace('!(\!\(\\\\\[/(.*)\\\]\)\!isu)!isu', '[/$2]', $k);
                                $str       = $close_tag . $str;
                            }
                        }

                        // If still not closed, then corresponding opening tag was missed.
                        if (!$is_closed) {
                            // Remove current tag.
                            $str = mb_substr($text[$i], 0, mb_strlen($text[$i]) - mb_strlen($str));
                        }

                        break;
                    }
                }
            }

            // If still is not a tag, then it's definitely user's text between or inside tags.
            if (!$is_tag) {
                // If search mode is on, add "[search]" tag to all corresponding matches.
                if ($search !== null) {
                    $str = preg_replace("!({$search})!isu", '[search]$1[/search]', $str);
                }
            }
        }

        unset($str);

        // Close all tags, remained unclosed.
        while (!$stack->isEmpty()) {
            $k = $stack->pop();

            // Add missing closing tag.
            $close_tag = preg_replace('!(\!\(\\\\\[/(.*)\\\]\)\!isu)!isu', '[/$2]', $k);
            $text[]    = $close_tag;
        }

        // Merge the array into solid block of text.
        $text = implode(null, $text);

        // Encode existing HTML special characters.
        $text = htmlspecialchars($text, ENT_COMPAT);

        // Convert BBCode tags into XML ones.
        $text = preg_replace(array_keys(self::$bbcode2xml), array_values(self::$bbcode2xml), $text);
        $text = '<bbcode>' . $text . '</bbcode>';

        // Transform resulted XML into DOM document.
        $page = new \DOMDocument();
        $xslt = new \XSLTProcessor();

        $page->load(__DIR__ . '/' . BBCodeMode::get($mode));
        $xslt->importStylesheet($page);
        $page->loadXML($text);

        $dom = $xslt->transformToDoc($page);

        // Remove XML headers from DOM document.
        $root = $dom->getElementsByTagName('bbcode')->item(0);

        // Convert XML tree into string of text.
        $text = null;

        foreach ($root->childNodes as $node) {
            $text .= $dom->saveXML($node);
        }

        // Decode back all existing HTML special characters, encoded before.
        $text = htmlspecialchars_decode($text, ENT_COMPAT);

        return $text;
    }
}
