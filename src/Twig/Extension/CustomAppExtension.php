<?php

namespace App\Twig\Extension;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * Class CustomAppExtension
 * @package App\Twig\Extension
 */
class CustomAppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * CustomAppExtension constructor.
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    /**
     * @return string[]
     */
    public function getGlobals(): array
    {
        return [
            'locale' => $this->locale
        ];
    }

    /**
     * @param $number
     * @return string
     */
    public function priceFilter($number)
    {
        return '$'.number_format($number, 2, '.', ',');
    }

    /**
     * @return TwigTest[]
     */
    public function getTests()
    {
        return [
            new TwigTest('like', function ($obj) { return $obj instanceof LikeNotification;}
            )
        ];
    }
}