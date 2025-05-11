<?php

declare(strict_types=1);

namespace App\Service;

use GProDB\LandingPage\ElementName;
use GProDB\LandingPage\Elements\AbstractElement;
use GProDB\LandingPage\LandingPage;

/**
 * @method meta
 * @method hero
 * @method about
 * @method newsletter
 * @method contact
 */
class PageData
{
    public function __construct(
        private readonly LandingPage $data,
    ) {
    }

    public function __call(string $name, array $arguments): AbstractElement|null
    {
        $section = ElementName::tryFrom($name);
        return $this->data->getElement($section);
    }
}
