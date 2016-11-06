<?php

namespace AGuardia\Validator;


class HashtagValidator
{
    public function validate($message)
    {
        if(preg_match('/^(\[)?([ ]*)(#[a-zA-Z0-9 ]+)([ ]*)(\])?/', $message) === 0)
            return false;

        return true;
    }
}
