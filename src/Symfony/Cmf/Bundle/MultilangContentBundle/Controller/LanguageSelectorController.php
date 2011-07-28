<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * A controller to render the language selector and to decide on default language
 */
class LanguageSelectorController extends Controller
{
    /** the phpcr session */
    protected $session;
    protected $chooser;
    protected $routename;

    public function __construct(ContainerInterface $container, $session, $chooser, $routename)
    {
        $this->container = $container;
        $this->session = $session;
        $this->chooser = $chooser;
        $this->routename = $routename;
    }

    /**
     * Render all available languages
     *
     * @param string $url the url for the injected routename
     * @param array $languageUrls optional to not generate routes: list of language code to array with url, fullname and completion
     */
    public function languagesAction($url, $languageUrls = false)
    {
        if ($languageUrls === false) {
            $languageUrls = array();
            $available = $this->chooser->getDefaultLanguages();
            if (count($available) < 2) {
                //nothing to choose from, don't show language chooser
                return new Response();
            }
            foreach($available as $lang) {
                $languageUrls[$lang] = $this->chooser->getLanguageMeta($lang);
                $languageUrls[$lang]['url'] = $this->generateUrl($this->routename, array('_locale' => $lang, 'url' => $url));
                // TODO: check for availability of this url in this lang and add to the language info.
                // we could also provide a variant that walks up the tree to link only existing languages if no fallback is desired
            }
        }
        return $this->render('SymfonyCmfMultilangContentBundle:LanguageSelector:languageselector.html.twig',
            array('languageUrls' => $languageUrls)
        );
    }

    /**
     * action for / to redirect to the best language based on the request language order
     */
    public function defaultLanguageAction()
    {
        $defaultPreferredLangs = $this->get('symfony_cmf_multilang_content.chooser')->getPreferredLanguages();
        $bestLang = $this->get('request')->getPreferredLanguage($defaultPreferredLangs);
        // we only care about the first 2 characters, even if the user's preference is de_CH.
        $bestLang = substr($bestLang, 0, 2);

        /* Note: I wanted to send a 300 "Multiple Choices" header along with a
         * Location header, but user agents may behave inconsistently in
         * repsonse to this.
         *
         * For example Chrome was not redirecting unless the headers were
         * carefully tailored for it. (In particular, it doesn't like the
         * lowercase 'location' header that results from calling
         * $response->headers->set('Location', '...')
         */
        $response = $this->redirect($this->get('router')->generate($this->routename, array('_locale' => $bestLang, '/'), true), 301);
        $response->setVary('accept-language');
        return $response;
    }

}