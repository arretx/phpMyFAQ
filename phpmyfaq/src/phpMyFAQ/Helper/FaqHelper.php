<?php

/**
 * Helper class for phpMyFAQ FAQs.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL wasn't distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ\Helper
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2010-2024 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2010-11-12
 */

namespace phpMyFAQ\Helper;

use Exception;
use ParsedownExtra;
use phpMyFAQ\Category;
use phpMyFAQ\Configuration;
use phpMyFAQ\Date;
use phpMyFAQ\Entity\FaqEntity;
use phpMyFAQ\Faq;
use phpMyFAQ\Helper;
use phpMyFAQ\Language\LanguageCodes;
use phpMyFAQ\Link;
use phpMyFAQ\Strings;
use phpMyFAQ\Translation;
use phpMyFAQ\Utils;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FaqHelper
 *
 * @package phpMyFAQ\Helper
 */
class FaqHelper extends Helper
{
    /**
     * Constructor.
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Rewrites the CSS class generated by TinyMCE for HighlightJS.
     */
    public function renderMarkupContent(string $answer): string
    {
        return str_replace('class="language-markup"', 'class="language-html"', $answer);
    }

    /**
     * Extends URL fragments (e.g. <a href="#foo">) with the full default URL.
     */
    public function rewriteUrlFragments(string $answer, string $currentUrl): string
    {
        return str_replace('href="#', 'href="' . $currentUrl . '#', $answer);
    }

    /**
     * Renders a "Send to friend" HTML snippet.
     */
    public function renderSendToFriend(string $url): string
    {
        if ($url === '' || $url === '0' || !$this->configuration->get('main.enableSendToFriend')) {
            return '';
        }

        return sprintf(
            '<i aria-hidden="true" class="bi bi-envelope"></i>' .
            '<a rel="nofollow" href="%s" class="text-decoration-none"> %s</a>',
            $url,
            Translation::get('msgSend2Friend')
        );
    }


    /**
     * Renders a select box with all translations of a FAQ.
     */
    public function renderChangeLanguageSelector(Faq $faq, int $categoryId): string
    {
        $html = '';
        $faqUrl = sprintf(
            '?action=faq&amp;cat=%d&amp;id=%d&amp;artlang=%%s',
            $categoryId,
            $faq->faqRecord['id']
        );

        $oLink = new Link($this->configuration->getDefaultUrl() . $faqUrl, $this->configuration);
        $oLink->itemTitle = $faq->faqRecord['title'];
        $availableLanguages = $this->configuration->getLanguage()->isLanguageAvailable($faq->faqRecord['id']);

        if ((is_countable($availableLanguages) ? count($availableLanguages) : 0) > 1) {
            $html = '<form method="post">';
            $html .= '<select name="language" onchange="top.location.href = this.options[this.selectedIndex].value;">';

            foreach ($availableLanguages as $availableLanguage) {
                $html .= sprintf('<option value="%s"', sprintf($oLink->toString(), $availableLanguage));
                $html .= ($faq->faqRecord['lang'] === $availableLanguage ? ' selected' : '');
                $html .= sprintf('>%s</option>', LanguageCodes::get($availableLanguage));
            }

            $html .= '</select></form>';
        }

        return $html;
    }

    /**
     * Renders a preview of the answer
     */
    public function renderAnswerPreview(string $answer, int $numWords): string
    {
        if ($this->configuration->get('main.enableMarkdownEditor')) {
            $parseDown = new ParsedownExtra();
            return Utils::chopString(strip_tags((string) $parseDown->text($answer)), $numWords);
        }
        return Utils::chopString(strip_tags($answer), $numWords);
    }

    /**
     * Creates an overview with all categories with their FAQs.
     *
     * @throws Exception
     */
    public function createOverview(Category $category, Faq $faq, string $language = ''): string
    {
        $output = '';

        // Initialize categories
        $category->transform(0);

        // Get all FAQs
        $faq->getAllRecords(FAQ_SORTING_TYPE_CATID_FAQID, ['lang' => $language]);
        $date = new Date($this->configuration);

        if ((is_countable($faq->faqRecords) ? count($faq->faqRecords) : 0) !== 0) {
            $lastCategory = 0;
            foreach ($faq->faqRecords as $data) {
                if (!is_null($data['category_id']) && $data['category_id'] !== $lastCategory) {
                    $output .= sprintf(
                        '<h3>%s</h3>',
                        $this->cleanUpContent($category->getPath($data['category_id'], ' &raquo; '))
                    );
                }

                $output .= sprintf('<h4>%s</h4>', Strings::htmlentities($data['title']));
                if (!empty($data['content'])) {
                    $output .= sprintf('<article>%s</article>', $this->cleanUpContent($data['content']));
                }

                $output .= sprintf(
                    '<p>%s: %s<br>%s',
                    Translation::get('msgAuthor'),
                    Strings::htmlentities($data['author']),
                    Translation::get('msgLastUpdateArticle') . $date->format($data['updated'])
                );
                $output .= '<hr>';

                $lastCategory = $data['category_id'];
            }
        }

        return $output;
    }

    /**
     * Creates a list of links with available languages to edit a FAQ
     * in the admin backend.
     */
    public function createFaqTranslationLinkList(int $faqId, int $categoryId, string $faqLang): string
    {
        $output = '';

        $availableLanguages = $this->configuration->getLanguage()->isLanguageAvailable($categoryId, 'faqcategories');
        foreach ($availableLanguages as $availableLanguage) {
            if ($availableLanguage !== $faqLang) {
                $output .= sprintf(
                    '<a class="dropdown-item" href="?action=editentry&id=%d&cat=%d&translateTo=%s">%s %s</a>',
                    $faqId,
                    $categoryId,
                    $availableLanguage,
                    'Translate to',
                    LanguageCodes::get($availableLanguage)
                );
            } else {
                $output .= '<a class="dropdown-item">n/a</a>';
            }
        }

        return $output;
    }

    /**
     * Returns the URL for a given FAQ Entity and category ID.
     */
    public function createFaqUrl(FaqEntity $faqEntity, int $categoryId): string
    {
        return sprintf(
            '%s?action=faq&cat=%d&id=%d&artlang=%s',
            $this->configuration->getDefaultUrl() . 'index.php',
            $categoryId,
            $faqEntity->getId(),
            $faqEntity->getLanguage()
        );
    }

    /**
     * Remove <script> tags, we don't need them
     */
    public function cleanUpContent(string $content): string
    {
        $contentLength = strlen($content);
        $htmlSanitizer = new HtmlSanitizer(
            (new HtmlSanitizerConfig())
                ->withMaxInputLength($contentLength + 1)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowStaticElements()
                ->allowRelativeMedias()
                ->forceHttpsUrls($this->configuration->get('security.useSslOnly'))
                ->allowElement('iframe', ['title', 'src', 'width', 'height', 'allow', 'allowfullscreen'])
                ->allowMediaSchemes(['https', 'http', 'mailto', 'data'])
                ->allowMediaHosts(
                    [
                        Request::createFromGlobals()->getHost(),
                        'www.youtube.com'
                    ]
                )
        );

        return $htmlSanitizer->sanitize($content);
    }
}
