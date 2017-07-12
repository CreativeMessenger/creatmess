<?php namespace Distilleries\Messenger\Proxies;

class NullMessengerProxy implements \Distilleries\Messenger\Contracts\MessengerProxyContract {
    public function receivedInput($inputName, $inputValue, $messengerUser, $messengerConfig)
    {
        return true;
    }
}