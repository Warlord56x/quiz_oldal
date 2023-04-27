<?php
class Account {
    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private int $age;
    private int $overallScore;

    public function __construct(int $id = 0, string $email = "", string $firstName = "", string $lastName = "", int $age = 0, int $overallScore = 0) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->overallScore = $overallScore;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    public function getAge(): int {
        return $this->age;
    }

    public function setAge(int $age): void {
        $this->age = $age;
    }

    public function getOverallScore(): int {
        return $this->overallScore;
    }

    public function setOverallScore(int $overallScore): void {
        $this->overallScore = $overallScore;
    }
}