<?php declare(strict_types=1);

namespace PrepaidCard\Card;

use PrepaidCard\MoneyInterface;

class CardManager
{
    /** @var CardRepositoryInterface */
    private $repository;

    public function __construct(CardRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function persist(Card $card): Card
    {
        return $this->repository->persist($card);
    }

    public function get(string $cardId): Card
    {
        return $this->repository->get($cardId);
    }

    public function debit(Card $card, MoneyInterface $amount): Card
    {
        $card->debit($amount);

        return $this->repository->persist($card);
    }

    public function credit(Card $card, MoneyInterface $amount): Card
    {
        $card->credit($amount);

        return $this->repository->persist($card);
    }

    public function block(Card $card, MoneyInterface $amount): Card
    {
        $card->block($amount);

        return $this->repository->persist($card);
    }

    public function refund(Card $card, MoneyInterface $amount): Card
    {
        $card->refund($amount);

        return $this->repository->persist($card);
    }
}
