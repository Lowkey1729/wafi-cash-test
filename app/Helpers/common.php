<?php

if ( !function_exists('get_txRef') )
{
    /**
     * @param $service_type <string> <airtime | data | bank-transfer | cable | power | epins | wallet-topup | wallet-transfer | account-ref >
     * @return string
     */
    function get_txRef($service_type): string
    {
        return Str::tranxRef($service_type);
    }
}
