<?php

namespace Sonysarkis\Skills\Http\Integrations\DummyJson\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetQuoteRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected int $id
    ) {}

    public function resolveEndpoint(): string
    {
        return "/quotes/{$this->id}";
    }
}
