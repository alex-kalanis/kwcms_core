<?php

namespace KWCMS\modules\Krep\Libs\Shared;


use kalanis\kw_forums\Interfaces\ITargets;
use kalanis\kw_forums\Content\PostList;
use kalanis\kw_forums\Content\TopicList;
use KWCMS\modules\Krep\Libs\Config;


/**
 * Class Parser
 * @package KWCMS\modules\Krep\Libs\Discus
 * Parse page to data
 */
class Parser implements ITargets
{
    /** @var Config */
    protected $config = null;
    /** @var PageData */
    protected $pageData = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    public function process(string $response, bool $canPost, ?int $currentPost): PageData
    {
        $this->pageData = new PageData();
        $data = $this->parsePage($response);
        // sestav data do struktury

        $body = preg_replace('#<head[^>]*?>.*?</head>#si', '', $data); // remove whole head
        $this->parsePageHeader($data);

        if ((strpos($this->extractBlock($body, '', '', '#(<!--Param:)(.*?)(-->)#si'), "Messages")) && (static::IS_ALIVE == $canPost)) { // je to diskuze, ne vyctovnik ci najezd do archivu; kdyz je parametr v diskuzi jiny nez 1 a neni nastaven archiv
            // hledam prispevky
            $this->parsePosts($body);
        } else { //je to sprosty vycet temat
            $this->parseTopics($body);
            $this->pageData->showForm = static::FORM_SEND_NOBODY;
            $this->pageData->listingType = static::LISTING_THEMAS;
        }
        $this->pageData->canPost = $this->pageData->canPost && $canPost;
        $this->pageData->currentPost = $currentPost;
        return $this->pageData;
    }

    protected function parsePage(string $body): string
    {
        if (preg_match('#<form[^>]*?>(.*)?</form>#si', $body, $form)) {
            $body = preg_replace('#<form[^>]*?>.*?</form>#si', '', $body); // remove form
            $this->processForm($form[1]);
        }
        if (preg_match('#charset=([^"]*)"#si', $body, $codePage)) {
            $this->pageData->encodingRemote = $codePage[1];
        }
        /*    $body = preg_replace('#<head[^>]*?>.*?</head>#si', '', $body); // remove whole head*/
        $body = preg_replace('#<script[^>]*?>.*?</script>#si', '', $body); // Remove any scripts enclosed between <script />
        $body = preg_replace("#\s*(\bon\w+)=([\"\"])?(.*?)([\"\"])?([\s\>])#i", "$5", $body); // Remove javascript event handlers
        $body = preg_replace('#<noscript>(.*?)</noscript>#si', "$1", $body); //expose any html between <noscript />
        $body = preg_replace('#<!--attachment:(.*?)<!--/attachment-->#si', "", $body); // remove attachments
        /*    $body = preg_replace('#<style[^>]*?>.*?</style>#si', '', $body);*/
        $body = preg_replace('#<ul[^>]*?>.*?</ul>#si', '', $body); // try to remove menu
        $body = preg_replace('#<hr[^>]*?><table[^>]*?>.*?</table>#si', '', $body); // remove subsciption
        return $body;
    }

    protected function processForm(string $formMatch): void
    {
        if (preg_match('#class="jenreg"#si', $formMatch)) {
            $this->pageData->showForm = static::FORM_SEND_REGISTERED;
        }
    }

    protected function parsePageHeader(string $template): void
    {
        $found = $this->parseHeaderPart($template, '', '#(<!--Topic: )(.*?)(-->)#si');
        if (!empty($found[1])) {
            $this->pageData->discusNumber = $found[0];
            $this->pageData->discusDesc = $found[1];
        }

        $found = $this->parseHeaderPart($template, '', '#(<!--Me: )(.*?)(-->)#si');
        if (!empty($found[1])) {
            $this->pageData->topicNumber = $found[0];
            $this->pageData->topicDesc = $found[1];
        }

        $found = $this->parseHeaderPart($template, '', '#(<!--Level 1: )(.*?)(-->)#si');
        if (!empty($found[1])) {
            $this->pageData->levelNumber = $found[0];
            $this->pageData->levelDesc = $found[1];
        }

        $this->pageData->parentDiscusNumber = ($this->pageData->levelNumber > 0) ? (($this->pageData->topicNumber == $this->pageData->levelNumber) ? $this->pageData->discusNumber : $this->pageData->levelNumber) : $this->pageData->topicNumber;
        $this->pageData->currentDiscusNumber = ($this->pageData->topicNumber == $this->pageData->levelNumber) ? $this->pageData->levelNumber : $this->pageData->topicNumber;

        $this->pageData->title = $this->extractBlock($template, '', '', '#(<title>)(.*?)(</title>)#si');
        $this->pageData->announce = $this->extractBlock($template, '', '', '#(<!--Announcement-->)(.*?)(<!--/Announcement--)#si');

        // encodings
        $encodingTarget = $this->config->encoding;
        if ($encodingTarget != $this->pageData->encodingRemote) {
            $this->pageData->title = iconv($this->pageData->encodingRemote, $encodingTarget, $this->pageData->title);
            $this->pageData->announce = iconv($this->pageData->encodingRemote, $encodingTarget, $this->pageData->announce);
            $this->pageData->discusDesc = iconv($this->pageData->encodingRemote, $encodingTarget, $this->pageData->discusDesc);
            $this->pageData->topicDesc = iconv($this->pageData->encodingRemote, $encodingTarget, $this->pageData->topicDesc);
            $this->pageData->levelDesc = iconv($this->pageData->encodingRemote, $encodingTarget, $this->pageData->levelDesc);
        }

        if (
            (substr_count(strtolower($this->pageData->title), "archiv") > 0)
            || (substr_count(strtolower($this->pageData->discusDesc), "archiv") > 0)
            || (substr_count(strtolower($this->pageData->topicDesc), "archiv") > 0)
        ) { // jsem archivem
            $this->pageData->canPost = static::IS_ARCHIVED ;
            $this->pageData->showForm = static::FORM_SEND_NOBODY;
        }
    }

    /**
     * @param string $block
     * @param string $subst
     * @param string $pattern
     * @return string[]
     */
    protected function parseHeaderPart(string $block, string $subst, string $pattern): array
    {
        return explode("/", $this->extractBlock($block, '', $subst, $pattern));
    }

    protected function extractBlock(string $template, string $block_name, string $subst = '', string $pattern = ''): string
    {
        if ($pattern == '') {
            $pattern = "#(<!-- BEGIN $block_name -->)(.*?)(<!-- END $block_name -->)#s";
        }
        if (!preg_match($pattern, $template, $matches)) {
            return "";
        }
        str_replace($matches[1] . $matches[2] . $matches[3], $subst, $template);
        return $matches[2];
        /*
        extract_block(odkud, jmeno_bloku, podretezec, regular_vyraz)
        */
    }

    protected function parsePosts(string $body): void
    {
        $libPost = new PostList();
        $body = $this->getLargerBlock($body, '(<body)(.*?)(</body>)');
        $body = $this->getLargerBlock($body, '(<tbody id="dfprispevky">)(.*?)(</tbody>)');
        $end = '<!--/Post:';
        while (strpos($body, $end)) {
            $block = $this->getLargerBlock($body, '(<!--Post: )(.*?)(<!--/Post: )');
            $num = $this->getLargerBlock($block, '(="POST)(.*?)(">)');
//        $time = $this->getLargerBlock($block, '(<em>'."\n".')(.*?)('."\n".'</em>)');
            $time = $this->getLargerBlock($block, '(<!--Time: )(.*?)(-->)');
            $count = $this->getLargerBlock($block, '(<!--field:posts-->)(.*?)(<!--/field-->)');
            $name = $this->getLargerBlock($block, '(<!--name-->)(.*?)(<!--/name-->)');
            $text = $this->getLargerBlock($block, '(<!--Text-->)(.*?)(<!--/Text-->)');

            // encodings
            $encodingTarget = $this->config->encoding;
            if ($encodingTarget != $this->pageData->encodingRemote) {
                $name = iconv($this->pageData->encodingRemote, $encodingTarget, $name);
                $text = iconv($this->pageData->encodingRemote, $encodingTarget, $text);
            }

            $post = clone $libPost;
            $this->pageData->posts[] = $post->setData(intval($num), intval($time), intval($count), $name, $this->getSmileys($text));

            $tpe = strpos($body, $end) + strlen($end);
            $body = substr($body, $tpe);
        }
    }

    protected function parseTopics(string $body): void
    {
        $libTopic = new TopicList();
        $body = $this->getLargerBlock($body, '(<tbody class="dfdialogy">)(.*?)(</tbody>)');
        $end = '<!--/Top-->';
        while (strpos($body, $end)) {
            $asHeader = false;
            $topics = $this->getLargerBlock($body, '(<!--Top: )(.*?)(<!--/Top-->)');
            $tpdx = "catch_";
            $num = $this->getLargerBlock($tpdx . $topics, '(' . $tpdx . ')(.*?)(-->)');
            $name = $this->getLargerBlock($topics, '(<a href=")(.*?)(a>)');
            $name = $this->getLargerBlock($name, '(">)(.*?)(</)');
            $time = $this->getLargerBlock($topics, '(.html?)(.*?)(">');
            if ((false !== strpos($name, '<div>')) || (false !== strpos($name, '<em>'))) { // je to hlavicka
                preg_match('#name="[^"]+"><\/a>\s([^<]+)\s<#i', $topics, $matches);
                $name = $matches[1];
                $asHeader = true;
            }

            // encodings
            $encodingTarget = $this->config->encoding;
            if ($encodingTarget != $this->pageData->encodingRemote) {
                $name = iconv($this->pageData->encodingRemote, $encodingTarget, $name);
            }

            $topic = clone $libTopic;
            $this->pageData->topics[] = $topic->setData(intval($num), intval($time), $name, '', 0, $asHeader);
            $tpe = strpos($body, $end) + strlen($end);
            $body = substr($body, $tpe);
        }
    }

    /**
     * Nahrazeni znamych smajlu pomoci zameny
     * @param string $message
     * @return string
     */
    protected function getSmileys(string $message): string
    {
//        $message = strval(preg_replace("/<img src=\"[^\"]+\" alt=\"\[([^\"]+)\]\" title=\"[^\"]+\" \/>/si", "::$1::", $message));
//        $message = strval(preg_replace("/<img src=\"http\:\/\/www\.k-report\.net\/discus\/clipart\/([^\.]+)[^\"]+\" alt=\"([^\"]+)?\" border=0>/si", "::$1::", $message)); // wink
        // a podobne kolecko s obrazky...
//        $message = strval(preg_replace('#(<a href="JavaScript: otevri\(\')(.*)(\'\)"><img src="http://www.k-report.net/discus/nahledy/male/)(.{9,12})(" alt="" title="" class="nahled1" height="100"></a>)#si', '::Image:($2)::', $message));
//        $message = strval(preg_replace('#(<div class="dfn" style="background\: url\(\'/discus/obrazky-male/(.{1,3})/(.{1,3})/(.{2,8})\.jpg\'\) left top no-repeat; width: (.{1,4})px; height: (.{1,4})px" title="(.*)"><span><a href="/ukazobrazek.php?soubor=(.{2.8})\.jpg&styl=2" target="_blank" title="Otev&#x0159;&#x00ED;t ve standardn&#x00ED;m okn&#x011B;"></a></span></div>)#si', '::Image:($3)::', $message));
        $message = strval(preg_replace("/<div class=\"dfn\" style=\"[^-]+[^\/]+\/(.{1,3})\/(.{1,3})\/(.{2,8})\.[^\"]+\" title=\"([^\"]+)?\"><span><a href=\"[^\"]+\" target=\"_blank\" title=\"[^\"]+\"><\/a><\/span><\/div>/si", "::ShowImage:($1,$2,$3,$4)::", $message));
//        $message = strval(preg_replace('#(<a href="http://www.k-report.net/ukazobrazek.php?)(.*)(class="nahled2"></a>)#si', '', $message));
//        $message = strval(preg_replace('#(<img src="http://www.k-report.net/discus/messages/)(.{2,6})(/)(.{5,7})(\.)(.{3,4})(" alt="">)#si', '::smImg:($4)::', $message));
//        $message = strval(preg_replace('#(<img src="http://www.k-report.net/discus/messages/)(.{2,6})(/)(.{5,7})(\.)(.{3,4})(" alt=")(.{0,30})(">)#si', '::UsrImg:($8)::', $message));
        //  Likvidace zbytku
        $message = strval(preg_replace('/(<a href="JavaScript:)(.*?)(<\/a>)/si', '', $message));
//        $message = strval(preg_replace('/<(img|image)[^>]*?\>/si', '', $message)); // vyrazovani zbylych obrazku
        return $message;
    }

    /**
     * Kdyz ma problemy preg_*
     * @param string $i
     * @param string $r
     * @return string
     */
    protected function getLargerBlock(string $i, string $r): string
    {
        $casti = explode(")", $r);
        $casti[0] = substr($casti[0], 1);
        $casti[2] = substr($casti[2], 1);
        $zacatek = strpos($i, $casti[0]);
        $i = substr($i, $zacatek);
        $konec = strpos($i, $casti[2]);
        $o = substr($i, strlen($casti[0]), ($konec - strlen($casti[0])));
        return $o;
    }
}
