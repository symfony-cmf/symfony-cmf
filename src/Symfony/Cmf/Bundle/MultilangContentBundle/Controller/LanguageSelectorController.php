<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\Translation\LocaleChooser\LocaleChooserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Cmf\Component\Routing\RouteAwareInterface;

/**
 * A controller to render the language selector and to decide on default language
 *
 * This controller depends on phpcr-odm and does not work with other odms.
 */
class LanguageSelectorController
{
    protected $om;
    protected $templating;
    protected $router;
    /**
     * @var LocaleChooserInterface
     */
    protected $chooser;
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Doctrine\ODM\PHPCR\Translation\LocaleChooser\LocaleChooserInterface $chooser
     */
    public function __construct(ObjectManager $om, EngineInterface $templating, RouterInterface $router, LocaleChooserInterface $chooser)
    {
        $this->om = $om;
        $this->templating = $templating;
        $this->router = $router;
        $this->chooser = $chooser;
    }

    /**
     * Render all available languages
     *
     * @param string $id the id to the content document to build translations for
     * @param array $languageUrls optional to not generate routes: list of language code to array with url, fullname and completion
     */
    public function languagesAction($id, $languageUrls = false)
    {
        if ($languageUrls === false) {
            $languageUrls = array();
            $available = $this->chooser->getDefaultLocalesOrder();
            if (count($available) < 2) {
                //nothing to choose from, don't show language chooser
                return new Response();
            }

            $content = $this->om->find(null, $id);
            foreach ($available as $lang) {
                $languageUrls[$lang]['fullname'] = \Locale::getDisplayLanguage($lang, $lang);
                $languageUrls[$lang]['url'] = $this->router->generate('', array('_locale' => $lang, 'content' => $content));
                // TODO: check for availability of this url in this lang and add to the language info.
                // we could also provide a variant that walks up the tree to link only existing languages if no fallback is desired
            }
        }

        return $this->templating->renderResponse('SymfonyCmfMultilangContentBundle:LanguageSelector:languageselector.html.twig',
            array('languageUrls' => $languageUrls)
        );
    }

    /**
     * action for / to redirect to the best language based on the request language order
     */
    public function defaultLanguageAction(Request $request, $contentDocument)
    {
        if (! $contentDocument instanceof RouteAwareInterface) {
            throw new \Exception('The route passed to the language selection action must emulate content to have the correct route generated.');
        }

        // TODO: use lunetics/LocaleBundle https://github.com/symfony-cmf/cmf-sandbox/issues/54
        $defaultPreferredLangs = $this->chooser->getDefaultLocalesOrder();
        $bestLang = $request->getPreferredLanguage($defaultPreferredLangs);

        // we only care about the first 2 characters, even if the user's preference is de_CH.
        $bestLang = substr($bestLang, 0, 2);

        /*
         * Let the router generate the route for the requested language. The
         * route provides its children, which should be the urls for each locale
         * as content.
         */
        $routeParams = $request->query->all(); // do not lose eventual get parameters
        $routeParams['_locale'] = $bestLang; // and set the locale
        $routeParams['content'] = $contentDocument; // and the content for the router

        $url = $this->router->generate('', $routeParams, true);
        /* Note: I wanted to send a 300 "Multiple Choices" header along with a
         * Location header, but user agents may behave inconsistently in
         * response to this.
         *
         * For example Chrome was not redirecting unless the headers were
         * carefully tailored for it. (In particular, it doesn't like the
         * lowercase 'location' header that results from calling
         * $response->headers->set('Location', '...')
         */

        $response = new RedirectResponse($url, 301);
        $response->setVary('accept-language');
        return $response;
    }
}
