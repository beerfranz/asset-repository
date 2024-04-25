<?php

namespace App\Tests\Api;

use Zenstruck\Messenger\Test\InteractsWithMessenger;

trait RogerTestMessageTrait {

    use InteractsWithMessenger;

    protected function assertQueueIsEmpty() {
        $this->transport('async')->queue()->assertEmpty();
        $this->transport('failed')->queue()->assertEmpty();
    }

    protected function assertQueueCount(int $count) {
        $this->transport('async')->queue()->assertCount($count);
    }

    protected function processQueue(?int $id = null) {
        try {
            if (null === $id)
                $this->transport('async')->process();
            else
                $this->transport('async')->process($id);

            $this->transport('async')->rejected()->assertEmpty();
        } catch (\Throwable $t) {
            fwrite(STDERR, print_r('Message is rejected', true));
            var_dump($this->getRejectedMessages());
            throw $t;
        }
    }

    protected function getMessages(?int $id = null) {
        if (null === $id)
            return $this->transport('async')->queue()->messages();
        else
            return $this->transport('async')->queue()->messages($id);
    }

    protected function getRejectedMessages() {
        return $this->transport('async')->rejected()->messages();
    }

    protected function getAckownledgedMessages() {
        return $this->transport('async')->acknowledged()->messages();
    }

    protected function getDispatchedMessages() {
        return $this->transport('async')->dispatched()->messages();
    }
}
