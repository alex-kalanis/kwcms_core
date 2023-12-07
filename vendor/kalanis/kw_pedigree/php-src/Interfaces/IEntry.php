<?php

namespace kalanis\kw_pedigree\Interfaces;


use kalanis\kw_pedigree\PedigreeException;


/**
 * Interface IEntry
 * @package kalanis\kw_pedigree\Interfaces
 * Interface about pedigree entry and processed within them
 * Mainly for editing and search
 */
interface IEntry
{
    /**
     * Under which key is that PK, usually number or string
     * @return string
     */
    public function getIdKey(): string;

    /**
     * @param int $id
     * @throws PedigreeException
     * @return $this
     */
    public function setId(int $id): self;

    /**
     * PK of that record, return as string, might be a number
     * @throws PedigreeException
     * @return int
     */
    public function getId(): int;

    public function getNameKey(): string;

    /**
     * @param string $name
     * @throws PedigreeException
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @throws PedigreeException
     * @return string
     */
    public function getName(): string;

    public function getFamilyKey(): string;

    /**
     * @param string $family
     * @throws PedigreeException
     * @return $this
     */
    public function setFamily(string $family): self;

    /**
     * @throws PedigreeException
     * @return string
     */
    public function getFamily(): string;

    /**
     * Under which table key is that key string
     * @return string
     */
    public function getShortKey(): string;

    /**
     * @param string $key
     * @throws PedigreeException
     * @return $this
     */
    public function setShort(string $key): self;

    /**
     * Key string by which it will be accessed
     * @throws PedigreeException
     * @return string
     */
    public function getShort(): string;

    public function getBirthKey(): string;

    /**
     * @param string|null $birth
     * @throws PedigreeException
     * @return $this
     */
    public function setBirth(?string $birth): self;

    /**
     * @throws PedigreeException
     * @return string|null
     */
    public function getBirth(): ?string;

    public function getDeathKey(): string;

    /**
     * @param string|null $death
     * @throws PedigreeException
     * @return $this
     */
    public function setDeath(?string $death): self;

    /**
     * @throws PedigreeException
     * @return string|null
     */
    public function getDeath(): ?string;

    /**
     * @param int|null $fatherId
     * @throws PedigreeException
     * @return bool|null bool if changed, null if nothing happens
     */
    public function setFatherId(?int $fatherId): ?bool;

    /**
     * @throws PedigreeException
     * @return int
     */
    public function getFatherId(): ?int;

    /**
     * @param int|null $motherId
     * @throws PedigreeException
     * @return bool|null bool if changed, null if nothing happens
     */
    public function setMotherId(?int $motherId): ?bool;

    /**
     * @throws PedigreeException
     * @return int|null
     */
    public function getMotherId(): ?int;

    public function getSuccessesKey(): string;

    /**
     * @param string $successes
     * @throws PedigreeException
     * @return $this
     */
    public function setSuccesses(string $successes): self;

    /**
     * @throws PedigreeException
     * @return string
     */
    public function getSuccesses(): string;

    public function getSexKey(): string;

    /**
     * @param string $sex
     * @throws PedigreeException
     * @return $this
     */
    public function setSex(string $sex): self;

    /**
     * @throws PedigreeException
     * @return string
     */
    public function getSex(): string;

    public function getTextKey(): string;

    /**
     * @param string $text
     * @throws PedigreeException
     * @return $this
     */
    public function setText(string $text): self;

    /**
     * @throws PedigreeException
     * @return string
     */
    public function getText(): string;
}
