<?php declare(strict_types=1);

namespace PrepaidCard\Card;

use Predis\Client;
use PrepaidCard\Exception\EntityNotFoundException;
use PrepaidCard\Money;

class CardPredisRepository implements CardRepositoryInterface
{
    const IDENTIFIER = 'card:%s';

    /** @var Client */
    private $storage;

    public function __construct(Client $storage)
    {
        $this->storage = $storage;
    }

    public function get(string $cardId): Card
    {
        $data = $this->storage->hgetall(sprintf(self::IDENTIFIER, $cardId));

        if (empty($data)) {
            throw new EntityNotFoundException(sprintf('Card ID "%s" not found', $cardId));
        }

        return new Card(
            $data[CardRepositoryInterface::FIELD_OWNER],
            new Money($data[CardRepositoryInterface::FIELD_BALANCE]),
            new Money($data[CardRepositoryInterface::FIELF_BLOCKED]),
            $data[CardRepositoryInterface::FIELD_ID]
        );
    }

    public function persist(Card $card): Card
    {
        $this->storage->hmset(sprintf(self::IDENTIFIER, $card->getCardId()), $card->toArray());

        return $card;
    }
}
