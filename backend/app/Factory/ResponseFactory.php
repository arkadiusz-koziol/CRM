<?php

declare(strict_types=1);

namespace App\Factory;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory as BaseResponseFactory;
use Illuminate\Routing\UrlGenerator;

class ResponseFactory extends BaseResponseFactory
{
    public function __construct(protected readonly UrlGenerator $generator, ViewFactory $view, Redirector $redirector)
    {
        parent::__construct($view, $redirector);
    }

    public function back(): RedirectResponse
    {
        return $this->redirectTo($this->generator->previous());
    }
}
