<?php
declare(strict_types=1);

namespace IdService\Dto;

class AuthResult
{
    private $uid;
    private $token;

    public function __construct(string $uid, string $token)
    {
        $this->uid = $uid;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}