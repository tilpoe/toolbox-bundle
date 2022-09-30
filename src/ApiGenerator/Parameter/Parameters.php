<?php

namespace Feierstoff\ToolboxBundle\ApiGenerator\Parameter;

use Symfony\Component\HttpFoundation\Request;

class Parameters {

    public function __construct(
        private readonly Request $request,
        private ?array $parameters = []
    ) {
        $payload = json_decode($this->request->getContent(), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            foreach ($payload as $key => $param) {
                $this->request->request->set($key, $param);
            }
        }
    }

    public function setAll(array $parameters): self {
        $this->parameters = $parameters;
        return $this;
    }

    public function getAll(): array {
        return $this->parameters;
    }

    public function set(string $key, mixed $value): self {
        $this->parameters[$key] = $value;
        return $this;
    }

    public function get(string $key, mixed $default = null): mixed {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    public function getFromRequest(string $key, mixed $default = null, string $type = ""): mixed {
        $value = match(true) {
            $this->request->request->has($key) => $this->request->get($key),
            $this->request->query->has($key) => $this->request->query->get($key),
            $this->request->files->has($key) => $this->request->files->get($key),
            array_key_exists($key, $this->parameters) => $this->parameters[$key],
            default => $default
        };

        return $value;
    }

    public function request(): Request {
        return $this->request;
    }

}