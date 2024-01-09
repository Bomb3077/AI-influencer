<?php

return new class('login', 'password', 'imapServer:imapPort', 'imapLogin', 'imapPassword') {

    private $login;
    private $password;
    private $imapServer;
    private $imapLogin;
    private $imapPassword;

    public function __construct(
        string $login,
        string $password,
        string $imapServer,
        string $imapLogin,
        string $imapPassword
    )
    {
        $this->login        = $login;
        $this->password     = $password;
        $this->imapServer   = $imapServer;
        $this->imapLogin    = $imapLogin;
        $this->imapPassword = $imapPassword;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getImapServer(): string
    {
        return $this->imapServer;
    }

    public function getImapLogin(): string
    {
        return $this->imapLogin;
    }

    public function getImapPassword(): string
    {
        return $this->imapPassword;
    }
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setImapServer(string $imapServer): void
    {
        $this->imapServer = $imapServer;
    }

    public function setImapLogin(string $imapLogin): void
    {
        $this->imapLogin = $imapLogin;
    }

    public function setImapPassword(string $imapPassword): void
    {
        $this->imapPassword = $imapPassword;
    }
};