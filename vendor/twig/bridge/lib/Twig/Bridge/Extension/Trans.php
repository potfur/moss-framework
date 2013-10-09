<?php

class Twig_Bridge_Extension_Trans extends Twig_Extension
{
    private $translator;

    public function __construct(Twig_Bridge_Extension_TransInterface $translator = null)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            'trans' => new \Twig_Filter_Method($this, 'trans'),
            'transchoice' => new \Twig_Filter_Method($this, 'transchoice'),
        );
    }

    public function getTokenParsers()
    {
        return array(
            // {% trans "Symfony ain't that great!" %}
            // {% trans %}Symfony ain't that great!{% endtrans %}
            new Twig_Bridge_TokenParser_Trans(),

            // {% transchoice count %}
            //      '{0} There are no apples|{1} There is one apple|]1,19] There are %count% apples|[20,Inf] There are many apples'
            // {% endtranschoice %}
            new Twig_Bridge_TokenParser_TransChoice(),
        );
    }

    public function trans($message, array $arguments = array(), $locale = null)
    {
        if(!$this->translator) {
            return strtr($message, $arguments);
        }

        return $this->translator->trans($message, $arguments, $locale);
    }

    public function transchoice($message, $count, array $arguments = array(), $locale = null)
    {
        if(!$this->translator) {
            return strtr($message, $arguments);
        }

        return $this->translator->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $locale);
    }

    public function getName()
    {
        return 'translator';
    }
}
