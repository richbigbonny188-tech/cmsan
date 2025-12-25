<?php


namespace Gambio\GX;


use SessionHandlerInterface;

class FakeSessionHandler implements SessionHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function destroy($session_id): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function open($save_path, $name): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function read($session_id)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function write($session_id, $session_data): bool
    {
        return true;
    }
}