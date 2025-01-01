<?php

namespace clearwebconcepts;

interface LoginRepositoryInterface
{
    public function verify_credentials($username, $password): bool;
    public function store_token_in_database($user_id, $token): bool;
    public function logoff(): void;
}