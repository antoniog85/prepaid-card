<?php declare(strict_types=1);

namespace PrepaidCard\Card;

interface CardRepositoryInterface
{
    public const FIELD_ID      = 'card-id';
    public const FIELD_OWNER   = 'owner-id';
    public const FIELD_BALANCE = 'balance';
    public const FIELF_BLOCKED = 'blocked';

    public function get(string $cardId): Card;

    public function persist(Card $card): Card;
}
